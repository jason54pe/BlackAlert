<?php
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
			case "blackalerts":
				if(isset($args[0])){
					$args[0] = strtolower($args[0]);
					if($args[0] == "help"){
						if($sender->hasPermission("blackalerts.help")){
							$sender->sendMessage($this->plugin->translateColors("&", "&b-- &aCommandes disponibles &b--"));
							$sender->sendMessage($this->plugin->translateColors("&", "&d/balerts help &b-&a Afficher l'aide sur ce plugin"));
							$sender->sendMessage($this->plugin->translateColors("&", "&d/balerts info &b-&a Afficher les informations sur ce plugin"));
							$sender->sendMessage($this->plugin->translateColors("&", "&d/balerts reload &b-&a Recharger la config"));
							break;
						}else{
							$sender->sendMessage($this->plugin->translateColors("&", "&cVous n'êtes pas autorisé à utiliser cette commande"));
							break;
						}
					}elseif($args[0] == "info"){
						if($sender->hasPermission("blackalerts.info")){
							$sender->sendMessage($this->plugin->translateColors("&", BlackAlerts::PREFIX . "&aBlackAlerts &dv" . BlackAlerts::VERSION . " &adéveloppé par&d " . BlackAlerts::PRODUCER));
							break;
						}else{
							$sender->sendMessage($this->plugin->translateColors("&", "&cVous n'êtes pas autorisé à utiliser cette commande"));
							break;
						}
					}elseif($args[0] == "reload"){
						if($sender->hasPermission("blackalerts.reload")){
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
						if($sender->hasPermission("blackalerts")){
							$sender->sendMessage($this->plugin->translateColors("&", BlackAlerts::PREFIX . "&cSous-commande &a" . $args[0] . " &cpas trouvé. Utilisation &a/balerts help &cafficher les commandes disponibles"));
							break;
						}else{
							$sender->sendMessage($this->plugin->translateColors("&", "&cVous n'êtes pas autorisé à utiliser cette commande"));
							break;
						}
					}
				}else{
					if($sender->hasPermission("blackalerts.help")){
							$sender->sendMessage($this->plugin->translateColors("&", "&b-- &aCommandes disponibles &b--"));
							$sender->sendMessage($this->plugin->translateColors("&", "&d/balerts help &b-&a Afficher l'aide sur ce plugin"));
							$sender->sendMessage($this->plugin->translateColors("&", "&d/balerts info &b-&a Afficher les informations sur ce plugin"));
							$sender->sendMessage($this->plugin->translateColors("&", "&d/balerts reload &b-&a Recharger la config"));
						break;
					}else{
						$sender->sendMessage($this->plugin->translateColors("&", "&cVous n'êtes pas autorisé à utiliser cette commande"));
						break;
					}
				}
		}
		return true;
	}
}