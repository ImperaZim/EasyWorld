<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\object\tree;

use pocketmine\block\utils\TreeType;
use pocketmine\utils\Random;
use pocketmine\world\BlockTransaction;

class JungleTree extends GenericTree{

	public function __construct(Random $random, BlockTransaction $transaction){
		parent::__construct($random, $transaction);
		$this->setHeight($random->nextBoundedInt(7) + 4);
		$this->setType(TreeType::JUNGLE());
	}
}
