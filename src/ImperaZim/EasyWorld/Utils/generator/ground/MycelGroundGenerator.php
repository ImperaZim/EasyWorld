<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\ground;

use pocketmine\block\VanillaBlocks;

class MycelGroundGenerator extends GroundGenerator{

	public function __construct(){
		parent::__construct(VanillaBlocks::MYCELIUM());
	}
}