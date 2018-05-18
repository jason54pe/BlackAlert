<?php
namespace BlackAlerts\Events;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

class CustomAlertsOutdatedServerKickEvent extends PluginEvent{

	public static $handlerList = null;

	private $player;

	public function __construct(Player $player){
		$this->player = $player;
	}

	public function getPlayer(){
		return $this->player;
	}
}