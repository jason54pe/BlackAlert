<?php

/*
 * BlackAlerts (v1.6) by EvolSoft
 * Developer: EvolSoft (Flavius12)
 * Website: http://www.evolsoft.tk
 * Date: 14/07/2015 01:33 PM (UTC)
 * Copyright & License: (C) 2014-2015 EvolSoft
 * Licensed under MIT (https://github.com/EvolSoft/BlackAlerts/blob/master/LICENSE)
 */

namespace BlackAlerts\Events;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

class BlackAlertsFullServerKickEvent extends PluginEvent{

	public static $handlerList = null;

	/** @var Player $player */
	private $player;

	/**
	 * @param Player $player
	 */
	public function __construct(Player $player){
		$this->player = $player;
	}

	/**
	 * Get full server kick event player
	 *
	 * @return Player
	 */
	public function getPlayer(){
		return $this->player;
	}
}