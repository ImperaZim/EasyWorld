<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\overworld\populator\biome\utils;

use ImperaZim\EasyWorld\Utils\generator\object\OreType;

final class OreTypeHolder{

	public OreType $type;

	public int $value;

	public function __construct(OreType $type, int $value){
		$this->type = $type;
		$this->value = $value;
	}
}
