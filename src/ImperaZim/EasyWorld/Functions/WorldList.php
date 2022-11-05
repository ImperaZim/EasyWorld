<?php

namespace ImperaZim\EasyWorld\Functions;

use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use ImperaZim\EasyWorld\Loader;

class WorldList {

 public static function execute(Player $player) {
  $plugin = self::getPlugin();
  $server = self::getServer();
  $message = $plugin->getConfig(); 
  $config = new Config($plugin->getDataFolder() . "worlds.yml");
  $worlds = $config->getAll();
  $player->sendMessage($plugin->ProcessTags(
   ["{prefix}"],
   [$message->get("plugin.prefix")],
   $message->get("command.list.message")));
   
  $worldsArray = scandir($server->getDataPath() . "worlds/");
  foreach ($worldsArray as $world) {
   if ($world === "." || $world === "..") { continue; }
   
   $name = $worlds[$world]["name"];
   $chat = $worlds[$world]["chat"];
   $combat = $worlds[$world]["combat"];
   $protected = $worlds[$world]["protected"];
   
   $chat = $chat == true ? "§atrue" : "§cfalse";
   $combat = $combat == true ? "§atrue" : "§cfalse";
   $protected = $protected == true ? "§atrue" : "§cfalse";
   
   $player->sendMessage("§b[{$name}]§7 Chat: {$chat} §b| §7Protected: {$protected} §b| §7Combat: {$combat}");
  }
  $player->sendMessage("§b ");
 }

 public static function getPlugin() {
  return Loader::getInstance();
 }

 public static function getServer() {
  return Server::getInstance();
 }

}