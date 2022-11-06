<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\noise\bukkit;

use pocketmine\utils\Random;

class SimplexOctaveGenerator extends BaseOctaveGenerator{

	private static function createOctaves(Random $rand, int $octaves) : array{
		$result = [];

		for($i = 0; $i < $octaves; ++$i){
			$result[$i] = new SimplexNoiseGenerator($rand);
		}

		return $result;
	}

	private float $w_scale = 1.0;

	public function __construct(Random $rand, int $octaves){
		parent::__construct(self::createOctaves($rand, $octaves));
	}

	public function setScale(float $scale) : void{
		parent::setScale($scale);
		$this->setWScale($scale);
	}

	public function getWScale() : float{
		return $this->w_scale;
	}

	public function setWScale(float $scale) : void{
		$this->w_scale = $scale;
	}

	public function octaveNoise(float $x, float $y, float $z, float $frequency, float $amplitude, bool $normalized) : float{
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

	public function noise(float $x, float $y, float $z, float $w, float $frequency, float $amplitude, bool $normalized = false) : float{
		$result = 0.0;
		$amp = 1.0;
		$freq = 1.0;
		$max = 0.0;

		$x *= $this->x_scale;
		$y *= $this->y_scale;
		$z *= $this->z_scale;
		$w *= $this->w_scale;

		foreach($this->octaves as $octave){
			$result += $octave->noise($x * $freq, $y * $freq, $z * $freq, $w * $freq) * $amp;
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
