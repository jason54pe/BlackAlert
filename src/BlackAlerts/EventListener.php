<?php

/*
 * BlackAlerts (v1.6) by EvolSoft
 * Developer: EvolSoft (Flavius12)
 * Website: http://www.evolsoft.tk
 * Date: 28/05/2015 04:29 PM (UTC)
 * Copyright & License: (C) 2014-2015 EvolSoft
 * Licensed under MIT (https://github.com/EvolSoft/BlackAlerts/blob/master/LICENSE)
 */

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

use BlackAlerts\Events\BlackAlertsDeathEvent;
use BlackAlerts\Events\BlackAlertsFullServerKickEvent;
use BlackAlerts\Events\BlackAlertsJoinEvent;
use BlackAlerts\Events\BlackAlertsMotdUpdateEvent;
use BlackAlerts\Events\BlackAlertsOutdatedClientKickEvent;
use BlackAlerts\Events\BlackAlertsOutdatedServerKickEvent;
use BlackAlerts\Events\BlackAlertsQuitEvent;
use BlackAlerts\Events\BlackAlertsWhitelistKickEvent;
use BlackAlerts\Events\BlackAlertsWorldChangeEvent;

class EventListener implements Listener{

	public function __construct(BlackAlerts $plugin){
		$this->plugin = $plugin;
	}

	public function onReceivePacket(DataPacketReceiveEvent $event){
		$player = $event->getPlayer();
		$packet = $event->getPacket();
		if($packet instanceof LoginPacket){
			if($packet->protocol < ProtocolInfo::CURRENT_PROTOCOL){
				//Check if outdated client message is custom
				if(BlackAlerts::getAPI()->isOutdatedClientMessageCustom()){
					BlackAlerts::getAPI()->setOutdatedClientMessage(BlackAlerts::getAPI()->getDefaultOutdatedClientMessage($player));
				}
				//Outdated Client Kick Event
				$this->plugin->getServer()->getPluginManager()->callEvent(new BlackAlertsOutdatedClientKickEvent($player));
				//Check if Outdated Client message is not empty
				if(BlackAlerts::getAPI()->getOutdatedClientMessage() != null){
					$player->close("", BlackAlerts::getAPI()->getOutdatedClientMessage());
					$event->setCancelled(true);
				}
			}elseif($packet->protocol > ProtocolInfo::CURRENT_PROTOCOL){
				//Check if outdated server message is custom
				if(BlackAlerts::getAPI()->isOutdatedServerMessageCustom()){
					BlackAlerts::getAPI()->setOutdatedServerMessage(BlackAlerts::getAPI()->getDefaultOutdatedServerMessage($player));
				}
				//Outdated Server Kick Event
				$this->plugin->getServer()->getPluginManager()->callEvent(new BlackAlertsOutdatedServerKickEvent($player));
				//Check if Outdated Server message is not empty
				if(BlackAlerts::getAPI()->getOutdatedServerMessage() != null){
					$player->close("", BlackAlerts::getAPI()->getOutdatedServerMessage());
					$event->setCancelled(true);
				}
			}
		}
	}

	/**
	 * @param PlayerPreLoginEvent $event
	 *
	 * @priority HIGHEST
	 */
	public function onPlayerPreLogin(PlayerPreLoginEvent $event){
		$player = $event->getPlayer();
		if(count($this->plugin->getServer()->getOnlinePlayers()) - 1 < $this->plugin->getServer()->getMaxPlayers()){
			if(!$this->plugin->getServer()->isWhitelisted($event->getPlayer()->getName())){
				//Check if Whitelist message is custom
				if(BlackAlerts::getAPI()->isWhitelistMessageCustom()){
					BlackAlerts::getAPI()->setWhitelistMessage(BlackAlerts::getAPI()->getDefaultWhitelistMessage($player));
				}
				//Whitelist Kick Event
				$this->plugin->getServer()->getPluginManager()->callEvent(new BlackAlertsWhitelistKickEvent($player));
				//Check if Whitelist message is not empty
				if(BlackAlerts::getAPI()->getWhitelistMessage() != null){
					$player->close("", BlackAlerts::getAPI()->getWhitelistMessage());
					$event->setCancelled(true);
				}
			}
		}else{
			//Check if Full Server message is custom
			if(BlackAlerts::getAPI()->isFullServerMessageCustom()){
				BlackAlerts::getAPI()->setFullServerMessage(BlackAlerts::getAPI()->getDefaultFullServerMessage($player));
			}
			//Full Server Kick Event
			$this->plugin->getServer()->getPluginManager()->callEvent(new BlackAlertsFullServerKickEvent($player));
			//Check if Full Server message is not empty
			if(BlackAlerts::getAPI()->getFullServerMessage() != null){
				$player->close("", BlackAlerts::getAPI()->getFullServerMessage());
				$event->setCancelled(true);
			}
		}
	}

