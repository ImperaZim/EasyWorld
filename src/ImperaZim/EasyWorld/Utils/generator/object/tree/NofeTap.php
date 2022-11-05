<?php

namespace ImperaZim\EasyWorld\Utils\generator\object\tree;

class NofeTap{

	public $x;
	public $y;
	public $z;
	public $branch_y;

	public function __construct(int $x, int $y, int $z, int $branch_y){
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
		$this->branch_y = $branch_y;
	} 
	
} 
