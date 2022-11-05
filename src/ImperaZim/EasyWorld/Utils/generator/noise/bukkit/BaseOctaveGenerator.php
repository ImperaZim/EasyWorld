<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\noise\bukkit;

abstract class BaseOctaveGenerator{

	protected array $octaves;

	protected float $x_scale = 1.0;
	protected float $y_scale = 1.0;
	protected float $z_scale = 1.0;

	protected function __construct(array $octaves){
		$this->octaves = $octaves;
	}

	public function setScale(float $scale) : void{
		$this->setXScale($scale);
		$this->setYScale($scale);
		$this->setZScale($scale);
	}

	public function getXScale() : float{
		return $this->x_scale;
	}

	public function setXScale(float $scale) : void{
		$this->x_scale = $scale;
	}

	public function getYScale() : float{
		return $this->y_scale;
	}

	public function setYScale(float $scale) : void{
		$this->y_scale = $scale;
	}

	public function getZScale() : float{
		return $this->z_scale;
	}

	public function setZScale(float $scale) : void{
		$this->z_scale = $scale;
	}

	public function getOctaves() : array{
		$octaves = [];
		foreach($this->octaves as $key => $value){
			$octaves[$key] = clone $value;
		}

		return $octaves;
	}
}
