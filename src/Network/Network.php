<?php

declare(strict_types=1);

namespace AvionBlock\VoiceCraft\Network;

use pocketmine\utils\TextFormat;
use pocketmine\player\Player;
use AvionBlock\VoiceCraft\MainClass;
use AvionBlock\VoiceCraft\Network\Payloads\PacketType;
use AvionBlock\VoiceCraft\Network\Payloads\MCCommPacket;
use AvionBlock\VoiceCraft\Network\Payloads\Login;
use AvionBlock\VoiceCraft\Network\Payloads\Logout;
use AvionBlock\VoiceCraft\Network\Payloads\Bind;
use AvionBlock\VoiceCraft\Network\Payloads\Deny;

/**
 * @description Checks if the string input is null or whitespace.
 * @param mixed $input
 * @returns bool
 */
function IsNullOrWhitespace(mixed $input): bool {
	return !is_string($input) || strlen(trim(strval($input))) == 0; // empty($input);
}

class Network {
	private static string $Version = "1.0.0";

	public string $IPAddress = "";
	public int $Port = 9050;
	public string $Key = "";
	public string $Token = "";
	public bool $IsConnected = false;

	public NetworkRunner $NetworkRunner;

	public function __construct(private MainClass $Plugin) {
		$this->NetworkRunner = new NetworkRunner($Plugin, $this);
	}

	public function SendPacket(MCCommPacket $data): MCCommPacket {
		$jsonData = json_encode($data);
		// $this->Logger->info("Request: " . $jsonData);

		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_POST => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_URL => "http://{$this->IPAddress}:{$this->Port}/",
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 5,
			CURLOPT_POSTFIELDS => $jsonData
		));
		$body = curl_exec($ch);
		if ($body == false) {
			throw new \Exception("Sending HTTP Packet Failed, Reason: " . curl_error($ch));
		}
		/** @var string $body */
		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		// $this->Logger->info("Response: " . $body);

		if ($statusCode == 200) {
			$response = MCCommPacket::fromJSON(json_decode($body, false));
			if ($response == null) throw new \Exception("Receiving HTTP Packet Failed, Reason: Invalid JSON");
			return $response;
		} else {
			throw new \Exception("Sending HTTP Packet Failed, Reason: HTTP_EXCEPTION, STATUS_CODE: {$statusCode}, body: {$body}");
		}
	}

	/**
	 * @description Connects to a VoiceCraft server specified by the IPAddress and Port.
	 * @param string $ipAddress
	 * @param int $port
	 * @param string $key
	 */
	public function Connect(string $ipAddress, int $port, string $key): void {
		if ($port < 0 || $port > 65535) throw new \Exception("Invalid Port!");

		$this->Disconnect("Reconnecting to another server.");
		$this->IPAddress = $ipAddress;
		$this->Port = $port;
		$this->Key = $key;

		$packet = new Login();
		$packet->LoginKey = $key;
		$packet->Version = self::$Version;

		$response = null;
		try {
			$response = $this->SendPacket($packet);
		} catch (\Exception $ex) {
			throw new \Exception("Could not contact server. Please check if your IPAddress and Port are correct! ERROR: {$ex->getMessage()}");
		}

		/** @var MCCommPacket $response */
		if ($response->PacketId == PacketType::Accept) {
			/** @type {Accept} */
			$packetData = $response;
			$this->IsConnected = true;
			$this->Token = $packetData->Token;
			$this->NetworkRunner->Start();
			return;
		} else {
			/** @var Deny $response */
			$packetData = $response;
			throw new \Exception("Login Denied. Server denied link request! Reason: {$packetData->Reason}");
		}
	}

	/**
	 * @description Connects to the VoiceCraft server using auto connect settings.
	 */
	public function AutoConnect(): void {
		$Config = $this->Plugin->getConfig();
		$IP = $Config->getNested("config.host", "");
		$Port = $Config->getNested("config.port", "");
		$ServerKey = $Config->getNested("config.server-key", "");

		if (
			(!is_string($IP) || IsNullOrWhitespace($IP)) ||
			(!is_string($ServerKey) || IsNullOrWhitespace($ServerKey)) ||
			!is_numeric($Port)
		) {
			throw new \Exception("Error: Cannot connect. AutoConnect settings may not be setup properly!");
		}

		$this->Connect("{$IP}", intval($Port), "{$ServerKey}");
	}

	/**
	 * @description Disconnects from a server with an optional reason.
	 * @param string $reason
	 */
	public function Disconnect(string $reason = "N.A."): void {
		if ($this->IsConnected) {
			$this->NetworkRunner->Stop();

			$packet = new Logout();
			$packet->Token = $this->Token;

			$this->SendPacket($packet);
			$this->IsConnected = false;
			// if ($this->Plugin->getConfig()->getNested("config.broadcastVoipDisconnection"))
				$this->Plugin->getServer()->broadcastMessage("§cDisconnected from VOIP Server, Reason: {$reason}");
		}
	}

	/**
	 * @description Binds a player to a VoiceCraft client.
	 * @param Player $player
	 * @param int $key
	 */
	public function Bind(Player $player, int $key): void {
		if (!$this->IsConnected) throw new \Exception("Could not bind, Server not connected/linked!");

		$packet = new Bind();
		$packet->Gamertag = $player->getName();
		$packet->PlayerKey = $key;
		$packet->PlayerId = $player->getUniqueId()->toString();
		$packet->Token = $this->Token;

		try {
			$response = $this->SendPacket($packet);
			if ($response->PacketId == PacketType::Accept) {
				return;
			} else {
				/** @var Deny $response */
				$packetData = $response;
				throw new \Exception("Binding Unsuccessful, Reason: {$packetData->Reason}");
			}
		} catch (\Exception $ex) {
			throw new \Exception("Binding Unsuccessful, ERROR: {$ex->getMessage()}");
		}
	}

}
