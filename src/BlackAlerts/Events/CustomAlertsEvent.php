<?php

namespace BlackAlerts\Events;

use pocketmine\event\plugin\PluginEvent;

abstract class CustomAlertsEvent extends PluginEvent {
    
    private $message;

    public function getMessage(){
        return $this->message;
    }

    public function setMessage($message){
        $this->message = $message;
    }
}