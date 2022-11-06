<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\biomegrid;

class SmoothMapLayer extends MapLayer{

	private MapLayer $below_layer;

	public function __construct(int $seed, MapLayer $below_layer){
		parent::__construct($seed);
		$this->below_layer = $below_layer;
	}

	public function generateValues(int $x, int $z, int $size_x, int $size_z) : array{
		$grid_x = $x - 1;
		$grid_z = $z - 1;
		$grid_size_x = $size_x + 2;
		$grid_size_z = $size_z + 2;
		$values = $this->below_layer->generateValues($grid_x, $grid_z, $grid_size_x, $grid_size_z);

		$final_values = [];
		for($i = 0; $i < $size_z; ++$i){
			for($j = 0; $j < $size_x; ++$j){
				$upper_val = $values[$j + 1 + $i * $grid_size_x];
				$lower_val = $values[$j + 1 + ($i + 2) * $grid_size_x];
				$left_val = $values[$j + ($i + 1) * $grid_size_x];
				$right_val = $values[$j + 2 + ($i + 1) * $grid_size_x];
				$center_val = $values[$j + 1 + ($i + 1) * $grid_size_x];
				if($upper_val === $lower_val && $left_val === $right_val){
					$this->setCoordsSeed($x + $j, $z + $i);
					$center_val = $this->nextInt(2) === 0 ? $upper_val : $left_val;
				}elseif($upper_val === $lower_val){
					$center_val = $upper_val;
				}elseif($left_val === $right_val){
					$center_val = $left_val;
				}

				$final_values[$j + $i * $size_x] = $center_val;
			}
		}

		return $final_values;
	}
}
