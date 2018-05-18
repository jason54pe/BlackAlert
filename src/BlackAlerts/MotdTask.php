<?php
namespace BlackAlerts;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

use BlackAlerts\Events\CustomAlertsMotdUpdateEvent;

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
			if(BlackAlerts::getAPI()->isMotdCustom()){
				BlackAlerts::getAPI()->setMotdMessage(BlackAlerts::getAPI()->getDefaultMotdMessage());
			}
			$this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsMotdUpdateEvent($this->plugin->getServer()->getMotd()));
			$this->counter = 0;
		}
	}
}