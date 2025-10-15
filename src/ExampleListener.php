<?php

declare(strict_types=1);

namespace AvionBlock\VoiceCraft;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerRespawnEvent;

class ExampleListener implements Listener{

	public function __construct(private MainClass $plugin){ }

	/**
	 * @param PlayerRespawnEvent $event
	 *
	 * @priority NORMAL
	 */
	public function onRespawn(PlayerRespawnEvent $event) : void{
		$this->plugin->getServer()->broadcastMessage($event->getPlayer()->getDisplayName() . " has just respawned!");
	}

	/**
	 * This runs after all other priorities. We mustn't cancel the event at MONITOR priority, we can only observe the
	 * result.
	 *
	 * @priority MONITOR
	 */
	public function handlerNamesCanBeAnything(PlayerChatEvent $event) : void{
		if(!$event->isCancelled()){
			$this->plugin->getLogger()->info("Player " . $event->getPlayer()->getName() . " sent a message: " . $event->getMessage());
		}else{
			$this->plugin->getLogger()->info("Player " . $event->getPlayer()->getName() . " tried to send a message, but it was cancelled: " . $event->getMessage());
		}
	}
}
