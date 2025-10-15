<?php

declare(strict_types=1);

namespace AvionBlock\VoiceCraft\Network\Payloads;

use AvionBlock\VoiceCraft\Network\Payloads\PacketType;
use AvionBlock\VoiceCraft\Network\Payloads\MCCommPacket;

class Bind extends MCCommPacket {
	public string $PlayerId = "";
	public int $PlayerKey = 0;
	public string $Gamertag = "";
	public function __construct() {
		parent::__construct(PacketType::Bind);
	}
}
