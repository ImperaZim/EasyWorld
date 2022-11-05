<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\biomegrid;

use ImperaZim\EasyWorld\Utils\generator\biomegrid\ClimateWhittakerMap;

class WhittakerMapLayer extends MapLayer {

	public const WARM_WET = 0;
	public const COLD_DRY = 1;
	public const LARGER_BIOMES = 2;

	private static array $MAP = [];

	public static function init() : void{
		self::$MAP[self::WARM_WET] = new ClimateWhittakerMap(2, [3, 1], 4);
		self::$MAP[self::COLD_DRY] = new ClimateWhittakerMap(3, [2, 4], 1);
	}

	private MapLayer $below_layer;
	private int $type;

	public function __construct(int $seed, MapLayer $below_layer, int $type){
		parent::__construct($seed);
		$this->below_layer = $below_layer;
		$this->type = $type;
	}

	public function generateValues(int $x, int $z, int $size_x, int $size_z) : array{
		if($this->type === self::WARM_WET || $this->type === self::COLD_DRY){
			return $this->swapValues($x, $z, $size_x, $size_z);
		}

		return $this->modifyValues($x, $z, $size_x, $size_z);
	}

	private function swapValues(int $x, int $z, int $size_x, int $size_z) : array{
		$grid_x = $x - 1;
		$grid_z = $z - 1;
		$grid_size_x = $size_x + 2;
		$grid_size_z = $size_z + 2;
		$values = $this->below_layer->generateValues($grid_x, $grid_z, $grid_size_x, $grid_size_z);

		$climate = self::$MAP[$this->type];
		$final_values = [];
		for($i = 0; $i < $size_z; ++$i){
			for($j = 0; $j < $size_x; ++$j){
				$center_val = $values[$j + 1 + ($i + 1) * $grid_size_x];
				if($center_val === $climate->value){
					$upper_val = $values[$j + 1 + $i * $grid_size_x];
					$lower_val = $values[$j + 1 + ($i + 2) * $grid_size_x];
					$left_val = $values[$j + ($i + 1) * $grid_size_x];
					$right_val = $values[$j + 2 + ($i + 1) * $grid_size_x];
					foreach($climate->cross_types as $type){
						if(($upper_val === $type) || ($lower_val === $type) || ($left_val === $type) || ($right_val === $type)){
							$center_val = $climate->final_value;
							break;
						}
					}
				}

				$final_values[$j + $i * $size_x] = $center_val;
			}
		}

		return $final_values;
	}

	/**
	 * @param int $x
	 * @param int $z
	 * @param int $size_x
	 * @param int $size_z
	 * @return int[]
	 */
	private function modifyValues(int $x, int $z, int $size_x, int $size_z) : array{
		$values = $this->below_layer->generateValues($x, $z, $size_x, $size_z);
		$final_values = [];
		for($i = 0; $i < $size_z; ++$i){
			for($j = 0; $j < $size_x; ++$j){
				$val = $values[$j + $i * $size_x];
				if($val !== 0){
					$this->setCoordsSeed($x + $j, $z + $i);
					if($this->nextInt(13) === 0){
						$val += 1000;
					}
				}

				$final_values[$j + $i * $size_x] = $val;
			}
		}
		return $final_values;
	}
}
