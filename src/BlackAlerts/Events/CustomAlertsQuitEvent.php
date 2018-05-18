<?php
namespace BlackAlerts\Events;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

class CustomAlertsQuitEvent extends PluginEvent{

	public static $handlerList = null;

	private $player;

	private $pocketminemessage;

	public function __construct(Player $player, $pocketminemessage){
		$this->player = $player;
		$this->pocketminemessage = $pocketminemessage;
	}

	public function getPlayer(){
		return $this->player;
	}

	public function getPocketMineQuitMessage(){
		return $this->pocketminemessage;
	}
}