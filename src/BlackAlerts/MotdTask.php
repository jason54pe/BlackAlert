<?php

namespace BlackAlerts;

use pocketmine\scheduler\Task;

class MotdTask extends Task {
    
    private $plugin;
	
    public function __construct(BlackAlerts $plugin){
      $this->plugin = $plugin;
    }
    
    public function onRun($tick){
        BlackAlerts::getAPI()->updateMotd();
    }

    public function getPlugin(){
        return $this->plugin;
    }
}
