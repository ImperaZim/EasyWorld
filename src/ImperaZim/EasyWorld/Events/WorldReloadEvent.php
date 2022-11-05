<?php

namespace ImperaZim\EasyWorld\Events;

use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use ImperaZim\EasyWorld\Loader;
use pocketmine\event\world\WorldInitEvent;

class WorldReloadEvent implements Listener {
 
 public static function execute() : void { self::reload(); }
  
 public function InitEvent(WorldInitEvent $event) { self::reload(); }
 
 public static function reload() : void {
  $plugin = self::getPlugin();
  $server = self::getServer();
  $worlds = scandir($server->getDataPath() . "worlds/");
  $config = new Config($plugin->getDataFolder() . "worlds.yml");
  foreach ($worlds as $world) {
   if ($world === "." || $world === "..") {
    continue;
   }
   if (!isset($config->getAll()[$world])) {
    $config = new Config($plugin->getDataFolder() . "worlds.yml", Config::YAML, [
     $world => [
      "name" => $world,
      "chat" => true,
      "combat" => true,
      "protected" => false,
      "commands_blocked" => [],
     ]
    ]);
    $config->save();
    $server->getLogger()->notice("{$world} adicionado na pasta");
   }
  } 
 } 
 
 public static function getPlugin() {
  return Loader::getInstance();
 } 
 
 public static function getServer() {
  return Server::getInstance();
 } 
 
}