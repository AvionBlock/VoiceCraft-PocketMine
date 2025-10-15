<?php

declare(strict_types=1);

namespace AvionBlock\VoiceCraft\Network\Payloads;

use AvionBlock\VoiceCraft\Network\Payloads\PacketType;
use AvionBlock\VoiceCraft\Network\Payloads\MCCommPacket;

class SetParticipantBitmask extends MCCommPacket {
	public string $PlayerId = "";
	public bool $IgnoreDataBitmask = false;
	public int $Bitmask = 0;
	public function __construct() {
		parent::__construct(PacketType::SetParticipantBitmask);
	}
}
