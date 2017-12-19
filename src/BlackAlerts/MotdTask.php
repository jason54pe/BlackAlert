<?php

/*
 * BlackAlerts (v1.6) by EvolSoft
 * Developer: EvolSoft (Flavius12)
 * Website: http://www.evolsoft.tk
 * Date: 05/06/2015 10:51 AM (UTC)
 * Copyright & License: (C) 2014-2015 EvolSoft
 * Licensed under MIT (https://github.com/EvolSoft/BlackAlerts/blob/master/LICENSE)
 */

namespace BlackAlerts;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

use BlackAlerts\Events\BlackAlertsMotdUpdateEvent;

class MotdTask extends PluginTask{

	private $plugin;
	private $counter;

	public function __construct(BlackAlerts $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
		$this->counter = 0;
	}

	public function onRun(int $tick){
		$cfg = $this->plugin->getConfig()->getAll();
		$this->counter += 1;
		if($this->counter >= $cfg["Motd"]["update-timeout"]){
			//Check if Motd message is custom
			if(BlackAlerts::getAPI()->isMotdCustom()){
				BlackAlerts::getAPI()->setMotdMessage(BlackAlerts::getAPI()->getDefaultMotdMessage());
			}
			$this->plugin->getServer()->getPluginManager()->callEvent(new BlackAlertsMotdUpdateEvent($this->plugin->getServer()->getMotd()));
			$this->counter = 0;
		}
	}
}