<?php

namespace ImperaZim\EasyWorld\Utils\generator;

use ImperaZim\EasyWorld\Utils\generator\biomegrid\BiomeGrid;
use function array_key_exists;

class VanillaBiomeGrid implements BiomeGrid{

	public array $biomes = [];

	public function getBiome(int $x, int $z) : ?int{
		return array_key_exists($hash = $x | $z << 4, $this->biomes) ? $this->biomes[$hash] & 0xFF : null;
	}

	public function setBiome(int $x, int $z, int $biome_id) : void{
		$this->biomes[$x | $z << 4] = $biome_id;
	}
}
