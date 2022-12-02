<?php

namespace ImperaZim\EasyWorld\Functions;

use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use ImperaZim\EasyWorld\Loader;
use ImperaZim\EasyWorld\Utils\form\FormAPI;

class WorldSettings {

 public static function sendMenu(Player $player, $world) {
  
  /* ================================== */
  $plugin = self::getPlugin();
  $server = self::getServer();
  $message = $plugin->getConfig(); 
  $data = $plugin->getDataFolder();
  $config = (new Config($data . "worlds.yml"))->getAll()[$world];
  /* ================================== */
  
  $form = FormAPI::createCustomForm(function($player, $data = null){
   if (is_null($data)) return true;
   $plugin = self::getPlugin();
   $server = self::getServer();
   $message = $plugin->getConfig(); 
   $datafolder = $plugin->getDataFolder();
   $world = $player->getWorld()->getDisplayName();
   $worlds = (new Config($datafolder . "worlds.yml"))->getAll()[$world];
   
   self::chat($player, $world, $data["chat"]);
   self::combat($player, $world, $data["combat"]);
   self::protect($player, $world, $data["protect"]);
   
   $chat = $worlds["chat"] == true ? "§atrue" : "§cfalse";
   $combat = $worlds["combat"] == true ? "§atrue" : "§cfalse";
   $protected = $worlds["protected"] == true ? "§atrue" : "§cfalse";
   
   $player->sendMessage($plugin->ProcessTags(["{prefix}", "{world}"], [$message->get("plugin.prefix"), $world], $message->get("command.menu.sucess")));
   $player->sendMessage("§b[{$world}]§7 Chat: {$chat} §b| §7Protected: {$protected} §b| §7Combat: {$combat}");
  });
  $form->setTitle("§b@{$world}'s §7manager");
  $form->addLabel("§b");
  $form->addInput("§7World rename: §c(maintenance)", "World name", "$world", "name");
  $form->addLabel("§bUSAGE §r§7=> [§cOFF§7/§aON§7]");
  $form->addToggle("World Protect", $config["protected"], "protect");
  $form->addToggle("Send Message in World", $config["chat"], "chat");
  $form->addToggle("World Combat", $config["combat"], "combat");
  $form->sendToPlayer($player);
  return $form;  
 }

 public static function chat($player, $world, $bool) : void {
  $dataFolder = self::getPlugin()->getDataFolder();
  $config = new Config($dataFolder . "worlds.yml");
  $config->setNested("$world.chat", $bool);
  $config->save();
 }

 public static function combat($player, $world, $bool) : void {
  $dataFolder = self::getPlugin()->getDataFolder();
  $config = new Config($dataFolder . "worlds.yml");
  $config->setNested("$world.combat", $bool);
  $config->save();
 }

 public static function protect($player, $world, $bool) : void {
  $dataFolder = self::getPlugin()->getDataFolder();
  $config = new Config($dataFolder . "worlds.yml");
  $config->setNested("$world.protected", $bool);
  $config->save();
 }

 public static function nocommand($player, $world, $command) {
  $dataFolder = self::getPlugin()->getDataFolder();
  $config = new Config($dataFolder . "worlds.yml");
  $worlds = $config->getAll();
  if (!in_array($command, $worlds[$world]["commands_blocked"])) {
   $command = $worlds[$world]["commands_blocked"] .= "|{$command}";
   $config->setNested("$world.commands_blocked", $command);
   $config->save();
   //message
   return true;
  }
  //message
 }

 public static function getPlugin() {
  return Loader::getInstance();
 }

 public static function getServer() {
  return Server::getInstance();
 }

}
