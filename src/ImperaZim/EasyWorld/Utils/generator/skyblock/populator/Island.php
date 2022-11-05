<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\skyblock\populator;

use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\object\OakTree;
use pocketmine\world\generator\populator\Populator;

class Island implements Populator {

	public function populate(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void {
		$center = new Vector3(256, 68, 256);

		for($x = -1; $x <= 1; $x++) {
			for($y = -1; $y <= 1; $y++) {
				for($z = -1; $z <= 1; $z++) {

					$centerVec = $center->add($x, $y, $z);
					if($centerVec->getY() == 69) {
						$world->setBlockAt($centerVec->getX(), $centerVec->getY(), $centerVec->getZ(), VanillaBlocks::GRASS());
					} else {
						$world->setBlockAt($centerVec->getX(), $centerVec->getY(), $centerVec->getZ(), VanillaBlocks::DIRT());
					}

					$leftVec = $center->add(3, 0, 0)->add($x, $y, $z);
					if($leftVec->getY() == 69) {
						$world->setBlockAt($leftVec->getX(), $leftVec->getY(), $leftVec->getZ(), VanillaBlocks::GRASS());
					} else {
						$world->setBlockAt($leftVec->getX(), $leftVec->getY(), $leftVec->getZ(), VanillaBlocks::DIRT());
					}

					$downVec = $center->subtract(0, 0, 3)->add($x, $y, $z);
					if($leftVec->getY() == 69) {
						$world->setBlockAt($downVec->getX(), $downVec->getY(), $downVec->getZ(), VanillaBlocks::GRASS());
					} else {
						$world->setBlockAt($downVec->getX(), $downVec->getY(), $downVec->getZ(), VanillaBlocks::DIRT());
					}
				}
			}
		}

		$chestVec = $center->add(0, 2, -4);
		
		$world->setBlockAt($chestVec->getX(), $chestVec->getY(), $chestVec->getZ(), VanillaBlocks::CHEST());

		$treeVec = $center->add(4, 2, 1);
		$tree = new OakTree;

		$tree->getBlockTransaction($world, $treeVec->getX(), $treeVec->getY(), $treeVec->getZ(), $random)->apply();
		
		$world->setBlockAt($center->getX(), $center->getY(), $center->getZ(), VanillaBlocks::BEDROCK());
	}
}
