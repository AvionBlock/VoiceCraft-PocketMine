<?php

declare(strict_types=1);

namespace AvionBlock\VoiceCraft\Network\Payloads;

use AvionBlock\VoiceCraft\Network\Payloads\PacketType;

abstract class MCCommPacket {
	public PacketType $PacketId;
	public string $Token = "";

	/** @argument {PacketType|int} packetId */
	public function __construct(PacketType|int $packetId) {
		$this->PacketId = $packetId instanceof PacketType ? $packetId : PacketType::from($packetId);
	}

	public static function fromJSON(mixed $object): MCCommPacket|null {
		if (!($object instanceof \stdClass) || !isset($object->PacketId)) return null;

		$obj = null;
		/** @var MCCommPacket|null $obj */
		try {
			$packetType = PacketType::from($object->PacketId)->name;
			$class = __NAMESPACE__ . '\\' . $packetType;
			$obj = new $class();
		} catch (\Exception $ex) {
		}

		if ($obj != null) {
			foreach ($object as $key => $value) { // @phpstan-ignore-line
				if ($key == "PacketId") $value = PacketType::from($value);
				$obj->{$key} = $value; // @phpstan-ignore-line
			}
		}

		/** @var MCCommPacket|null $obj */
		return $obj;
	}
}
