<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\overworld\populator\biome;

use ImperaZim\EasyWorld\Utils\generator\overworld\biome\BiomeIds;

class DesertMountainsPopulator extends DesertPopulator{

	protected function initPopulators() : void{
		$this->water_lake_decorator->setAmount(1);
	}

	public function getBiomes() : ?array{
		return [BiomeIds::DESERT_MUTATED];
	}
}