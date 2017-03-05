<?php

namespace UM;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\Config;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class Main extends PluginBase implements Listener{
  
  const PREFIX = "UltimateMute";
  
  public $config;
  
  public function onEnable(){
    @mkdir($this->getDataFolder());
    $this->config = new Config($this->getDataFolder()."mute.yml", Config::YAML, array());
    $this->saveDefaultConfig();
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->getLogger()->info(self::PREFIX." by LCraftPE Enabled...");
  }
  
  public function muteCheck($event, $player){
    $name = $player->getName();
    $mute = $this->config->getAll();
    $today = new \DateTime(date("d-m-Y", time()));
    $today->add(new \DateInterval("PT".date("H", time())."H".date("i", time())."M"));
    $today->format("d-m-Y H:i");
    $end = new \DateTime($mute[$player->getName()]["Mute"][0]);
    $end->add(new \DateInterval("P".$mute[$name]["Mute"][1]."DT".(date("H", $mute[$name]["UNIX"]) + $mute[$name]["Mute"][2])."H".(date("i", $mute[$name]["UNIX"]) + $mute[$name]["Mute"][3])."M"));
    $end->format("d-m-Y H:i");
    if($end <= $today){
      $this->config->remove($player->getName());
      $this->config->save();
      $player->sendMessage("§8» §7Vous pouvez de nouveau vous exprimer dans le tchat");
    }else{
      $event->setCancelled(true);
      $interval = $end->diff($today);
      $player->sendMessage("§8» §7Vous etes toujours mute §e(".$interval->format("%D j %H h et %I min").")"); 
    }
  }
  
  public function onCommand(CommandSender $sender, Command $command, $label, array $args){
    if($command->getName() == "mute"){
      if($sender instanceof Player){
        if($sender->isOp()){
          if(count($args) == 4){
            $player = $this->getServer()->getPlayer($args[0]);
            if($player instanceof Player && is_numeric($args[1]) && is_numeric($args[2]) && is_numeric($args[3])){
              if(strlen($args[1]) >= 2 && strlen($args[2]) >= 2 && strlen($args[3]) >= 2){
                $mute = $this->config->getAll();
                $date = new \DateTime(date("Y-m-d", time()));
                $format = $date->format("Y-m-d");
                $date->add(new \DateInterval("P".$args[1]."DT".$args[2]."H".$args[3]."M"));
                $mute[$player->getName()]["Mute"] = array($format, str_pad($date->format("d") - 5, 2, "0", STR_PAD_LEFT), $date->format("H"), $date->format("i"));
                $mute[$player->getName()]["UNIX"] = time();
                $this->config->setAll($mute);
                $this->config->save();
                $sender->sendMessage("§8» §7Vous avez mute §c".$player->getName()." §e(".$args[1]." j ".$args[2]." h et ".$args[3]." min)");
                $player->sendMessage("§8» §7Vous avez été mute §e(".$args[1]." j ".$args[2]." h ".$args[3]." min)");
              }else{
                $sender->sendMessage("§cVous devez écrire les jours, les heures et les minutes avec au moins 2 chiffres.");
              }
            }else{
              $sender->sendMessage("§cErreur d'arguments: essaie /mute {player} {days} {hours} {minutes}");
            }
          }else{
            $sender->sendMessage("§cErreur d'arguments: essaie /mute {player} {days} {hours} {minutes}");
          }
        }else{
          $sender->sendMessage("§cVous n'avez pas la permission d'éxécuter cette commande.");
        }
      }
    }
  }
  
  public function onPlayerJoin(PlayerJoinEvent $event){
    $player = $event->getPlayer();
    if(isset($this->config->getAll()[$player->getName()])) $this->muteCheck($event, $player);
  }
  
  public function onPlayerChat(PlayerChatEvent $event){
    $player = $event->getPlayer();
    if(isset($this->config->getAll()[$player->getName()])) $this->muteCheck($event, $player);
  }
  
  public function onPlayerCommandPreProcess(PlayerCommandPreProcessEvent $event){
    $player = $event->getPlayer();
    $cmd = explode(" ", $event->getMessage());
    $list = array("/say", "/tell", "/me", "/broadcast", "/announce", "w", "whisper", "msg", "m");
    if(in_array($cmd[0], $list) && isset($this->config->getAll()[$player->getName()])) $this->muteCheck($event, $player);
  }
}
