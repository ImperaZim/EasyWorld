<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\overworld\populator\biome;

use ImperaZim\EasyWorld\Utils\generator\object\tree\BigOakTree;
use ImperaZim\EasyWorld\Utils\generator\object\tree\CocoaTree;
use ImperaZim\EasyWorld\Utils\generator\overworld\biome\BiomeIds;
use ImperaZim\EasyWorld\Utils\generator\overworld\decorator\types\TreeDecoration;

class JungleEdgePopulator extends JunglePopulator{

	protected static array $TREES;

	protected static function initTrees() : void{
		self::$TREES = [
			new TreeDecoration(BigOakTree::class, 10),
			new TreeDecoration(CocoaTree::class, 45)
		];
	}

	protected function initPopulators() : void{
		$this->tree_decorator->setAmount(2);
		$this->tree_decorator->setTrees(...self::$TREES);
	}

	public function getBiomes() : ?array{
		return [BiomeIds::JUNGLE_EDGE, BiomeIds::JUNGLE_EDGE_MUTATED];
	}
}
JungleEdgePopulator::init();
