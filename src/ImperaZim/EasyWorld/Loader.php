<?php

namespace ImperaZim\EasyWorld;

use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\world\generator\GeneratorManager;
use ImperaZim\EasyWorld\Events\WorldCombatEvent;
use ImperaZim\EasyWorld\Events\WorldReloadEvent;
use ImperaZim\EasyWorld\Events\WorldNoChatEvent;
use ImperaZim\EasyWorld\Events\WorldProtectEvent;
use ImperaZim\EasyWorld\Events\WorldNoCommandEvent;
use ImperaZim\EasyWorld\Commands\EasyWorldCommands;
use ImperaZim\EasyWorld\Utils\generator\void\VoidGenerator;
use ImperaZim\EasyWorld\Utils\generator\ender\EnderGenerator;
use ImperaZim\EasyWorld\Utils\generator\nether\NetherGenerator;
use ImperaZim\EasyWorld\Utils\generator\skyblock\SkyBlockGenerator;
use ImperaZim\EasyWorld\Utils\generator\overworld\OverworldGenerator;

class Loader extends PluginBase {

 public static Loader $instance;

 public static function getInstance() : Loader {
  return self::$instance;
 }

 public function onLoad() : void {
  self::$instance = $this;
  $generators = [
   "ender" => EnderGenerator::class,
   "void" => VoidGenerator::class,
   "skyblock" => SkyBlockGenerator::class,
   "nether_mw" => NetherGenerator::class,
   "normal_mw" => OverworldGenerator::class
  ]; 
  foreach ($generators as $name => $class) {
   GeneratorManager::getInstance()->addGenerator($class, $name, fn() => null, true);
  } 
  WorldReloadEvent::execute();
  self::getInstance()->getConfig()->get("plugin.prefix");
 }

 public function onEnable() : void {
  $events = [
   WorldNoChatEvent::class,
   WorldReloadEvent::class,
   WorldCombatEvent::class,
   WorldProtectEvent::class,
   WorldNoCommandEvent::class
  ];
  $commands = [
   "EasyWorld" => new EasyWorldCommands()
  ];
  foreach ($events as $event) {
   Server::getInstance()->getPluginManager()->registerEvents(new $event(), $this);
  }
  foreach ($commands as $command) {
   Server::getInstance()->getCommandMap()->register("EasyWorld", $command);
  }
 }
 
 public function ProcessTags($tags, $processed, $message) {
  return str_replace($tags, $processed, $message);
 }  

}
