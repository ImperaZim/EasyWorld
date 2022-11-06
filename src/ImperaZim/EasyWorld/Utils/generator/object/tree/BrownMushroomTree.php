<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\object\tree;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\utils\Random;
use pocketmine\world\BlockTransaction;
use pocketmine\world\ChunkManager;
use function array_key_exists;

class BrownMushroomTree extends GenericTree{

	protected int $type = BlockLegacyIds::BROWN_MUSHROOM_BLOCK;

	public function __construct(Random $random, BlockTransaction $transaction){
		parent::__construct($random, $transaction);
		$this->setOverridables(
			BlockLegacyIds::AIR,
			BlockLegacyIds::LEAVES,
			BlockLegacyIds::LEAVES2
		);
		$this->setHeight($random->nextBoundedInt(3) + 4);
	}

	public function canPlaceOn(Block $soil) : bool{
		$id = $soil->getId();
		return $id === BlockLegacyIds::GRASS || $id === BlockLegacyIds::DIRT || $id === BlockLegacyIds::MYCELIUM;
	}

	public function canPlace(int $base_x, int $base_y, int $base_z, ChunkManager $world) : bool{
		$world_height = $world->getMaxY();
		for($y = $base_y; $y <= $base_y + 1 + $this->height; ++$y){
			$radius = 3;
			if($y <= $base_y + 3){
				$radius = 0;
			}

			for($x = $base_x - $radius; $x <= $base_x + $radius; ++$x){
				for($z = $base_z - $radius; $z <= $base_z + $radius; ++$z){
					if($y < 0 || $y >= $world_height){ 
						return false;
					}
					if($y !== $base_y || $x !== $base_x || $z !== $base_z){
						if(!array_key_exists($world->getBlockAt($x, $y, $z)->getId(), $this->overridables)){
							return false;
						}
					}
				}
			}
		}
		return true;
	}

	public function generate(ChunkManager $world, Random $random, int $source_x, int $source_y, int $source_z) : bool{
		if($this->cannotGenerateAt($source_x, $source_y, $source_z, $world)){
			return false;
		}

		$block_factory = BlockFactory::getInstance();

		$stem = $block_factory->get($this->type, 10);
		for($y = 0; $y < $this->height; ++$y){
			$this->transaction->addBlockAt($source_x, $source_y + $y, $source_z, $stem);
		}

		$cap_y = $source_y + $this->height;
		if($this->type === BlockLegacyIds::RED_MUSHROOM_BLOCK){
			$cap_y = $source_y + $this->height - 3; 
		}

		for($y = $cap_y; $y <= $source_y + $this->height; ++$y){
			$radius = 1;
			if($y < $source_y + $this->height){
				$radius = 2;
			}
			if($this->type === BlockLegacyIds::BROWN_MUSHROOM_BLOCK){
				$radius = 3;
			}
			for($x = $source_x - $radius; $x <= $source_x + $radius; ++$x){
				for($z = $source_z - $radius; $z <= $source_z + $radius; ++$z){
					$data = 5;
					if($x === $source_x - $radius){
						$data = 4; 
					}elseif($x === $source_x + $radius){
						$data = 6; 
					}
					if($z === $source_z - $radius){
						$data -= 3;
					}elseif($z === $source_z + $radius){
						$data += 3;
					}

					if($this->type === BlockLegacyIds::BROWN_MUSHROOM_BLOCK || $y < $source_y + $this->height){

						if(($x === $source_x - $radius || $x === $source_x + $radius)
							&& ($z === $source_z - $radius || $z === $source_z + $radius)){
							continue;
						}

						if($x === $source_x - ($radius - 1) && $z === $source_z - $radius){
							$data = 1; 
						}elseif($x === $source_x - $radius && $z === $source_z - ($radius
								- 1)){
							$data = 1; 
						}elseif($x === $source_x + $radius - 1 && $z === $source_z - $radius){
							$data = 3; 
						}elseif($x === $source_x + $radius && $z === $source_z - ($radius - 1)){
							$data = 3;
						}elseif($x === $source_x - ($radius - 1) && $z === $source_z + $radius){
							$data = 7; 
						}elseif($x === $source_x - $radius && $z === $source_z + $radius - 1){
							$data = 7; 
						}elseif($x === $source_x + $radius - 1 && $z === $source_z + $radius){
							$data = 9; 
						}elseif($x === $source_x + $radius && $z === $source_z + $radius - 1){
							$data = 9; 
						}
					}

					if($data !== 5 || $y >= $source_y + $this->height){
						$this->transaction->addBlockAt($x, $y, $z, $block_factory->get($this->type, $data));
					}
				}
			}
		}

		return true;
	}
}
