<?php

declare(strict_types=1);

namespace AvionBlock\VoiceCraft\Network\Payloads;

use AvionBlock\VoiceCraft\Network\Payloads\PacketType;
use AvionBlock\VoiceCraft\Network\Payloads\MCCommPacket;

class SetChannelSettings extends MCCommPacket {
	public int $ChannelId = 0;
	public int $ProximityDistance = 30;
	public bool $ProximityToggle = true;
	public bool $VoiceEffects = true;
	public bool $ClearSettings = true;
	public function __construct() {
		parent::__construct(PacketType::SetChannelSettings);
	}
}
