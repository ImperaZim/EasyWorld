<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\overworld\decorator;

use ImperaZim\EasyWorld\Utils\generator\Decorator;
use ImperaZim\EasyWorld\Utils\generator\object\BlockPatch;
use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;

class UnderwaterDecorator extends Decorator{

	private Block $type;

	private int $horiz_radius;

	private int $vert_radius;

	private array $overridables;

	public function __construct(Block $type){
		$this->type = $type;
	}

	final public function setRadii(int $horiz_radius, int $vert_radius) : UnderwaterDecorator{
		$this->horiz_radius = $horiz_radius;
		$this->vert_radius = $vert_radius;
		return $this;
	}

	final public function setOverridableBlocks(Block ...$overridables) : UnderwaterDecorator{
		foreach($overridables as $overridable){
			$this->overridables[] = $overridable->getFullId();
		}
		return $this;
	}

	public function decorate(ChunkManager $world, Random $random, int $chunk_x, int $chunk_z, Chunk $chunk) : void{
		$source_x = ($chunk_x << 4) + $random->nextBoundedInt(16);
		$source_z = ($chunk_z << 4) + $random->nextBoundedInt(16);
		$source_y = $chunk->getHighestBlockAt($source_x & 0x0f, $source_z & 0x0f) - 1;
		while(
			$source_y > 1 &&
			(
				($block_id = $world->getBlockAt($source_x, $source_y - 1, $source_z)->getId()) === BlockLegacyIds::STILL_WATER ||
				$block_id === BlockLegacyIds::FLOWING_WATER
			)
		){
			--$source_y;
		}
		$material = $world->getBlockAt($source_x, $source_y, $source_z)->getId();
		if($material === BlockLegacyIds::STILL_WATER || $material === BlockLegacyIds::FLOWING_WATER){
			(new BlockPatch($this->type, $this->horiz_radius, $this->vert_radius, ...$this->overridables))->generate($world, $random, $source_x, $source_y, $source_z);
		}
	}
}
