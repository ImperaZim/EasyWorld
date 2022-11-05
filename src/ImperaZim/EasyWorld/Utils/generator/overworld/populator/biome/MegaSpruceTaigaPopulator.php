<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\overworld\populator\biome;

use ImperaZim\EasyWorld\Utils\generator\object\tree\MegaSpruceTree;
use ImperaZim\EasyWorld\Utils\generator\object\tree\RedwoodTree;
use ImperaZim\EasyWorld\Utils\generator\object\tree\TallRedwoodTree;
use ImperaZim\EasyWorld\Utils\generator\overworld\biome\BiomeIds;
use ImperaZim\EasyWorld\Utils\generator\overworld\decorator\types\TreeDecoration;

class MegaSpruceTaigaPopulator extends MegaTaigaPopulator{

	/** @var TreeDecoration[] */
	protected static array $TREES;

	protected static function initTrees() : void{
		self::$TREES = [
			new TreeDecoration(RedwoodTree::class, 44),
			new TreeDecoration(TallRedwoodTree::class, 22),
			new TreeDecoration(MegaSpruceTree::class, 33)
		];
	}

	public function getBiomes() : ?array{
		return [BiomeIds::REDWOOD_TAIGA_MUTATED, BiomeIds::REDWOOD_TAIGA_HILLS_MUTATED];
	}

	protected function initPopulators() : void{
		$this->tree_decorator->setTrees(...self::$TREES);
    }
}

MegaSpruceTaigaPopulator::init();