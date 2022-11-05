<?php

namespace ImperaZim\EasyWorld\Events;

use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\event\Listener;
use ImperaZim\EasyWorld\Loader;
use pocketmine\event\server\CommandEvent;

class WorldNoCommandEvent implements Listener {

 public function __construct() {
  /* TODO: NULL */
 }

 public function SendCommand(CommandEvent $event) {
  $plugin = $this->getPlugin();
  $player = $event->getSender();
  $message = $plugin->getConfig(); 
  if ($player instanceof Player) {
   $command = $event->getCommand() . " command";
   $command = explode(" ", $command);
   $command = $command[0];
   $world = $player->getWorld()->getDisplayName();
   $config = new Config($plugin->getDataFolder() . "worlds.yml");
   $black_list = $config->getAll()[$world]["commands_blocked"];
   if($black_list == []) $black_list = ["#blocked"];
   if(isset($black_list[1])) $black_list .= "|#blocked";
   if (in_array($command, $black_list)) {
    $event->cancel();
    $player->sendMessage($plugin->ProcessTags(
      ["{prefix}", "command", "{world}"],
      [$message->get("plugin.prefix"), $command, $world],
      $message->get("command.block.error")));
    return true;
   }
  }
 }

 public function getPlugin() {
  return Loader::getInstance();
 }

}