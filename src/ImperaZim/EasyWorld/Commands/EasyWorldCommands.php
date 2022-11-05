<?php

namespace ImperaZim\EasyWorld\Commands;

use pocketmine\Server;
use pocketmine\player\Player;
use ImperaZim\EasyWorld\Loader;
use pocketmine\command\Command;
use pocketmine\plugin\PluginOwned;
use pocketmine\command\CommandSender;
use ImperaZim\EasyWorld\Functions\WorldList;
use ImperaZim\EasyWorld\Functions\WorldCreate;
use ImperaZim\EasyWorld\Functions\WorldDelete;
use ImperaZim\EasyWorld\Functions\WorldRename;
use ImperaZim\EasyWorld\Functions\WorldSettings;
use ImperaZim\EasyWorld\Functions\WorldTeleport;

class EasyWorldCommands extends Command implements PluginOwned {

 public function __construct() {
  parent::__construct("EasyWorld", "§7EasyWorld settings!", null, ["easyworld", "ew"]);
  $this->setPermission("easyworld.manager");
 }

 public function execute(CommandSender $player, string $commandLabel, array $args) : bool {
  if (!$player instanceof Player) {
   self::getServer()->getLogger()->error("This command can only be used in the game");
   return true;
  }
  self::subcommands($player, $args);
  return true;
 }
 
 public static function subcommands($player, array $args) {
  $server = self::getServer();
  $plugin = Loader::getInstance();
  $world = $player->getWorld()->getDisplayName(); 
  $message = Loader::getInstance()->getConfig();
  if (isset($args[0])) {
   if (in_array(strtolower($args[0]), ["help", "ajuda"])) {
    if ((!isset($args[1])) || $args[1] == "1") {
     $player->sendMessage($plugin->ProcessTags(["{prefix}"],  [$message->get("plugin.prefix")], "{prefix} §7EasyWorld commands: [2/2] \n 
     §b-> §7/easyworld help
     §b-> §7/easyworld create
     §b-> §7/easyworld delete
     §b-> §7/easyworld teleport
     "));
     return true; 
    }
    if ((isset($args[1])) || $args[1] == "2") {
     $player->sendMessage($plugin->ProcessTags(["{prefix}"],  [$message->get("plugin.prefix")], 
      "{prefix} §7EasyWorld commands: [2/2] \n 
      §b-> §7/easyworld list
      §b-> §7/easyworld manager
      §b-> §7/easyworld command
      "));
     return true;
    }
    if ($args[1] >= 3) {
     $player->sendMessage($plugin->ProcessTags(["{prefix}"],  [$message->get("plugin.prefix")], "{prefix} §7usage: §b/easyworld help [1 or 2]")); 
    }
    $player->sendMessage($plugin->ProcessTags(["{prefix}"],  [$message->get("plugin.prefix")], "{prefix} §7usage: §b/easyworld help [1 or 2]")); 
    return true;
   }
   if (in_array(strtolower($args[0]), ["create", "criar"])) {
    if (isset($args[1])) {
     $name = $args[1];
     $seed = $args[2] ?? 1000;
     $generator = $args[3] ?? "normal";
     WorldCreate::execute($player, $name, $seed, $generator);
     return true;
    }
    $player->sendMessage($plugin->ProcessTags(["{prefix}"], [$message->get("plugin.prefix")], $message->get("world.create.help")));
    return true;
   }
   if (in_array(strtolower($args[0]), ["delete", "deletar"])) {
    if (isset($args[1])) {
     $name = $args[1];
     WorldDelete::execute($player, $args[1]);
     return true;
    }
    $player->sendMessage($plugin->ProcessTags(["{prefix}"], [$message->get("plugin.prefix")], $message->get("world.delete.help")));
    return true;
   }
   if (in_array(strtolower($args[0]), ["rename", "renomear"])) {
    if (isset($args[2])) {
     $old = $args[1];
     $new = $args[2];
     WorldRename::execute($player, $old, $new);
     return true;
    }
    $player->sendMessage($plugin->ProcessTags(["{prefix}"], [$message->get("plugin.prefix")], $message->get("world.rename.help")));
    return true;
   }
   if (in_array(strtolower($args[0]), ["manager", "settings"])) {
    WorldSettings::sendMenu($player, $world);
    return true;
   }
   if (in_array(strtolower($args[0]), ["teleport", "tp", "to"])) {
    if (isset($args[1])) {
     $name = $args[1];
     WorldTeleport::execute($player, $name);
     return true;
    }
    $player->sendMessage($plugin->ProcessTags(["{prefix}"], [$message->get("plugin.prefix")], $message->get("world.tp.help")));
    return true;
   }
   if (in_array(strtolower($args[0]), ["command", "blockcmd", "blockcommand"])) {
    if (isset($args[1])) {
     $command = $args[1];
     WorldSettings::nocmmand($player, $world, $command);
     return true;
    }
    $player->sendMessage($plugin->ProcessTags(["{prefix}"], [$message->get("plugin.prefix")], $message->get("command.block.help")));
    return true;
   }
   if (in_array(strtolower($args[0]), ["list", "lista", "worlds"])) {
    WorldList::execute($player);
    return true; 
   }
   return true;
  }
  $player->sendMessage($plugin->ProcessTags(["{prefix}"], [$message->get("plugin.prefix")], "{prefix} §7usage: §b/easyworld help"));
  return true;
 }
 
 public static function getServer() {
  return Server::getInstance();
 }

 public function getOwningPlugin(): Loader {
  return Loader::getInstance();
 }

}
