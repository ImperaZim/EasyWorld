<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\overworld\decorator;

use ImperaZim\EasyWorld\Utils\generator\Decorator;
use ImperaZim\EasyWorld\Utils\generator\object\Flower;
use ImperaZim\EasyWorld\Utils\generator\overworld\decorator\types\FlowerDecoration;
use pocketmine\block\Block;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;

class FlowerDecorator extends Decorator{

	private static function getRandomFlower(Random $random, array $decorations) : ?Block{
		$total_weight = 0;
		foreach($decorations as $decoration){
			$total_weight += $decoration->getWeight();
		}

		if($total_weight > 0){
			$weight = $random->nextBoundedInt($total_weight);
			foreach($decorations as $decoration){
				$weight -= $decoration->getWeight();
				if($weight < 0){
					return $decoration->getBlock();
				}
			}
		}

		return null;
	}

	private array $flowers = [];

	final public function setFlowers(FlowerDecoration ...$flowers) : void{
		$this->flowers = $flowers;
	}

	public function decorate(ChunkManager $world, Random $random, int $chunk_x, int $chunk_z, Chunk $chunk) : void{
		$x = $random->nextBoundedInt(16);
		$z = $random->nextBoundedInt(16);
		$source_y = $random->nextBoundedInt($chunk->getHighestBlockAt($x & 0x0f, $z & 0x0f) + 32);

		$flower = self::getRandomFlower($random, $this->flowers);
		if($flower !== null){
			(new Flower($flower))->generate($world, $random, ($chunk_x << 4) + $x, $source_y, ($chunk_z << 4) + $z);
		}
	}
}
