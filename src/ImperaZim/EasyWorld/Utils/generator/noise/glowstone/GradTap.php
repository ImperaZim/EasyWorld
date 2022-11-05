<?php

namespace ImperaZim\EasyWorld\Utils\generator\noise\glowstone; 

use ImperaZim\EasyWorld\Utils\generator\noise\glowstone\SimplexNoise; 
class GradTap{

	public float $x;
	public float $y;
	public float $z;

	public function __construct(float $x, float $y, float $z){
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
	}
}

SimplexNoise::init(); 
