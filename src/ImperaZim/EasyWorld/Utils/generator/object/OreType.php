<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\object;

use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\utils\Random;

class OreType{

	private Block $type;
	private int $min_y;
	private int $max_y;
	private int $amount;
	private int $target_type;

	public function __construct(Block $type, int $min_y, int $max_y, int $amount, int $target_type = BlockLegacyIds::STONE){
		$this->type = $type;
		$this->min_y = $min_y;
		$this->max_y = $max_y;
		$this->amount = ++$amount;
		$this->target_type = $target_type;
	}

	public function getType() : Block{
		return $this->type;
	}

	public function getMinY() : int{
		return $this->min_y;
	}

	public function getMaxY() : int{
		return $this->max_y;
	}

	public function getAmount() : int{
		return $this->amount;
	}

	public function getTargetType() : int{
		return $this->target_type;
	}

	public function getRandomHeight(Random $random) : int{
		return $this->min_y === $this->max_y
			? $random->nextBoundedInt($this->min_y) + $random->nextBoundedInt($this->min_y)
			: $random->nextBoundedInt($this->max_y - $this->min_y) + $this->min_y;
	}
}
