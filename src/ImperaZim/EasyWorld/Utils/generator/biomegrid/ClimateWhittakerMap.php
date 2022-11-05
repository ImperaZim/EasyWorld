<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\biomegrid;

use ImperaZim\EasyWorld\Utils\generator\biomegrid\WhittakerMapLayer;

class ClimateWhittakerMap {

	public int $value;

	/** @var int[] */
	public array $cross_types;

	public int $final_value;

	/**
	 * @param int $value
	 * @param int[] $cross_types
	 * @param int $final_value
	 */
	public function __construct(int $value, array $cross_types, int $final_value){
	 $this->value = $value;
	 $this->cross_types = $cross_types;
	 $this->final_value = $final_value;
	}
}

WhittakerMapLayer::init();
