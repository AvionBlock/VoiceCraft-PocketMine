<?php

declare(strict_types=1);

namespace AvionBlock\VoiceCraft\Network;

use pocketmine\block\BlockTypeIds;
use pocketmine\utils\TextFormat;
use pocketmine\math\AxisAlignedBB;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\TaskHandler;
use pocketmine\player\Player;
use AvionBlock\VoiceCraft\MainClass;
use AvionBlock\VoiceCraft\Network\Network;
use AvionBlock\VoiceCraft\Network\Payloads\PacketType;
use AvionBlock\VoiceCraft\Network\Payloads\Update;
use AvionBlock\VoiceCraft\Network\Payloads\VoiceCraftPlayer;

class NetworkRunner extends Task {
	private ?TaskHandler $UpdateLoop = null;
	private int $ReconnectRetries = 0;
	/** @var int[] */
	private static array $CaveBlocks = array(
		BlockTypeIds::STONE,
		BlockTypeIds::DIORITE,
		BlockTypeIds::GRANITE,
		BlockTypeIds::DEEPSLATE,
		BlockTypeIds::TUFF,
	);

	public function __construct(private MainClass $Plugin, private Network $Network) {
	}

	/**
	 * @description Starts the update looper.
	 */
	public function Start(): void {
		$this->UpdateLoop = $this->Plugin->getScheduler()->scheduleRepeatingTask($this, 20);
	}

	/**
	 * @description Stops the update looper (does not set the Network.IsConnected field to false).
	 */
	public function Stop(): void {
		if ($this->UpdateLoop != null) {
			$this->UpdateLoop->cancel();
			$this->UpdateLoop = null;
		}
	}

	/**
	 * @description Get's the cave density for a player based on the CaveBlocks list.
	 * @param Player $player
	 * @returns float
	 */
	public function GetCaveDensity(Player $player): float {
		if (!$this->Network->IsConnected) return 0.0;

		$world = $player->getWorld();
		$headLocation = $player->getLocation();
		$headLocation->y += $player->getEyeHeight();

		try {
			$total = 0.0;

			// up
			$b = $world->getCollisionBlocks(new AxisAlignedBB($headLocation->x, $headLocation->y, $headLocation->z, $headLocation->x, $headLocation->y + 50, $headLocation->z), true);
			if (count($b) > 0 && in_array($b[0]->getTypeId(), self::$CaveBlocks, true)) $total += 1.0;

			// left
			$b = $world->getCollisionBlocks(new AxisAlignedBB($headLocation->x, $headLocation->y, $headLocation->z, $headLocation->x - 20, $headLocation->y, $headLocation->z), true);
			if (count($b) > 0 && in_array($b[0]->getTypeId(), self::$CaveBlocks, true)) $total += 1.0;

			// right
			$b = $world->getCollisionBlocks(new AxisAlignedBB($headLocation->x, $headLocation->y, $headLocation->z, $headLocation->x + 20, $headLocation->y, $headLocation->z), true);
			if (count($b) > 0 && in_array($b[0]->getTypeId(), self::$CaveBlocks, true)) $total += 1.0;

			// forward
			$b = $world->getCollisionBlocks(new AxisAlignedBB($headLocation->x, $headLocation->y, $headLocation->z, $headLocation->x, $headLocation->y, $headLocation->z - 20), true);
			if (count($b) > 0 && in_array($b[0]->getTypeId(), self::$CaveBlocks, true)) $total += 1.0;

			// backward
			$b = $world->getCollisionBlocks(new AxisAlignedBB($headLocation->x, $headLocation->y, $headLocation->z, $headLocation->x, $headLocation->y, $headLocation->z + 20), true);
			if (count($b) > 0 && in_array($b[0]->getTypeId(), self::$CaveBlocks, true)) $total += 1.0;

			// down
			$b = $world->getCollisionBlocks(new AxisAlignedBB($headLocation->x, $headLocation->y, $headLocation->z, $headLocation->x, $headLocation->y - 50, $headLocation->z), true);
			if (count($b) > 0 && in_array($b[0]->getTypeId(), self::$CaveBlocks, true)) $total += 1.0;

			return $total / 6;
		} catch (\Exception $ex) {
			return 0.0;
		}
	}

	/**
	 * @description Sends an update to the VoiceCraft server.
	 */
	public function onRun(): void {
		if ($this->Network->IsConnected) {
			try {
				$playerList = [];
				foreach ($this->Plugin->getServer()->getOnlinePlayers() as $plr) {
					$player = new VoiceCraftPlayer();
					$player->PlayerId = $plr->getUniqueId()->toString();
					$player->DimensionId = $plr->getWorld()->getId();
					$player->Location = $plr->getLocation()->asVector3();
					$player->Location->y += $plr->getEyeHeight();
					$player->Rotation = $plr->getLocation()->yaw;
					$player->EchoFactor = $this->GetCaveDensity($plr);
					$player->Muffled = $plr->isUnderwater();
					$player->IsDead = $plr->isAlive();
					$playerList[] = $player;
				}

				//Build the packet.
				$packet = new Update();
				$packet->Players = $playerList;
				$packet->Token = $this->Network->Token;

				$response = $this->Network->SendPacket($packet);
				// it keeps saying "Negated boolean expression is always false."
				if (!($this->Network->IsConnected)) return; // @phpstan-ignore-line

				if ($response->PacketId == PacketType::AckUpdate) {
					/** @type {AckUpdate} */
					$packetData = $response;
					//You can do stuff with the AckUpdate packet data here...
					return;
				} else if ($response->PacketId == PacketType::Deny) {
					/** @type {Deny} */
					$packetData = $response;
					$this->Network->IsConnected = false;
					// http.cancelAll($packetData->Reason);
					$this->Stop();
				}
			} catch (\Exception $ex) {
				if (!$this->Network->IsConnected) return; //do nothing.
			}
		} else if ($this->UpdateLoop != null) {
			$this->Stop();
		}
	}

	public function Reconnect(): void {
		if ($this->ReconnectRetries < 5) {
			$this->ReconnectRetries++;

			$this->Plugin->getLogger()->info(TextFormat::YELLOW . "Reconnecting to server... Attempt: {$this->ReconnectRetries}");

			try {
				$this->Network->Connect(
					$this->Network->IPAddress,
					$this->Network->Port,
					$this->Network->Key
				);
				$this->Plugin->getLogger()->info("Successfully reconnected to VOIP server.");

/*
				if ($this->Plugin->getConfig()->getNested("config.broadcastVoipDisconnection"))
					$this->Plugin->getServer()->broadcastMessage(TextFormat::GREEN . "Successfully reconnected to VOIP server.");
*/
			} catch (\Exception $e) {
				if ($this->ReconnectRetries < 5) {
					$this->Plugin->getLogger()->info(TextFormat::YELLOW . "Connection failed, Retrying...");
					$this->Reconnect();
					return;
				}
				$this->Plugin->getLogger()->info(TextFormat::RED . "Failed to reconnect to VOIP server.");

/*
				if ($this->Plugin->getConfig()->getNested("config.broadcastVoipDisconnection"))
					$this->Plugin->getServer()->broadcastMessage("§cFailed to reconnect to VOIP server...");
*/
			}
		}
	}
}
