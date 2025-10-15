<?php

namespace AvionBlock\VoiceCraft\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use AvionBlock\VoiceCraft\MainClass;

class Bind extends Command implements PluginOwned {
	public function __construct(public MainClass $plugin){
		parent::__construct("bind", "Binds the client running the command to a client connected to the voicecraft server.", "/bind <Code: integer>");
		$this->setPermission("voicecraft.command.bind");
	}

	public function execute(CommandSender $Sender, string $CommandLabel, array $Args): bool{
		if (!$Sender instanceof Player) {
			$Sender->sendMessage("You must run this command in-game.");
			return false;
		}
		if (count($Args) < 1 || !is_numeric($Args[0])) {
			throw new InvalidCommandSyntaxException();
		}

		$Sender->sendMessage(TextFormat::YELLOW . "Binding...");
		try {
			$this->plugin->Network->Bind($Sender, intval($Args[0]));
			$Sender->sendMessage(TextFormat::GREEN . "Binding Successful!");

			// if ($this->plugin->getConfig()->get("sendBindedMessage"))
				$this->plugin->getServer()->broadcastMessage(
					TextFormat::AQUA . $Sender->getName() . TextFormat::DARK_GREEN . " has connected to VoiceCraft!"
				);
		} catch (\Exception $e) {
			$Sender->sendMessage(TextFormat::RED . $e->getMessage());
		}
		return true;
	}

	public function getOwningPlugin(): MainClass{
		return $this->plugin;
	}
}