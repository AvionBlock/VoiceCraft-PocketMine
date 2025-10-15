<?php

namespace AvionBlock\VoiceCraft\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use AvionBlock\VoiceCraft\MainClass;

class AutoConnect extends Command implements PluginOwned {
	public function __construct(public MainClass $plugin) {
		parent::__construct("autoconnect", "Takes the settings from the autoconnect settings and attempts connection.", "/autoconnect");
		$this->setPermission("voicecraft.command.autoconnect");
	}

	public function execute(CommandSender $Sender, string $CommandLabel, array $Args): bool {
		$Sender->sendMessage(TextFormat::YELLOW . "Connecting/Linking Server...");
		try {
			$this->plugin->Network->AutoConnect();
			$Sender->sendMessage(TextFormat::GREEN . "Login Accepted. Server successfully linked!");
		} catch (\Exception $e) {
			$Sender->sendMessage(TextFormat::RED . $e->getMessage());
		}
		return true;
	}

	public function getOwningPlugin(): MainClass {
		return $this->plugin;
	}
}