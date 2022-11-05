<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\overworld\populator\biome;

use ImperaZim\EasyWorld\Utils\generator\overworld\biome\BiomeIds;

class DesertPopulator extends BiomePopulator{

	protected function initPopulators() : void{
		$this->water_lake_decorator->setAmount(0);
		$this->dead_bush_decorator->setAmount(2);
		$this->sugar_cane_decorator->setAmount(60);
		$this->cactus_decorator->setAmount(10);
	}

	public function getBiomes() : ?array{
		return [BiomeIds::DESERT, BiomeIds::DESERT_HILLS];
	}
}