<?php
namespace BlackAlerts\Events;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

class CustomAlertsDeathEvent extends PluginEvent{

	public static $handlerList = null;

    private $player;

    private $cause;

    public function __construct(Player $player, $cause = null){
        $this->player = $player;
        $this->cause = $cause;
    }

    public function getPlayer(){
        return $this->player;
    }

    public function getCause(){
        return $this->cause;
    }

}