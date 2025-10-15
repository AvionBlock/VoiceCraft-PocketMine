<?php

declare(strict_types=1);

namespace AvionBlock\VoiceCraft\Network\Payloads;

use AvionBlock\VoiceCraft\Network\Payloads\PacketType;
use AvionBlock\VoiceCraft\Network\Payloads\MCCommPacket;
use AvionBlock\VoiceCraft\Network\Payloads\VoiceCraftPlayer;

class Update extends MCCommPacket {
	/** @var VoiceCraftPlayer[] */
	public array $Players = [];
	public function __construct() {
		parent::__construct(PacketType::Update);
	}
}
