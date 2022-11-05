<?php

namespace ImperaZim\EasyWorld\Functions;

use SplFileInfo;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use RecursiveIteratorIterator;
use ImperaZim\EasyWorld\Loader;
use RecursiveDirectoryIterator;
use pocketmine\world\WorldCreationOptions;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\generator\GeneratorManagerEntry;

class WorldDelete {

 public static function execute(Player $player, String $name) {
  $plugin = self::getPlugin();
  $server = self::getServer();
  $message = $plugin->getConfig(); 
  $config = new Config($plugin->getDataFolder() . "worlds.yml");
  if (!isset($config->getAll()[$name])) {
   $player->sendMessage($plugin->ProcessTags(
    ["{prefix}", "{world}"],
    [$message->get("plugin.prefix"), $name],
    $message->get("world.delete.non")));
   return true;
  }
  if ($server->getWorldManager()->isWorldLoaded($name)) {
   $world = self::getWorldByNameNonNull($name);
   if (count($world->getPlayers()) > 0) {
    foreach ($world->getPlayers() as $player) {
     $player->teleport(self::getDefaultWorldNonNull()->getSpawnLocation());
    }
   }
  }
  $removedFiles = 1;
  $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($worldPath = $server->getDataPath() . "/worlds/$name", FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
  foreach ($files as $fileInfo) {
   if ($filePath = $fileInfo->getRealPath()) {
    if ($fileInfo->isFile()) {
     unlink($filePath);
    } else {
     rmdir($filePath);
    }
    $removedFiles++;
   }
  }
  unset($config->getAll()[$name]);
  rmdir($worldPath);
  $player->sendMessage($plugin->ProcessTags(
   ["{prefix}", "{world}"],
   [$message->get("plugin.prefix"), $name],
   $message->get("world.delete.sucess")));
 }

 public static function getWorldByNameNonNull($name) {
  return self::getServer()->getWorldManager()->getWorldByName($name);
 }

 public static function getDefaultWorldNonNull() {
  return self::getServer()->getWorldManager()->getDefaultWorld();
 }

 public static function getPlugin() {
  return Loader::getInstance();
 }

 public static function getServer() {
  return Server::getInstance();
 }

}