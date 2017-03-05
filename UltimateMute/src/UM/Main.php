<?php

namespace UM;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

// Register Listener
use UM\API\API;
use UM\Mute\MuteEvent;

//Event
use UM\Event\PlayerMuteEvent;

class Main extends PluginBase implements Listener {
	const PREFIX = "UltimateMute";
	public $config;
	public function onEnable() {
		@mkdir ( $this->getDataFolder () );
		$this->config = new Config ( $this->getDataFolder () . "mute.yml", Config::YAML, array () );
		$this->saveDefaultConfig ();
		$this->RegisterListener (); // Register All Class
		$this->getLogger ()->info ( self::PREFIX . " by LCraftPE Enabled..." );
	}
	public function RegisterListener() {
		$this->getServer ()->getPluginManager ()->registerEvents ( $this, $this );
		$this->getServer ()->getPluginManager ()->registerEvents ( new MuteEvent ( $this, $this->config ), $this );
		$this->getServer ()->getPluginManager ()->registerEvents ( new API ( $this, $this->config ), $this );
	}
	
	/*
	 * Command Base
	 */
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
		if ($command->getName () == "mute") {
			if ($sender instanceof Player) {
				if ($sender->isOp ()) {
					if (count ( $args ) == 4) {
						$player = $this->getServer ()->getPlayer ( $args [0] );
						if ($player instanceof Player && is_numeric ( $args [1] ) && is_numeric ( $args [2] ) && is_numeric ( $args [3] )) {
							if (strlen ( $args [1] ) >= 2 && strlen ( $args [2] ) >= 2 && strlen ( $args [3] ) >= 2) {
								
								$this->getServer ()->getPluginManager ()->callEvent ( $ev = new PlayerMuteEvent ( $this, $player, $args[1] . ':' . $args[2] . ':' . $args[3] ) );
								if ($ev->isCancelled ())
									return;
								
								$mute = $this->config->getAll ();
								$date = new \DateTime ( date ( "Y-m-d", time () ) );
								$format = $date->format ( "Y-m-d" );
								$date->add ( new \DateInterval ( "P" . $args [1] . "DT" . $args [2] . "H" . $args [3] . "M" ) );
								$mute [$player->getName ()] ["Mute"] = array (
										$format,
										str_pad ( $date->format ( "d" ) - 5, 2, "0", STR_PAD_LEFT ),
										$date->format ( "H" ),
										$date->format ( "i" ) 
								);
								$mute [$player->getName ()] ["UNIX"] = time ();
								$this->config->setAll ( $mute );
								$this->config->save ();
								$sender->sendMessage ( "§8» §7Vous avez mute §c" . $player->getName () . " §e(" . $args [1] . " j " . $args [2] . " h et " . $args [3] . " min)" );
								$player->sendMessage ( "§8» §7Vous avez été mute §e(" . $args [1] . " j " . $args [2] . " h " . $args [3] . " min)" );
							} else {
								$sender->sendMessage ( "§cVous devez écrire les jours, les heures et les minutes avec au moins 2 chiffres." );
							}
						} else {
							$sender->sendMessage ( "§cErreur d'arguments: essaie /mute {player} {days} {hours} {minutes}" );
						}
					} else {
						$sender->sendMessage ( "§cErreur d'arguments: essaie /mute {player} {days} {hours} {minutes}" );
					}
				} else {
					$sender->sendMessage ( "§cVous n'avez pas la permission d'éxécuter cette commande." );
				}
			}
		}
	}
	
	/*
	 * Return to API
	 */
	public function muteCheck($event, Player $player) {
		return API::getInstance ()->muteCheck ( $event, $player );
	}
}
