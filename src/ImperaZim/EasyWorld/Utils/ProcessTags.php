<?php

namespace ImperaZim\EasyWorld\Utils;

use ImperaZim\EasyWorld\Loader;

class ProcessTags {
 
 public static function get($tags, $processed, $message) {
  return str_replace($tags, $processed, $message);
 } 
 
 public function getPlugin() {
  return Loader::getInstance();
 }  
 
}
