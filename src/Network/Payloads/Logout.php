<?php

declare(strict_types=1);

namespace AvionBlock\VoiceCraft\Network\Payloads;

use AvionBlock\VoiceCraft\Network\Payloads\PacketType;
use AvionBlock\VoiceCraft\Network\Payloads\MCCommPacket;

class Logout extends MCCommPacket {
	public function __construct() {
		parent::__construct(PacketType::Logout);
	}
}
