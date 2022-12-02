<?php

namespace ImperaZim\EasyWorld\Events;

use pocketmine\utils\Config;
use pocketmine\event\Listener;
use ImperaZim\EasyWorld\Loader;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;

class WorldProtectEvent implements Listener {

 public function BreakEvent(BlockBreakEvent $event) {
  $plugin = $this->getPlugin();
  $player = $event->getPlayer();
  $world = $player->getWorld()->getDisplayName();
  $message = $plugin->getConfig(); 
  $config = new Config($plugin->getDataFolder() . "worlds.yml");
  if ($config->getAll()[$world]["protected"]) {
   $event->cancel();
   $player->sendMessage($plugin->ProcessTags(
    ["{prefix}", "{world}"],
    [$message->get("plugin.prefix"), $world],
    $message->get("world.protect.break.message")));
   return;
  }
 }

 public function PlaceEvent(BlockPlaceEvent $event) {
  $plugin = $this->getPlugin();
  $player = $event->getPlayer();
  $message = $plugin->getConfig();  
  $world = $player->getWorld()->getDisplayName();
  $config = new Config($plugin->getDataFolder() . "worlds.yml");
  if ($config->getAll()[$world]["protected"]) {
   $event->cancel();
   $player->sendMessage($plugin->ProcessTags(
    ["{prefix}", "{world}"],
    [$message->get("plugin.prefix"), $world],
    $message->get("world.protect.place.message")));
   return;
  }
 }

 public function getPlugin() {
  return Loader::getInstance();
 }

}
