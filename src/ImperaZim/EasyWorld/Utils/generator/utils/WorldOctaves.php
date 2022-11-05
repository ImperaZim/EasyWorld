<?php

namespace ImperaZim\EasyWorld\Utils\generator\utils;

use ImperaZim\EasyWorld\Utils\generator\noise\bukkit\OctaveGenerator;

class WorldOctaves{

	public OctaveGenerator $height;

	public OctaveGenerator $roughness;

	public OctaveGenerator $roughness_2;

	public OctaveGenerator $detail;

	public OctaveGenerator $surface;

	public function __construct(
		OctaveGenerator $height,
		OctaveGenerator $roughness,
		OctaveGenerator $roughness_2,
		OctaveGenerator $detail,
		OctaveGenerator $surface
	){
		$this->height = $height;
		$this->roughness = $roughness;
		$this->roughness_2 = $roughness_2;
		$this->detail = $detail;
		$this->surface = $surface;
	}
}