	/**
	 * @param PlayerJoinEvent $event
	 *
	 * @priority HIGHEST
	 */
	public function onPlayerJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		//Motd Update
		//Check if Motd message is custom
		if(BlackAlerts::getAPI()->isMotdCustom()){
			BlackAlerts::getAPI()->setMotdMessage(BlackAlerts::getAPI()->getDefaultMotdMessage());
		}else{
			BlackAlerts::getAPI()->setMotdMessage($this->plugin->translateColors("&", $this->plugin->getServer()->getMotd()));
		}
		//Motd Update Event
		$this->plugin->getServer()->getPluginManager()->callEvent(new BlackAlertsMotdUpdateEvent($this->plugin->getServer()->getMotd()));
		$this->plugin->getServer()->getNetwork()->setName(BlackAlerts::getAPI()->getMotdMessage());
		//Join Message
		$status = 0;
		BlackAlerts::getAPI()->setJoinMessage($event->getJoinMessage());
		//Get First Join
		if(BlackAlerts::getAPI()->hasJoinedFirstTime($player)){
			//Register FirstJoin
			BlackAlerts::getAPI()->registerFirstJoin($player);
			//Check if FirstJoin message is enabled
			if(BlackAlerts::getAPI()->isDefaultFirstJoinMessageEnabled()){
				BlackAlerts::getAPI()->setJoinMessage(BlackAlerts::getAPI()->getDefaultFirstJoinMessage($player));
				$status = 1;
			}
		}
		//Default Join Message
		if($status == 0){
			//Check if Join message is hidden
			if(BlackAlerts::getAPI()->isDefaultJoinMessageHidden()){
				BlackAlerts::getAPI()->setJoinMessage("");
			}else{
				//Check if Join message is custom
				if(BlackAlerts::getAPI()->isDefaultJoinMessageCustom()){
					BlackAlerts::getAPI()->setJoinMessage(BlackAlerts::getAPI()->getDefaultJoinMessage($player));
				}
			}
		}
		//Join Event
		$this->plugin->getServer()->getPluginManager()->callEvent(new BlackAlertsJoinEvent($player, $event->getJoinMessage()));
		$event->setJoinMessage(BlackAlerts::getAPI()->getJoinMessage());
	}

	/**
	 * @param PlayerQuitEvent $event
	 *
	 * @priority HIGHEST
	 */
	public function onPlayerQuit(PlayerQuitEvent $event){
		$player = $event->getPlayer();
		//Motd Update
		if(BlackAlerts::getAPI()->isMotdCustom()){
			BlackAlerts::getAPI()->setMotdMessage(BlackAlerts::getAPI()->getDefaultMotdMessage());
		}else{
			BlackAlerts::getAPI()->setMotdMessage($this->plugin->translateColors("&", $this->plugin->getServer()->getMotd()));
		}
		//Motd Update Event
		$this->plugin->getServer()->getPluginManager()->callEvent(new BlackAlertsMotdUpdateEvent($this->plugin->getServer()->getMotd()));
		$this->plugin->getServer()->getNetwork()->setName(BlackAlerts::getAPI()->getMotdMessage());
		BlackAlerts::getAPI()->setQuitMessage($event->getQuitMessage());
		//Check if Quit message is hidden
		if(BlackAlerts::getAPI()->isQuitHidden()){
			BlackAlerts::getAPI()->setQuitMessage("");
		}else{
			//Check if Quit message is custom
			if(BlackAlerts::getAPI()->isQuitCustom()){
				BlackAlerts::getAPI()->setQuitMessage(BlackAlerts::getAPI()->getDefaultQuitMessage($player));
			}
		}
		//Quit Event
		$this->plugin->getServer()->getPluginManager()->callEvent(new BlackAlertsQuitEvent($player, $event->getQuitMessage()));
		$event->setQuitMessage(BlackAlerts::getAPI()->getQuitMessage());
	}

	public function onWorldChange(EntityLevelChangeEvent $event){
		$entity = $event->getEntity();
		BlackAlerts::getAPI()->setWorldChangeMessage("");
		//Check if the Entity is a Player
		if($entity instanceof Player){
			$player = $entity;
			$origin = $event->getOrigin();
			$target = $event->getTarget();
			//Check if Default WorldChange Message is enabled
			if(BlackAlerts::getAPI()->isDefaultWorldChangeMessageEnabled()){
				BlackAlerts::getAPI()->setWorldChangeMessage(BlackAlerts::getAPI()->getDefaultWorldChangeMessage($player, $origin, $target));
			}
			//WorldChange Event
			$this->plugin->getServer()->getPluginManager()->callEvent(new BlackAlertsWorldChangeEvent($player, $origin, $target));
			if(BlackAlerts::getAPI()->getWorldChangeMessage() != ""){
				Server::getInstance()->broadcastMessage(BlackAlerts::getAPI()->getWorldChangeMessage());
			}
		}
	}


	/**
	 * @param PlayerDeathEvent $event
	 *
	 * @priority HIGHEST
	 */
	public function onPlayerDeath(PlayerDeathEvent $event){
		$player = $event->getEntity();
		BlackAlerts::getAPI()->setDeathMessage($event->getDeathMessage());
		if($player instanceof Player){
			$cause = $player->getLastDamageCause();
			if(BlackAlerts::getAPI()->isDeathHidden($cause)){
				BlackAlerts::getAPI()->setDeathMessage("");
			}else{
				//Check if Death message is custom
				if(BlackAlerts::getAPI()->isDeathCustom($cause)){
					BlackAlerts::getAPI()->setDeathMessage(BlackAlerts::getAPI()->getDefaultDeathMessage($player, $cause));
				}
			}
			//Death Event
			$this->plugin->getServer()->getPluginManager()->callEvent(new BlackAlertsDeathEvent($player, $cause));
			$event->setDeathMessage(BlackAlerts::getAPI()->getDeathMessage());
		}
	}

}