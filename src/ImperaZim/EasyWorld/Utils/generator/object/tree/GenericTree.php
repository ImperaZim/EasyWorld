<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\object\tree;

use ImperaZim\EasyWorld\Utils\generator\object\TerrainObject;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\utils\TreeType;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\BlockTransaction;
use pocketmine\world\ChunkManager;
use pocketmine\world\World;
use function array_key_exists;

class GenericTree extends TerrainObject{

	protected BlockTransaction $transaction;
	protected int $height;
	protected Block $log_type;
	protected Block $leaves_type;
	protected array $overridables;

	public function __construct(Random $random, BlockTransaction $transaction){
		$this->transaction = $transaction;
		$this->setOverridables(
			BlockLegacyIds::AIR,
			BlockLegacyIds::LEAVES,
			BlockLegacyIds::GRASS,
			BlockLegacyIds::DIRT,
			BlockLegacyIds::LOG,
			BlockLegacyIds::LOG2,
			BlockLegacyIds::SAPLING,
			BlockLegacyIds::VINE
		);
		$this->setHeight($random->nextBoundedInt(3) + 4);
		$this->setType(TreeType::OAK());
	}

	final protected function setOverridables(int ...$overridables) : void{
		$this->overridables = array_flip($overridables);
	}

	final protected function setHeight(int $height) : void{
		$this->height = $height;
	}

	final protected function setType(TreeType $type) : void{
		$magic_number = $type->getMagicNumber();
		$block_factory = BlockFactory::getInstance();
		$this->log_type = $block_factory->get($magic_number >= 4 ? BlockLegacyIds::LOG2 : BlockLegacyIds::LOG, $magic_number & 0x3);
		$this->leaves_type = $block_factory->get($magic_number >= 4 ? BlockLegacyIds::LEAVES2 : BlockLegacyIds::LEAVES, $magic_number & 0x3);
	}

	public function canHeightFit(int $base_height) : bool{
		return $base_height >= 1 && $base_height + $this->height + 1 < World::Y_MAX;
	}

	public function canPlaceOn(Block $soil) : bool{
		$type = $soil->getId();
		return $type === BlockLegacyIds::GRASS || $type === BlockLegacyIds::DIRT || $type === BlockLegacyIds::FARMLAND;
	}

	public function canPlace(int $base_x, int $base_y, int $base_z, ChunkManager $world) : bool{
		for($y = $base_y; $y <= $base_y + 1 + $this->height; ++$y){
			$radius = 1; 
			if($y === $base_y){
				$radius = 0;
			}elseif($y >= $base_y + 1 + $this->height - 2){
				$radius = 2;
			}
			$height = $world->getMaxY();
			for($x = $base_x - $radius; $x <= $base_x + $radius; ++$x){
				for($z = $base_z - $radius; $z <= $base_z + $radius; ++$z){
					if($y >= 0 && $y < $height){
						if(!array_key_exists($world->getBlockAt($x, $y, $z)->getId(), $this->overridables)){
							return false;
						}
					}else{
						return false;
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

		for($y = $source_y + $this->height - 3; $y <= $source_y + $this->height; ++$y){
			$n = $y - ($source_y + $this->height);
			$radius = (int) (1 - $n / 2);
			for($x = $source_x - $radius; $x <= $source_x + $radius; ++$x){
				for($z = $source_z - $radius; $z <= $source_z + $radius; ++$z){
					if(abs($x - $source_x) !== $radius
						|| abs($z - $source_z) !== $radius
						|| ($random->nextBoolean() && $n !== 0)
					){
						$this->replaceIfAirOrLeaves($x, $y, $z, $this->leaves_type, $world);
					}
				}
			}
		}

		for($y = 0; $y < $this->height; ++$y){
			$this->replaceIfAirOrLeaves($source_x, $source_y + $y, $source_z, $this->log_type, $world);
		}

		$dirt = VanillaBlocks::DIRT();
		$this->transaction->addBlockAt($source_x, $source_y - 1, $source_z, $dirt);
		return true;
	}

	protected function cannotGenerateAt(int $base_x, int $base_y, int $base_z, ChunkManager $world) : bool{
		return !$this->canHeightFit($base_y)
			|| !$this->canPlaceOn($world->getBlockAt($base_x, $base_y - 1, $base_z))
			|| !$this->canPlace($base_x, $base_y, $base_z, $world);
	}

	protected function replaceIfAirOrLeaves(int $x, int $y, int $z, Block $new_material, ChunkManager $world) : void{
		$old_material = $world->getBlockAt($x, $y, $z)->getId();
		if($old_material === BlockLegacyIds::AIR || $old_material === BlockLegacyIds::LEAVES){
			$this->transaction->addBlockAt($x, $y, $z, $new_material);
		}
	}
}
