<?php
namespace BlackAlerts;

use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\Player;
use pocketmine\Server;

use BlackAlerts\Events\CustomAlertsDeathEvent;
use BlackAlerts\Events\CustomAlertsFullServerKickEvent;
use BlackAlerts\Events\CustomAlertsJoinEvent;
use BlackAlerts\Events\CustomAlertsMotdUpdateEvent;
use BlackAlerts\Events\CustomAlertsOutdatedClientKickEvent;
use BlackAlerts\Events\CustomAlertsOutdatedServerKickEvent;
use BlackAlerts\Events\CustomAlertsQuitEvent;
use BlackAlerts\Events\CustomAlertsWhitelistKickEvent;
use BlackAlerts\Events\CustomAlertsWorldChangeEvent;

class EventListener implements Listener{


    private $plugin;

	public function __construct(BlackAlerts $plugin){
		$this->plugin = $plugin;
	}

	public function onReceivePacket(DataPacketReceiveEvent $event){
		$player = $event->getPlayer();
		$packet = $event->getPacket();
		if($packet instanceof LoginPacket){
			if($packet->protocol < ProtocolInfo::CURRENT_PROTOCOL){
				if(BlackAlerts::getAPI()->isOutdatedClientMessageCustom()){
					BlackAlerts::getAPI()->setOutdatedClientMessage(BlackAlerts::getAPI()->getDefaultOutdatedClientMessage($player));
				}
				$this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsOutdatedClientKickEvent($player));
				if(BlackAlerts::getAPI()->getOutdatedClientMessage() != null){
					$player->close("", BlackAlerts::getAPI()->getOutdatedClientMessage());
					$event->setCancelled(true);
				}
			}elseif($packet->protocol > ProtocolInfo::CURRENT_PROTOCOL){
				if(BlackAlerts::getAPI()->isOutdatedServerMessageCustom()){
					BlackAlerts::getAPI()->setOutdatedServerMessage(BlackAlerts::getAPI()->getDefaultOutdatedServerMessage($player));
				}
				$this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsOutdatedServerKickEvent($player));
				if(BlackAlerts::getAPI()->getOutdatedServerMessage() != null){
					$player->close("", BlackAlerts::getAPI()->getOutdatedServerMessage());
					$event->setCancelled(true);
				}
			}
		}
	}

	public function onPlayerPreLogin(PlayerPreLoginEvent $event){
		$player = $event->getPlayer();
		if(count($this->plugin->getServer()->getOnlinePlayers()) - 1 < $this->plugin->getServer()->getMaxPlayers()){
			if(!$this->plugin->getServer()->isWhitelisted($event->getPlayer()->getName())){
				if(BlackAlerts::getAPI()->isWhitelistMessageCustom()){
					BlackAlerts::getAPI()->setWhitelistMessage(BlackAlerts::getAPI()->getDefaultWhitelistMessage($player));
				}
				$this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsWhitelistKickEvent($player));
				if(BlackAlerts::getAPI()->getWhitelistMessage() != null){
					$player->close("", BlackAlerts::getAPI()->getWhitelistMessage());
					$event->setCancelled(true);
				}
			}
		}else{
			if(BlackAlerts::getAPI()->isFullServerMessageCustom()){
				BlackAlerts::getAPI()->setFullServerMessage(BlackAlerts::getAPI()->getDefaultFullServerMessage($player));
			}
			$this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsFullServerKickEvent($player));
			if(BlackAlerts::getAPI()->getFullServerMessage() != null){
				$player->close("", BlackAlerts::getAPI()->getFullServerMessage());
				$event->setCancelled(true);
			}
		}
	}

	public function onPlayerJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		if(BlackAlerts::getAPI()->isMotdCustom()){
			BlackAlerts::getAPI()->setMotdMessage(BlackAlerts::getAPI()->getDefaultMotdMessage());
		}else{
			BlackAlerts::getAPI()->setMotdMessage($this->plugin->translateColors("&", $this->plugin->getServer()->getMotd()));
		}
		$this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsMotdUpdateEvent($this->plugin->getServer()->getMotd()));
		$this->plugin->getServer()->getNetwork()->setName(BlackAlerts::getAPI()->getMotdMessage());
		$status = 0;
		BlackAlerts::getAPI()->setJoinMessage($event->getJoinMessage());
		if(BlackAlerts::getAPI()->hasJoinedFirstTime($player)){
			BlackAlerts::getAPI()->registerFirstJoin($player);
			if(BlackAlerts::getAPI()->isDefaultFirstJoinMessageEnabled()){
				BlackAlerts::getAPI()->setJoinMessage(BlackAlerts::getAPI()->getDefaultFirstJoinMessage($player));
				$status = 1;
			}
		}
		if($status == 0){
			if(BlackAlerts::getAPI()->isDefaultJoinMessageHidden()){
				BlackAlerts::getAPI()->setJoinMessage("");
			}else{
				if(BlackAlerts::getAPI()->isDefaultJoinMessageCustom()){
					BlackAlerts::getAPI()->setJoinMessage(BlackAlerts::getAPI()->getDefaultJoinMessage($player));
				}
			}
		}
		$this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsJoinEvent($player, $event->getJoinMessage()));
		$event->setJoinMessage(BlackAlerts::getAPI()->getJoinMessage());
	}

	public function onPlayerQuit(PlayerQuitEvent $event){
		$player = $event->getPlayer();
		if(BlackAlerts::getAPI()->isMotdCustom()){
			BlackAlerts::getAPI()->setMotdMessage(BlackAlerts::getAPI()->getDefaultMotdMessage());
		}else{
			BlackAlerts::getAPI()->setMotdMessage($this->plugin->translateColors("&", $this->plugin->getServer()->getMotd()));
		}
		$this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsMotdUpdateEvent($this->plugin->getServer()->getMotd()));
		$this->plugin->getServer()->getNetwork()->setName(BlackAlerts::getAPI()->getMotdMessage());
		BlackAlerts::getAPI()->setQuitMessage($event->getQuitMessage());
		if(BlackAlerts::getAPI()->isQuitHidden()){
			BlackAlerts::getAPI()->setQuitMessage("");
		}else{
			if(BlackAlerts::getAPI()->isQuitCustom()){
				BlackAlerts::getAPI()->setQuitMessage(BlackAlerts::getAPI()->getDefaultQuitMessage($player));
			}
		}
		$this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsQuitEvent($player, $event->getQuitMessage()));
		$event->setQuitMessage(BlackAlerts::getAPI()->getQuitMessage());
	}

	public function onWorldChange(EntityLevelChangeEvent $event){
		$entity = $event->getEntity();
		BlackAlerts::getAPI()->setWorldChangeMessage("");
		if($entity instanceof Player){
			$player = $entity;
			$origin = $event->getOrigin();
			$target = $event->getTarget();
			if(BlackAlerts::getAPI()->isDefaultWorldChangeMessageEnabled()){
				BlackAlerts::getAPI()->setWorldChangeMessage(BlackAlerts::getAPI()->getDefaultWorldChangeMessage($player, $origin, $target));
			}
			$this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsWorldChangeEvent($player, $origin, $target));
			if(BlackAlerts::getAPI()->getWorldChangeMessage() != ""){
				Server::getInstance()->broadcastMessage(BlackAlerts::getAPI()->getWorldChangeMessage());
			}
		}
	}


	public function onPlayerDeath(PlayerDeathEvent $event){
		$player = $event->getEntity();
		BlackAlerts::getAPI()->setDeathMessage($event->getDeathMessage());
		if($player instanceof Player){
			$cause = $player->getLastDamageCause();
			if(BlackAlerts::getAPI()->isDeathHidden($cause)){
				BlackAlerts::getAPI()->setDeathMessage("");
			}else{
				if(BlackAlerts::getAPI()->isDeathCustom($cause)){
					BlackAlerts::getAPI()->setDeathMessage(BlackAlerts::getAPI()->getDefaultDeathMessage($player, $cause));
				}
			}
			$this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsDeathEvent($player, $cause));
			$event->setDeathMessage(BlackAlerts::getAPI()->getDeathMessage());
		}
	}

}