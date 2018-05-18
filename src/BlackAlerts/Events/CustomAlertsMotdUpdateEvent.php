<?php
namespace BlackAlerts\Events;

use pocketmine\event\plugin\PluginEvent;

class CustomAlertsMotdUpdateEvent extends PluginEvent{

	public static $handlerList = null;

	private $pocketminemessage;

	public function __construct($pocketminemessage){
		$this->pocketminemessage = $pocketminemessage;
	}

	public function getPocketMineMotd(){
		return $this->pocketminemessage;
	}
}