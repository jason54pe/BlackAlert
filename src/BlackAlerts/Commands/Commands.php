<?php

namespace BlackAlerts\Commands;

use pocketmine\command\Command;
use pocketmine\command\PluginCommand;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use BlackAlerts\BlackAlerts;

class Commands extends PluginCommand implements CommandExecutor {

	public function __construct(BlackAlerts $plugin){
       $this->plugin = $plugin;
    }
    
    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) : bool {
		if(isset($args[0])){
			$args[0] = strtolower($args[0]);
			switch($args[0]){
			    case "help":
			        goto help;
			    case "info":
			        if($sender->hasPermission("customalerts.info")){
			            $sender->sendMessage(TextFormat::colorize(CustomAlerts::PREFIX . "&aBlackAlerts &dv" . $this->plugin->getDescription()->getVersion() . "&a développé par la &dBlackTeam"));
			            break;
			        }
			        $sender->sendMessage(TextFormat::colorize("&cVous n'êtes pas autorisé à utiliser cette commande"));
			        break;
			    case "reload":
			        if($sender->hasPermission("customalerts.reload")){
			            $this->plugin->reloadConfig();
			            $this->plugin->cfg = $this->plugin->getConfig()->getAll();
			            $sender->sendMessage(TextFormat::colorize(CustomAlerts::PREFIX . "&aConfiguration rechargée."));
			            break;
			        }
			        $sender->sendMessage(TextFormat::colorize("&cVous n'êtes pas autorisé à utiliser cette commande"));
			        break;
			    default:
			        if($sender->hasPermission("customalerts")){
			            $sender->sendMessage(TextFormat::colorize(CustomAlerts::PREFIX . "&cSubcommand &a" . $args[0] . " &cpas trouvé. Utilisation &a/calerts help &cafficher les commandes disponibles"));
			            break;
			        }
			        $sender->sendMessage(TextFormat::colorize("&cVous n'êtes pas autorisé à utiliser cette commande"));
			        break;
			}
			return true;
		}
		help:
		if($sender->hasPermission("customalerts.help")){
		    $sender->sendMessage(TextFormat::colorize("&b-- &aCommandes disponibles &b--"));
		    $sender->sendMessage(TextFormat::colorize("&d/calerts help &b-&a Afficher l'aide sur ce plugin"));
		    $sender->sendMessage(TextFormat::colorize("&d/calerts info &b-&a Afficher les infos sur ce plugin"));
		    $sender->sendMessage(TextFormat::colorize("&d/calerts reload &b-&a Recharger la config"));
		}else{
		    $sender->sendMessage(TextFormat::colorize("&cVous n'êtes pas autorisé à utiliser cette commande"));
		}
    	return true;
    }
}
