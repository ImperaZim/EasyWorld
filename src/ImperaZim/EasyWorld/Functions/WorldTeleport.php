<?php

namespace ImperaZim\EasyWorld\Functions;

use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use ImperaZim\EasyWorld\Loader;

class WorldTeleport {

 public static function execute(Player $player, String $name) {
  $plugin = self::getPlugin();
  $server = self::getServer();
  $message = $plugin->getConfig(); 
  $config = new Config($plugin->getDataFolder() . "worlds.yml");
  $server->getWorldManager()->loadWorld($name);
  if (isset($config->getAll()[$name])) {
   $world = $server->getWorldManager()->getWorldByName($name);
   $player->teleport($world->getSafeSpawn());
   $player->sendMessage($plugin->ProcessTags(["{prefix}", "{world}"], [$message->get("plugin.prefix"), $name], $message->get("world.tp.sucess")));
   return;
  }
  $player->sendMessage($plugin->ProcessTags(["{prefix}", "{world}"], [$message->get("plugin.prefix"), $name], $message->get("world.tp.unknow")));
 }

 public static function getPlugin() {
  return Loader::getInstance();
 }

 public static function getServer() {
  return Server::getInstance();
 }

}