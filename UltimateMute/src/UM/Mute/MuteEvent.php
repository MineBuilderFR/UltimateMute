<?php

namespace UM\Mute;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

use UM\Main;

use UM\API\API; //API

class MuteEvent extends PluginBase implements Listener {
	public function __construct(Main $plugin, $config) {
		$this->plugin = $plugin;
		$this->config = $config;
	}
	public function onPlayerJoin(PlayerJoinEvent $event) {
		$player = $event->getPlayer ();
		if (isset ( $this->config->getAll () [$player->getName ()] ))
			$this->muteCheck ( $event, $player );
	}
	public function onPlayerChat(PlayerChatEvent $event) {
		$player = $event->getPlayer ();
		if (isset ( $this->config->getAll () [$player->getName ()] ))
			$this->muteCheck ( $event, $player );
	}
	public function onPlayerCommandPreProcess(PlayerCommandPreProcessEvent $event) {
		$player = $event->getPlayer ();
		$cmd = explode ( " ", $event->getMessage () );
		$list = array (
				"/say",
				"/tell",
				"/me",
				"/broadcast",
				"/announce",
				"w",
				"whisper",
				"msg",
				"m" 
		);
		if (in_array ( $cmd [0], $list ) && isset ( $this->config->getAll () [$player->getName ()] ))
			$this->muteCheck ( $event, $player );
	}
	
	public function muteCheck($event, Player $player){
		return API::getInstance()->muteCheck($event, $player);
	}
}
