<?php
namespace BlackAlerts\Events;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\level\Level;
use pocketmine\Player;

class CustomAlertsWorldChangeEvent extends PluginEvent{

	public static $handlerList = null;

	private $player;

	private $origin;

	private $target;

	public function __construct(Player $player, Level $origin, Level $target){
		$this->player = $player;
		$this->origin = $origin;
		$this->target = $target;
	}

	public function getPlayer(){
		return $this->player;
	}

	public function getOrigin(){
		return $this->origin;
	}

	public function getTarget(){
		return $this->target;
	}

}