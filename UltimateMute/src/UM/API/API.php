<?php

namespace UM\API;

use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class API extends PluginBase implements Listener {
	
	private static $instance = null;
	
	public function __construct($plugin, Config $config){
		$this->plugin = $plugin;
		$this->config = $config;
		self::$instance = $this;
	}
	
	public static function getInstance() {
		return self::$instance;
	}
	
	public function muteCheck($event, Player $player) {
		$name = $player->getName ();
		$mute = $this->config->getAll ();
		$today = new \DateTime ( date ( "d-m-Y", time () ) );
		$today->add ( new \DateInterval ( "PT" . date ( "H", time () ) . "H" . date ( "i", time () ) . "M" ) );
		$today->format ( "d-m-Y H:i" );
		$end = new \DateTime ( $mute [$player->getName ()] ["Mute"] [0] );
		$end->add ( new \DateInterval ( "P" . $mute [$name] ["Mute"] [1] . "DT" . (date ( "H", $mute [$name] ["UNIX"] ) + $mute [$name] ["Mute"] [2]) . "H" . (date ( "i", $mute [$name] ["UNIX"] ) + $mute [$name] ["Mute"] [3]) . "M" ) );
		$end->format ( "d-m-Y H:i" );
		if ($end <= $today) {
			$this->config->remove ( $player->getName () );
			$this->config->save ();
			$player->sendMessage ( "§8» §7Vous pouvez de nouveau vous exprimer dans le tchat" );
		} else {
			$event->setCancelled ( true );
			$interval = $end->diff ( $today );
			$player->sendMessage ( "§8» §7Vous etes toujours mute §e(" . $interval->format ( "%D j %H h et %I min" ) . ")" );
		}
	}
	
	//TODO: Add More Event in API
}
