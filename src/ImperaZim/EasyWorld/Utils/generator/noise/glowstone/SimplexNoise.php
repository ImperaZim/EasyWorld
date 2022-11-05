<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\noise\glowstone;

use pocketmine\utils\Random;
use ImperaZim\EasyWorld\Utils\generator\noise\glowstone\GradTap;

class SimplexNoise extends PerlinNoise{


	protected const SQRT_3 = 1.7320508075688772;
	protected const F2 = 0.5 * (self::SQRT_3 - 1);
	protected const G2 = (3 - self::SQRT_3) / 6;
	protected const G22 = self::G2 * 2.0 - 1;
	protected const F3 = 1.0 / 3.0;
	protected const G3 = 1.0 / 6.0;
	protected const G32 = self::G3 * 2.0;
	protected const G33 = self::G3 * 3.0 - 1.0;

	private static array $grad_3;

	protected array $perm_mod_12 = [];

	public static function init() : void{
		self::$grad_3 = [
			new GradTap(1, 1, 0), new GradTap(-1, 1, 0), new GradTap(1, -1, 0),
			new GradTap(-1, -1, 0),
			new GradTap(1, 0, 1), new GradTap(-1, 0, 1), new GradTap(1, 0, -1), new GradTap(-1, 0, -1),
			new GradTap(0, 1, 1), new GradTap(0, -1, 1), new GradTap(0, 1, -1), new GradTap(0, -1, -1)
		];
	}

	public function __construct(Random $rand){
		parent::__construct($rand);
		for($i = 0; $i < 512; ++$i){
			$this->perm_mod_12[$i] = $this->perm[$i] % 12;
		}
	}

	public static function floor(float $x) : int{
		return $x > 0 ? (int) $x : (int) $x - 1;
	}

	protected static function dot(Grad $g, float $x, float $y, float $z = 0.0) : float{
		return $g->x * $x + $g->y * $y + $g->z * $z;
	}

	protected function get2dNoise(array &$noise, float $x, float $z, int $size_x, int $size_y, float $scale_x, float $scale_y, float $amplitude) : array{
		$index = -1;
		for($i = 0; $i < $size_y; ++$i){
			$zin = $this->offset_y + ($z + $i) * $scale_y;
			for($j = 0; $j < $size_x; ++$j){
				$xin = $this->offset_x + ($x + $j) * $scale_x;
				$noise[++$index] += $this->simplex2D($xin, $zin) * $amplitude;
			}
		}
		return $noise;
	}

	protected function get3dNoise(array &$noise, float $x, float $y, float $z, int $size_x, int $size_y, int $sizeZ, float $scale_x, float $scale_y, float $scale_z, float $amplitude) : array{
		$index = -1;
		for($i = 0; $i < $sizeZ; ++$i){
			$zin = $this->offset_z + ($z + $i) * $scale_z;
			for($j = 0; $j < $size_x; ++$j){
				$xin = $this->offset_x + ($x + $j) * $scale_x;
				for($k = 0; $k < $size_y; ++$k){
					$yin = $this->offset_y + ($y + $k) * $scale_y;
					$noise[++$index] += $this->simplex3D($xin, $yin, $zin) * $amplitude;
				}
			}
		}
		return $noise;
	}

	public function noise3d(float $xin, float $yin = 0.0, float $zin = 0.0) : float{
		if($yin === 0.0){
			return parent::noise3d($xin, $yin, $zin);
		}

		$xin += $this->offset_x;
		$yin += $this->offset_y;
		if($xin === 0.0){
			return $this->simplex2D($xin, $yin);
		}

		$zin += $this->offset_z;
		return $this->simplex3D($xin, $yin, $zin);
	}

	private function simplex2D(float $xin, float $yin) : float{
		$s = ($xin + $yin) * self::F2;
		$i = self::floor($xin + $s);
		$j = self::floor($yin + $s);
		$t = ($i + $j) * self::G2;
		$dx0 = $i - $t;
                $dy0 = $j - $t; 
		$x0 = $xin - $dx0; 
		$y0 = $yin - $dy0;

		$i1 = 0;
		$j1 = 0;
		if($x0 > $y0){
			$i1 = 1; 
			$j1 = 0;
		}else{
			$i1 = 0; 
			$j1 = 1;
		}

		$x1 = $x0 - $i1 + self::G2; 
		$y1 = $y0 - $j1 + self::G2;
		$x2 = $x0 + self::G22; 
		$y2 = $y0 + self::G22;

		$ii = $i & 255;
		$jj = $j & 255;
		$gi0 = $this->perm_mod_12[$ii + $this->perm[$jj]];
		$gi1 = $this->perm_mod_12[$ii + $i1 + $this->perm[$jj + $j1]];
		$gi2 = $this->perm_mod_12[$ii + 1 + $this->perm[$jj + 1]];

		$t0 = 0.5 - $x0 * $x0 - $y0 * $y0;
		$n0 = 0.0;
		if($t0 < 0){
			$n0 = 0.0;
		}else{
			$t0 *= $t0;
			$n0 = $t0 * $t0 * self::dot(self::$grad_3[$gi0], $x0, $y0); // (x,y) of grad_3 used for 2D gradient
		}

		$t1 = 0.5 - $x1 * $x1 - $y1 * $y1;
		$n1 = 0.0;
		if($t1 < 0){
			$n1 = 0.0;
		}else{
			$t1 *= $t1;
			$n1 = $t1 * $t1 * self::dot(self::$grad_3[$gi1], $x1, $y1);
		}

		$t2 = 0.5 - $x2 * $x2 - $y2 * $y2;
		$n2 = 0;
		if($t2 < 0){
			$n2 = 0.0;
		}else{
			$t2 *= $t2;
			$n2 = $t2 * $t2 * self::dot(self::$grad_3[$gi2], $x2, $y2);
		}

		return 70.0 * ($n0 + $n1 + $n2);
	}

