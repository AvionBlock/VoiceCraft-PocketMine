<?php

declare(strict_types=1);

namespace AvionBlock\VoiceCraft\Network\Payloads;

use AvionBlock\VoiceCraft\Network\Payloads\PacketType;
use AvionBlock\VoiceCraft\Network\Payloads\MCCommPacket;

class GetDefaultSettings extends MCCommPacket {
	public int $ProximityDistance = 30;
	public bool $ProximityToggle = true;
	public bool $VoiceEffects = true;
	public function __construct() {
		parent::__construct(PacketType::GetDefaultSettings);
	}
}
