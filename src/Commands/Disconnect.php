<?php

namespace AvionBlock\VoiceCraft\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use AvionBlock\VoiceCraft\MainClass;

class Disconnect extends Command implements PluginOwned {
	public function __construct(public MainClass $plugin) {
		parent::__construct("disconnect", "Disconnects from the voicecraft server.", "/disconnect");
		$this->setPermission("voicecraft.command.disconnect");
	}

	public function execute(CommandSender $Sender, string $CommandLabel, array $Args): bool {
		$Sender->sendMessage(TextFormat::YELLOW . "Disconnecting from Server...");
		if (!$this->plugin->Network->IsConnected) {
			$Sender->sendMessage(TextFormat::RED . "Already disconnected from server.");
			return false;
		}

		try {
			$this->plugin->Network->Disconnect("Disconnection Request.");
		} catch (\Exception $e) {
			$Sender->sendMessage(TextFormat::RED . $e->getMessage());
		}
		return true;
	}

	public function getOwningPlugin(): MainClass {
		return $this->plugin;
	}
}