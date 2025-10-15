<?php

declare(strict_types=1);

namespace AvionBlock\VoiceCraft\Network\Payloads;

use pocketmine\math\Vector3;

class VoiceCraftPlayer {
	public string $PlayerId = "";
	public int $DimensionId = -1;
	public Vector3 $Location;
	public float $Rotation = 0.0;
	public float $EchoFactor = 0.0;
	public bool $Muffled = false;
	public bool $IsDead = false;

	public function __construct() {
		$this->Location = new Vector3(0, 0, 0);
	}
}
