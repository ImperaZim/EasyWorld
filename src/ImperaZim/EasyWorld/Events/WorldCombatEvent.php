<?php

namespace ImperaZim\EasyWorld\Events;

use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\event\Listener;
use ImperaZim\EasyWorld\Loader;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class WorldCombatEvent implements Listener {

 public function DamageEvent(EntityDamageEvent $event) {
  $plugin = $this->getPlugin();
  $player = $event->getEntity();
  $message = $plugin->getConfig();
  $world = $player->getWorld()->getDisplayName(); 
  $config = new Config($plugin->getDataFolder() . "worlds.yml");
  if($event->getCause() == 11){
   $event->cancel();
   $player->teleport($player->getWorld()->getSafeSpawn()); 
   return true;
  }
  if (!$config->getAll()[$world]["combat"]) {
   $event->cancel();
   return true;
  }
 }
 
 public function ByEntityEvent(EntityDamageByEntityEvent $event) {
  $plugin = $this->getPlugin();
  $damaged = $event->getEntity();
  $message = $plugin->getConfig();
   if (!$damaged instanceof Player) {
   $damager = $event->getDamager();
   $world = $damaged->getWorld()->getDisplayName(); 
   $config = new Config($plugin->getDataFolder() . "worlds.yml");
   if (!$config->getAll()[$world]["combat"]) {
    $event->cancel();
    $damager->sendMessage($plugin->ProcessTags(["{prefix}", "{world}"], [$message->get("plugin.prefix"), $world], $message->get("world.combat.off.message")));
    return true;
   } 
  }
 }

 public function getPlugin() {
  return Loader::getInstance();
 }

}
