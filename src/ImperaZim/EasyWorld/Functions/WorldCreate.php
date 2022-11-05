<?php

namespace ImperaZim\EasyWorld\Functions;

use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use ImperaZim\EasyWorld\Loader;
use pocketmine\world\WorldCreationOptions;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\generator\GeneratorManagerEntry;

class WorldCreate {

 public static function execute(Player $player, String $name, $seed = null, $generator = 'classic') {
  $plugin = self::getPlugin();
  $server = self::getServer();
  $message = $plugin->getConfig(); 
  $config = new Config($plugin->getDataFolder() . "worlds.yml");
  if (isset($config->getAll()[$name])) {
   $player->sendMessage($plugin->ProcessTags(
    ["{prefix}", "{world}"],
    [$message->get("plugin.prefix"), $name],
    $message->get("world.create.has")));
   return true;
  }
  if ($seed == null) $seed = mt_rand();
  if ((!isset($seed) && is_numeric($seed))) $seed = mt_rand();
  $generator = self::getGenerator($genNamed = $generator ?? "");
  $server->getWorldManager()->generateWorld($name, WorldCreationOptions::create()->setSeed($seed)->setGeneratorClass($generator->getGeneratorClass()));
  $player->sendMessage($plugin->ProcessTags(
   ["{prefix}", "{world}"],
   [$message->get("plugin.prefix"), $name],
   $message->get("world.create.sucess")));
 }

 public static function getGenerator(string $name): ?GeneratorManagerEntry {
  $name = match (strtolower($name)) {
   "classic",
   "basic" => "normal",
   "custom" => "normal_mw",
   "superflat" => "flat",
   "nether",
   "hell" => "nether_mw",
   "nether_old" => "nether",
   "sb" => "skyblock",
   "empty",
   "emptyworld" => "void",
   default => strtolower($name)
   };

   return GeneratorManager::getInstance()->getGenerator($name);
  }

  public static function getPlugin() {
   return Loader::getInstance();
  }

  public static function getServer() {
   return Server::getInstance();
  }

 }
