<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\skyblock;

use ImperaZim\EasyWorld\Utils\generator\skyblock\populator\Island;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;

class SkyBlockGenerator extends Generator {

	public function __construct(int $seed, string $preset) {
		parent::__construct($seed, $preset);
	}

	public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
	}

	public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
		if($chunkX == 16 && $chunkZ == 16) {
			$island = new Island();
			$island->populate($world, $chunkX, $chunkZ, $this->random);
		}
	}
}
