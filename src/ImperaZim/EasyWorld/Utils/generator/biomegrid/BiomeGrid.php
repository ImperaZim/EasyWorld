<?php

namespace ImperaZim\EasyWorld\Utils\generator\biomegrid;

interface BiomeGrid{

	public function getBiome(int $x, int $z) : ?int;

	public function setBiome(int $x, int $z, int $biome_id) : void;
}
