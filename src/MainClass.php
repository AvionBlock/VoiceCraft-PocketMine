<?php

declare(strict_types=1);

namespace AvionBlock\VoiceCraft;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use AvionBlock\VoiceCraft\Commands;
use AvionBlock\VoiceCraft\Network\Network;
use AvionBlock\VoiceCraft\Network\Payloads;

class MainClass extends PluginBase{
	public Network $Network;

	public function onLoad(): void {
		$this->getLogger()->info(TextFormat::WHITE . "I've been loaded!");
	}

	public function onEnable(): void {
		$this->Network = new Network($this);

		$this->saveDefaultConfig();
		$Config = $this->getConfig()->get("config");

		$this->getServer()->getPluginManager()->registerEvents(new ExampleListener($this), $this);
		$this->getLogger()->info(TextFormat::DARK_GREEN . "I've been enabled!");

		$this->getServer()->getCommandMap()->registerAll("voicecraft", [
			new Commands\Connect($this),
			new Commands\Disconnect($this),
			// new Commands\Settings($this),
			new Commands\Bind($this),
			// new Commands\BindFake($this),
			// new Commands\UpdateFake($this),
			new Commands\AutoConnect($this),
			// new Commands\SetAutoBind($this),
			// new Commands\ClearAutoBind($this),
		]);

		//if ($Config["autoConnectOnStart"]) {
			$this->getLogger()->info("Auto connection enabled, Connecting to server...");
			try {
				$this->Network->AutoConnect();
				$this->getLogger()->info(TextFormat::GREEN . "Successfully auto connected to VOIP server.");
			} catch (\Exception $ex) {
				$this->getLogger()->info(TextFormat::RED . "Failed to auto connect to VOIP server.");
			}
		// }
	}

	public function onDisable(): void {
		$this->Network->NetworkRunner->Stop();
		$this->getLogger()->info(TextFormat::DARK_RED . "I've been disabled!");
	}
}
