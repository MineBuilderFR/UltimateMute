<?php

namespace UM\Event;

use pocketmine\event\plugin\PluginEvent;
use UM\Main;

class MuteEvent extends PluginEvent {
	public function __construct($plugin) {
		if($plugin instanceof Main) parent::__construct ( $plugin );
	}
}