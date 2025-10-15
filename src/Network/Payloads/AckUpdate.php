<?php

declare(strict_types=1);

namespace AvionBlock\VoiceCraft\Network\Payloads;

use AvionBlock\VoiceCraft\Network\Payloads\PacketType;
use AvionBlock\VoiceCraft\Network\Payloads\MCCommPacket;

class AckUpdate extends MCCommPacket {
	/** @var string[] */
	public array $SpeakingPlayers = [];
	public function __construct() {
		parent::__construct(PacketType::AckUpdate);
	}
}
