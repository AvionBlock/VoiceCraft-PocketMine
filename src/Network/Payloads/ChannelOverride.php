<?php

declare(strict_types=1);

namespace AvionBlock\VoiceCraft\Network\Payloads;

class ChannelOverride {
	public int $ProximityDistance = 30;
	public bool $ProximityToggle = true;
	public bool $VoiceEffects = true;
}
