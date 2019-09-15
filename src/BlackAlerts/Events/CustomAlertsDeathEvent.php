<?php

namespace BlackAlerts\Events;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;

class CustomAlertsDeathEvent extends CustomAlertsEvent {
	
	public static $handlerList = null;
	
	private $player;
	
	private $cause;
	
	public function __construct(Player $player, EntityDamageEvent $cause = null){
		$this->player = $player;
		$this->cause = $cause;
	}

	public function getPlayer() : Player {
		return $this->player;
	}

	public function getCause() : ?EntityDamageEvent {
		return $this->cause;
	}
	
}