	private function simplex3D(float $xin, float $yin, float $zin) : float{
		$s = ($xin + $yin + $zin) * self::F3;
		$i = self::floor($xin + $s);
		$j = self::floor($yin + $s);
		$k = self::floor($zin + $s);
		$t = ($i + $j + $k) * self::G3;
		$dx0 = $i - $t; 
		$dy0 = $j - $t;
		$dz0 = $k - $t;

		$i1 = 0; 
		$j1 = 0;
		$k1 = 0;
		$i2 = 0; 
                $j2 = 0;
		$k2 = 0;

		$x0 = $xin - $dx0; 
		$y0 = $yin - $dy0;
		$z0 = $zin - $dz0;
		if($x0 >= $y0){
			if($y0 >= $z0){
				$i1 = 1;
				$j1 = 0;
				$k1 = 0;
				$i2 = 1;
				$j2 = 1;
				$k2 = 0;
			}elseif($x0 >= $z0){
				$i1 = 1;
				$j1 = 0;
				$k1 = 0;
				$i2 = 1;
				$j2 = 0;
				$k2 = 1;
			}else{
				$i1 = 0;
				$j1 = 0;
				$k1 = 1;
				$i2 = 1;
				$j2 = 0;
				$k2 = 1;
			}
		}else{ // x0<y0
			if($y0 < $z0){
				$i1 = 0;
				$j1 = 0;
				$k1 = 1;
				$i2 = 0;
				$j2 = 1;
				$k2 = 1;
			}elseif($x0 < $z0){
				$i1 = 0;
				$j1 = 1;
				$k1 = 0;
				$i2 = 0;
				$j2 = 1;
				$k2 = 1;
			}else{
				$i1 = 0;
				$j1 = 1;
				$k1 = 0;
				$i2 = 1;
				$j2 = 1;
				$k2 = 0;
			}
		}

		$x1 = $x0 - $i1 + self::G3; 
		$y1 = $y0 - $j1 + self::G3;
		$z1 = $z0 - $k1 + self::G3;
		$x2 = $x0 - $i2 + self::G32; 
		$y2 = $y0 - $j2 + self::G32;
		$z2 = $z0 - $k2 + self::G32;

		$ii = $i & 255;
		$jj = $j & 255;
		$kk = $k & 255;
		$gi0 = $this->perm_mod_12[$ii + $this->perm[$jj + $this->perm[$kk]]];
		$gi1 = $this->perm_mod_12[$ii + $i1 + $this->perm[$jj + $j1 + $this->perm[$kk + $k1]]];
		$gi2 = $this->perm_mod_12[$ii + $i2 + $this->perm[$jj + $j2 + $this->perm[$kk + $k2]]];
		$gi3 = $this->perm_mod_12[$ii + 1 + $this->perm[$jj + 1 + $this->perm[$kk + 1]]];

		$t0 = 0.5 - $x0 * $x0 - $y0 * $y0 - $z0 * $z0;
		$n0 = 0.0; 
		if($t0 < 0){
			$n0 = 0.0;
		}else{
			$t0 *= $t0;
			$n0 = $t0 * $t0 * self::dot(self::$grad_3[$gi0], $x0, $y0, $z0);
		}

		$t1 = 0.5 - $x1 * $x1 - $y1 * $y1 - $z1 * $z1;
		$n1 = 0.0;
		if($t1 < 0){
			$n1 = 0.0;
		}else{
			$t1 *= $t1;
			$n1 = $t1 * $t1 * self::dot(self::$grad_3[$gi1], $x1, $y1, $z1);
		}

		$t2 = 0.5 - $x2 * $x2 - $y2 * $y2 - $z2 * $z2;
		$n2 = 0.0;
		if($t2 < 0){
			$n2 = 0.0;
		}else{
			$t2 *= $t2;
			$n2 = $t2 * $t2 * self::dot(self::$grad_3[$gi2], $x2, $y2, $z2);
		}

		$x3 = $x0 + self::G33; 
		$y3 = $y0 + self::G33;
		$z3 = $z0 + self::G33;
		$t3 = 0.5 - $x3 * $x3 - $y3 * $y3 - $z3 * $z3;
		$n3 = 0.0;
		if($t3 < 0){
			$n3 = 0.0;
		}else{
			$t3 *= $t3;
			$n3 = $t3 * $t3 * self::dot(self::$grad_3[$gi3], $x3, $y3, $z3);
		}

		return 32.0 * ($n0 + $n1 + $n2 + $n3);
	}
}
