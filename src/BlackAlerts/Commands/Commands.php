<?php

/*
 * BlackAlerts (v1.6) by EvolSoft
 * Developer: EvolSoft (Flavius12)
 * Website: http://www.evolsoft.tk
 * Date: 05/06/2015 10:52 AM (UTC)
 * Copyright & License: (C) 2014-2015 EvolSoft
 * Licensed under MIT (https://github.com/EvolSoft/BlackAlerts/blob/master/LICENSE)
 */

namespace BlackAlerts\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

use BlackAlerts\BlackAlerts;

class Commands extends PluginBase implements CommandExecutor{

	public function __construct(BlackAlerts $plugin){
		$this->plugin = $plugin;
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
		$fcmd = strtolower($cmd->getName());
		switch($fcmd){
			case "BlackAlerts":
				if(isset($args[0])){
					$args[0] = strtolower($args[0]);
					if($args[0] == "help"){
						if($sender->hasPermission("BlackAlerts.help")){
							$sender->sendMessage($this->plugin->translateColors("&", "&b-- &aCommandes disponibles &b--"));
							$sender->sendMessage($this->plugin->translateColors("&", "&d/calerts help &b-&a Afficher l'aide sur ce plugin"));
							$sender->sendMessage($this->plugin->translateColors("&", "&d/calerts info &b-&a Afficher les informations sur ce plugin"));
							$sender->sendMessage($this->plugin->translateColors("&", "&d/calerts reload &b-&a Recharger la config"));
							break;
						}else{
							$sender->sendMessage($this->plugin->translateColors("&", "&cVous n'êtes pas autorisé à utiliser cette commande"));
							break;
						}
					}elseif($args[0] == "info"){
						if($sender->hasPermission("BlackAlerts.info")){
							$sender->sendMessage($this->plugin->translateColors("&", BlackAlerts::PREFIX . "&aBlackAlerts &dv" . BlackAlerts::VERSION . " &adevelope par&d " . BlackAlerts::PRODUCER));
							$sender->sendMessage($this->plugin->translateColors("&", BlackAlerts::PREFIX . "&site &d" . BlackAlerts::MAIN_WEBSITE));
							break;
						}else{
							$sender->sendMessage($this->plugin->translateColors("&", "&cVous n'êtes pas autorisé à utiliser cette commande"));
							break;
						}
					}elseif($args[0] == "reload"){
						if($sender->hasPermission("BlackAlerts.reload")){
							$this->plugin->reloadConfig();
							//Reload Motd
							if(!BlackAlerts::getAPI()->isMotdCustom()){
								BlackAlerts::getAPI()->setMotdMessage($this->plugin->getServer()->getMotd());
							}
							$sender->sendMessage($this->plugin->translateColors("&", BlackAlerts::PREFIX . "&aConfiguration reloaded."));
							break;
						}else{
							$sender->sendMessage($this->plugin->translateColors("&", "&cVous n'êtes pas autorisé à utiliser cette commande"));
							break;
						}
					}else{
						if($sender->hasPermission("BlackAlerts")){
							$sender->sendMessage($this->plugin->translateColors("&", BlackAlerts::PREFIX . "&cSubcommand &a" . $args[0] . " &cpas trouvé. Utilisation &a/calerts help &cafficher les commandes disponibles"));
							break;
						}else{
							$sender->sendMessage($this->plugin->translateColors("&", "&cVous n'êtes pas autorisé à utiliser cette commande"));
							break;
						}
					}
				}else{
					if($sender->hasPermission("BlackAlerts.help")){
						$sender->sendMessage($this->plugin->translateColors("&", "&b-- &aCommandes disponibles &b--"));
						$sender->sendMessage($this->plugin->translateColors("&", "&d/calerts help &b-&a Afficher l'aide sur ce plugin"));
						$sender->sendMessage($this->plugin->translateColors("&", "&d/calerts info &b-&a Afficher les informations sur ce plugin"));
						$sender->sendMessage($this->plugin->translateColors("&", "&d/calerts reload &b-&a Recharger la config"));
						break;
					}else{
						$sender->sendMessage($this->plugin->translateColors("&", "&cVous n'êtes pas autorisé à utiliser cette commande"));
						break;
					}
				}
		}
	}
}