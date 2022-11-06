<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\noise\bukkit;

abstract class OctaveGenerator extends BaseOctaveGenerator{

	public function noise(float $x, float $y, float $z, float $frequency, float $amplitude, bool $normalized) : float{
		$result = 0.0;
		$amp = 1.0;
		$freq = 1.0;
		$max = 0.0;

		$x *= $this->x_scale;
		$y *= $this->y_scale;
		$z *= $this->z_scale;

		foreach($this->octaves as $octave){
			$result += $octave->noise3d($x * $freq, $y * $freq, $z * $freq) * $amp;
			$max += $amp;
			$freq *= $frequency;
			$amp *= $amplitude;
		}

		if($normalized){
			$result /= $max;
		}

		return $result;
	}
}
