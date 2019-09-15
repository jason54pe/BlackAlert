<?php

namespace BlackAlerts\Events;

use pocketmine\Player;

class CustomAlertsQuitEvent extends CustomAlertsEvent {
	
	public static $handlerList = null;
	
	private $player;

	public function __construct(Player $player){
		$this->player = $player;
	}

	public function getPlayer() : Player {
		return $this->player;
	}
}