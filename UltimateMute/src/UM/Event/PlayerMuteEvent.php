<?php

namespace UM\Event;

use pocketmine\event\Cancellable;
use pocketmine\Player;
use UM\Event\MuteEvent;

class PlayerMuteEvent extends MuteEvent implements Cancellable {
	
	public static $handlerList = null;
	private $player;
	public function __construct($plugin, Player $player, $time) {
		$this->player = $player;
		$this->time = $time;
	}
	public function getPlayer() {
		return $this->player;
	}
	public function getMuteTime(){
		return $this->time;
	}
}