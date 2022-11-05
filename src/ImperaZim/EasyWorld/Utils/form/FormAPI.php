<?php

namespace ImperaZim\EasyWorld\Utils\form;

use pocketmine\plugin\PluginBase;
use ImperaZim\EasyWorld\Utils\form\types\ModalForm;
use ImperaZim\EasyWorld\Utils\form\types\SimpleForm;
use ImperaZim\EasyWorld\Utils\form\types\CustomForm;

class FormAPI {

 public static function createCustomForm(?callable $function = null) : CustomForm {
  return new CustomForm($function);
 }

 public static function createSimpleForm(?callable $function = null) : SimpleForm {
  return new SimpleForm($function);
 }

 public static function createModalForm(?callable $function = null) : ModalForm {
  return new ModalForm($function);
 }

}
