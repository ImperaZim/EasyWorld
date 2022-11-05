<?php

namespace ImperaZim\EasyWorld\Utils\generator\utils;

use ImperaZim\EasyWorld\Utils\generator\noise\bukkit\OctaveGenerator;

class NetherWorldOctaves extends WorldOctaves{

	public OctaveGenerator $soul_sand;

	public OctaveGenerator $gravel;

	public function __construct(
		OctaveGenerator $height,
		OctaveGenerator $roughness,
		OctaveGenerator $roughness_2,
		OctaveGenerator $detail,
		OctaveGenerator $surface,
		OctaveGenerator $soul_sand,
		OctaveGenerator $gravel
	){
		parent::__construct($height, $roughness, $roughness_2, $detail, $surface);
		$this->soul_sand = $soul_sand;
		$this->gravel = $gravel;
	}
}
