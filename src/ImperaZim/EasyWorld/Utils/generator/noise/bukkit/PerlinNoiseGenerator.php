<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\noise\bukkit;

class PerlinNoiseGenerator extends BasePerlinNoiseGenerator{

	private static ?PerlinNoiseGenerator $instance;

	public static function getInstance() : PerlinNoiseGenerator{
		return self::$instance ??= new PerlinNoiseGenerator();
	}

	public static function getNoise3d(float $x, float $y = 0.0, float $z = 0.0) : float{
		return self::getInstance()->noise3d($x, $y, $z);
	}

	public static function getNoise(float $x, float $y, float $z, int $octaves, float $frequency, float $amplitude) : float{
		return self::getInstance()->noise($x, $y, $z, $octaves, $frequency, $amplitude);
	}

	public function noise3d(float $x, float $y = 0.0, float $z = 0.0) : float{
		$x += $this->offset_x;
		$y += $this->offset_y;
		$z += $this->offset_z;

		$floor_x = self::floor($x);
		$floor_y = self::floor($y);
		$floor_z = self::floor($z);

		$X = $floor_x & 255;
		$Y = $floor_y & 255;
		$Z = $floor_z & 255;

		$x -= $floor_x;
		$y -= $floor_y;
		$z -= $floor_z;

		$fX = self::fade($x);
		$fY = self::fade($y);
		$fZ = self::fade($z);

		$A = $this->perm[$X] + $Y;
		$AA = $this->perm[$A] + $Z;
		$AB = $this->perm[$A + 1] + $Z;
		$B = $this->perm[$X + 1] + $Y;
		$BA = $this->perm[$B] + $Z;
		$BB = $this->perm[$B + 1] + $Z;

		return self::lerp($fZ, self::lerp($fY, self::lerp($fX, self::grad($this->perm[$AA], $x, $y, $z),
			self::grad($this->perm[$BA], $x - 1, $y, $z)),
			self::lerp($fX, self::grad($this->perm[$AB], $x, $y - 1, $z),
				self::grad($this->perm[$BB], $x - 1, $y - 1, $z))),
			self::lerp($fY, self::lerp($fX, self::grad($this->perm[$AA + 1], $x, $y, $z - 1),
				self::grad($this->perm[$BA + 1], $x - 1, $y, $z - 1)),
				self::lerp($fX, self::grad($this->perm[$AB + 1], $x, $y - 1, $z - 1),
					self::grad($this->perm[$BB + 1], $x - 1, $y - 1, $z - 1))));
	}

	public function noise(float $x, float $y, float $z, int $octaves, float $frequency, float $amplitude, bool $normalized = false) : float{
		$result = 0.0;
		$amp = 1.0;
		$freq = 1.0;
		$max = 0.0;

		for($i = 0; $i < $octaves; ++$i){
			$result += $this->noise3d($x * $freq, $y * $freq, $z * $freq) * $amp;
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
