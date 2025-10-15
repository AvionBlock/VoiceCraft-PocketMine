<?php

namespace AvionBlock\VoiceCraft\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use AvionBlock\VoiceCraft\MainClass;

class Connect extends Command implements PluginOwned {
	public function __construct(public MainClass $plugin) {
		parent::__construct("connect", "Attempts connection to a voicecraft server.", "/connect <IP: string> <PORT: integer> <Key: string>");
		$this->setPermission("voicecraft.command.connect");
	}

	public function execute(CommandSender $Sender, string $CommandLabel, array $Args): bool {
		if (count($Args) < 3 || !is_numeric($Args[1])) {
			throw new InvalidCommandSyntaxException();
		}

		$Sender->sendMessage(TextFormat::YELLOW . "Connecting/Linking Server...");
		try {
			$this->plugin->Network->Connect($Args[0], intval($Args[1]), $Args[2]);
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