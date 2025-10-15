<?php

declare(strict_types=1);

namespace AvionBlock\VoiceCraft\Network\Payloads;

use AvionBlock\VoiceCraft\Network\Payloads\PacketType;
use AvionBlock\VoiceCraft\Network\Payloads\MCCommPacket;

class Deny extends MCCommPacket {
	public string $Reason = "";
	public function __construct() {
		parent::__construct(PacketType::Deny);
	}
}
