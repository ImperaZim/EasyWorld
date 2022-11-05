<?php

namespace ImperaZim\EasyWorld\Events;

use pocketmine\utils\Config;
use pocketmine\event\Listener;
use ImperaZim\EasyWorld\Loader;
use pocketmine\event\player\PlayerChatEvent;

class WorldNoChatEvent implements Listener {
 
 public function __construct() { /* TODO: NULL */ } 
 
 public function WorldChatEvent(PlayerChatEvent $event) {
  $plugin = $this->getPlugin();
  $player = $event->getPlayer();
  $world = $player->getWorld()->getDisplayName();
  $config = new Config($plugin->getDataFolder() . "worlds.yml");
  $message = $plugin->getConfig(); 
  if (!$config->getAll()[$world]["chat"]) {
   $event->cancel();
   $player->sendMessage($plugin->ProcessTags(
     ["{prefix}", "{world}"], 
     [$message->get("plugin.prefix"), $world],
     $message->get("world.chat.locked.message")));
   return true;
  } 
 }
 
  public function getPlugin() {
  return Loader::getInstance();
 } 
 
} 