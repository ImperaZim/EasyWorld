<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\biomegrid;

class ErosionMapLayer extends MapLayer{

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
				$upper_left_val = $values[$j + $i * $grid_size_x];
				$lower_left_val = $values[$j + ($i + 2) * $grid_size_x];
				$upper_right_val = $values[$j + 2 + $i * $grid_size_x];
				$lower_right_val = $values[$j + 2 + ($i + 2) * $grid_size_x];
				$center_val = $values[$j + 1 + ($i + 1) * $grid_size_x];

				$this->setCoordsSeed($x + $j, $z + $i);
				if($center_val !== 0 && ($upper_left_val === 0 || $upper_right_val === 0 || $lower_left_val === 0 || $lower_right_val === 0)){
					$final_values[$j + $i * $size_x] = $this->nextInt(5) === 0 ? 0 : $center_val;
				}elseif($center_val === 0 && ($upper_left_val !== 0 || $upper_right_val !== 0
						|| $lower_left_val !== 0 || $lower_right_val !== 0)){
					if($this->nextInt(3) === 0){
						$final_values[$j + $i * $size_x] = $upper_left_val;
					}else{
						$final_values[$j + $i * $size_x] = 0;
					}
				}else{
					$final_values[$j + $i * $size_x] = $center_val;
				}
			}
		}

		return $final_values;
	}
}
