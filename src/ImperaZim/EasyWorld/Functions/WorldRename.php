<?php

namespace ImperaZim\EasyWorld\Functions;

use SplFileInfo;
use pocketmine\Server;
use FilesystemIterator;
use pocketmine\world\World;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use ImperaZim\EasyWorld\Loader;
use ImperaZim\EasyWorld\Events\WorldReloadEvent; 
use pocketmine\world\format\io\data\BaseNbtWorldData;

class WorldRename {

 public static function execute(Player $player, String $OldName, String $NewName) {
  $plugin = self::getPlugin();
  $server = self::getServer();
  $message = $plugin->getConfig(); 
  $config = new Config($plugin->getDataFolder() . "worlds.yml");
  if (!isset($config->getAll()[$oldName])) {
   $player->sendMessage($plugin->ProcessTags(
    ["{prefix}", "{world}"],
    [$message->get("plugin.prefix"), $oldName],
    $message->get("world.rename.non")));
   return true;
  }
  if (isset($config->getAll()[$newName])) {
   $player->sendMessage($plugin->ProcessTags(
    ["{prefix}", "{world}"],
    [$message->get("plugin.prefix"), $world],
    $message->get("world.rename.has")));
   return true;
  }
  $copy = $config->getAll()[$oldName];
  $array = new Config($plugin->getDataFolder() . "worlds.yml", Config::YAML, [
   "$newName" => [
    "name" => $copy["name"],
    "chat" => $copy["chat"],
    "combat" => $copy["combat"],
    "protected" => $copy["protected"],
    "commands_blocked" => $copy["commands_blocked"],
   ]
  ]);
  $array->save();
  unset($copy);
  self::lazyUnloadWorld($oldName);
  $from = $server->getDataPath() . "/worlds/" . $oldName;
  $to = $server->getDataPath() . "/worlds/" . $newName;
  rename($from, $to);
  self::lazyLoadWorld($newName);
  $newWorld = self::getServer()->getWorldManager()->getWorldByName($newName);
  if (!$newWorld instanceof World) {
   return;
  }
  $worldData = $newWorld->getProvider()->getWorldData();
  if (!$worldData instanceof BaseNbtWorldData) {
   return;
  }
  $worldData->getCompoundTag()->setString("LevelName", $newName);
  $server->getWorldManager()->unloadWorld($newWorld);
  self::lazyLoadWorld($newName);
  $player->sendMessage($plugin->ProcessTags(
   ["{prefix}", "{world}", "{new_name}"],
   [$message->get("plugin.prefix"), $oldName, $newName],
   $message->get("world.rename.sucess")));
   WorldReloadEvent::execute();
 }

 public static function lazyUnloadWorld(String $name, bool $force = false) {
  if (($world = self::getServer()->getWorldManager()->getWorldByName($name)) !== null) {
   return self::getServer()->getWorldManager()->unloadWorld($world, $force);
  }
  return false;
 }

 public static function lazyLoadWorld(String $name) {
  return !self::getServer()->getWorldManager()->isWorldLoaded($name) && self::getServer()->getWorldManager()->loadWorld($name, true);
 }

 public static function getPlugin() {
  return Loader::getInstance();
 }

 public static function getServer() {
  return Server::getInstance();
 }

}