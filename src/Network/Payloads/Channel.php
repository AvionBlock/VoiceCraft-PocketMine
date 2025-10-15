<?php

declare(strict_types=1);

namespace AvionBlock\VoiceCraft\Network\Payloads;

use AvionBlock\VoiceCraft\Network\Payloads\ChannelOverride;

class Channel {
	public string $Name = "";
	public string $Password = "";
	public bool $Locked = false;
	public bool $Hidden = false;
	public ChannelOverride|null $OverrideSettings = null;
}
