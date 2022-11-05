<?php

declare(strict_types=1);

namespace ImperaZim\EasyWorld\Utils\generator\biomegrid\utils;

final class BiomeEdgeEntry{

	public array $key;

	public ?array $value = null;

	public function __construct(array $mapping, ?array $value = null){
		$this->key = $mapping;
		if($value !== null){
			$this->value = [];
			foreach($value as $v){
				$this->value[$v] = $v;
			}
		}
	}
} 
 
