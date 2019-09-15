<?php

namespace BlackAlerts\Events;

use pocketmine\level\Level;
use pocketmine\Player;

class CustomAlertsWorldChangeEvent extends CustomAlertsEvent {
	
	public static $handlerList = null;
	
	private $player;
	
	private $origin;
	
	private $target;

	public function __construct(Player $player, Level $origin, Level $target){
		$this->player = $player;
		$this->origin = $origin;
		$this->target = $target;
	}

	public function getPlayer() : Player {
		return $this->player;
	}

	public function getOrigin() : Level {
		return $this->origin;
	}

	public function getTarget() : Level {
		return $this->target;
	}
}