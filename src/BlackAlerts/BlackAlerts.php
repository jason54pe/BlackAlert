<?php
namespace BlackAlerts;

use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class BlackAlerts extends PluginBase{
const PRODUCER = "BlackTeam";

	const VERSION = "1.1";
	const PREFIX = "&b[&aBlack&cAlerts&b] ";

	private $message_outdated_client;

	private $message_outdated_server;

	private $message_whitelist;

	private $message_fullserver;

	private $message_join;

	private $message_quit;

	private $message_world_change;

	private $message_death;

	private static $instance = null;
	
	public static function getAPI(){
		return self::$instance;
	}

	public function onLoad(){
		if(!self::$instance instanceof BlackAlerts){
			self::$instance = $this;
		}
	}

	public function translateColors($symbol, $message){

		$message = str_replace($symbol . "0", TextFormat::BLACK, $message);
		$message = str_replace($symbol . "1", TextFormat::DARK_BLUE, $message);
		$message = str_replace($symbol . "2", TextFormat::DARK_GREEN, $message);
		$message = str_replace($symbol . "3", TextFormat::DARK_AQUA, $message);
		$message = str_replace($symbol . "4", TextFormat::DARK_RED, $message);
		$message = str_replace($symbol . "5", TextFormat::DARK_PURPLE, $message);
		$message = str_replace($symbol . "6", TextFormat::GOLD, $message);
		$message = str_replace($symbol . "7", TextFormat::GRAY, $message);
		$message = str_replace($symbol . "8", TextFormat::DARK_GRAY, $message);
		$message = str_replace($symbol . "9", TextFormat::BLUE, $message);
		$message = str_replace($symbol . "a", TextFormat::GREEN, $message);
		$message = str_replace($symbol . "b", TextFormat::AQUA, $message);
		$message = str_replace($symbol . "c", TextFormat::RED, $message);
		$message = str_replace($symbol . "d", TextFormat::LIGHT_PURPLE, $message);
		$message = str_replace($symbol . "e", TextFormat::YELLOW, $message);
		$message = str_replace($symbol . "f", TextFormat::WHITE, $message);

		$message = str_replace($symbol . "k", TextFormat::OBFUSCATED, $message);
		$message = str_replace($symbol . "l", TextFormat::BOLD, $message);
		$message = str_replace($symbol . "m", TextFormat::STRIKETHROUGH, $message);
		$message = str_replace($symbol . "n", TextFormat::UNDERLINE, $message);
		$message = str_replace($symbol . "o", TextFormat::ITALIC, $message);
		$message = str_replace($symbol . "r", TextFormat::RESET, $message);

		return $message;
	}

	public function onEnable(){
		@mkdir($this->getDataFolder());
		@mkdir($this->getDataFolder() . "data/");
		$this->saveDefaultConfig();
		$this->cfg = $this->getConfig()->getAll();
		$this->getCommand("blackalerts")->setExecutor(new Commands\Commands($this));
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new MotdTask($this), 20);
	}

	const API_VERSION = "1.1";

	public function getVersion() : string{
		return BlackAlerts::VERSION;
	}

	public function getAPIVersion(){
		return BlackAlerts::API_VERSION;
	}

	public function registerExtension(PluginBase $extension, $priority = null){
		Server::getInstance()->getLogger()->warning("Cette fonction est obsolète depuis blackalerts API v1.1");
	}

	public function getAllExtensions($priority = null){
		Server::getInstance()->getLogger()->warning("Cette fonction est obsolète depuis blackalerts API v1.1");
	}

	public function isMotdCustom(){
		$cfg = $this->getConfig()->getAll();
		return $cfg["Motd"]["custom"];
	}

	public function getDefaultMotdMessage(){
		$cfg = $this->getConfig()->getAll();
		$message = $cfg["Motd"]["message"];
		$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
		$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
		$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
		return $this->translateColors("&", $message);
	}

	public function getMotdMessage(){
		return $this->message_motd;
	}

	public function setMotdMessage($message){
		$this->message_motd = $message;
		$this->getServer()->getNetwork()->setName($this->message_motd);
	}

	public function isOutdatedClientMessageCustom(){
		$cfg = $this->getConfig()->getAll();
		return $cfg["OutdatedClient"]["custom"];
	}

	public function getDefaultOutdatedClientMessage(Player $player){
		$cfg = $this->getConfig()->getAll();
		$message = $cfg["OutdatedClient"]["message"];
		$message = str_replace("{PLAYER}", $player->getName(), $message);
		$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
		$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
		$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
		return $this->translateColors("&", $message);
	}

	public function getOutdatedClientMessage(){
		return $this->message_outdated_client;
	}
	
	public function setOutdatedClientMessage($message){
		$this->message_outdated_client = $message;
	}

	public function isOutdatedServerMessageCustom(){
		$cfg = $this->getConfig()->getAll();
		return $cfg["OutdatedServer"]["custom"];
	}

	public function getDefaultOutdatedServerMessage(Player $player){
		$cfg = $this->getConfig()->getAll();
		$message = $cfg["OutdatedServer"]["message"];
		$message = str_replace("{PLAYER}", $player->getName(), $message);
		$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
		$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
		$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
		return $this->translateColors("&", $message);
	}

	public function getOutdatedServerMessage(){
		return $this->message_outdated_server;
	}

	public function setOutdatedServerMessage($message){
		$this->message_outdated_server = $message;
	}

	public function isWhitelistMessageCustom(){
		$cfg = $this->getConfig()->getAll();
		return $cfg["WhitelistedServer"]["custom"];
	}

	public function getDefaultWhitelistMessage(Player $player){
		$cfg = $this->getConfig()->getAll();
		$message = $cfg["WhitelistedServer"]["message"];
		$message = str_replace("{PLAYER}", $player->getName(), $message);
		$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
		$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
		$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
		return $this->translateColors("&", $message);
	}

	public function getWhitelistMessage(){
		return $this->message_whitelist;
	}

	public function setWhitelistMessage($message){
		$this->message_whitelist = $message;
	}

	public function isFullServerMessageCustom(){
		$cfg = $this->getConfig()->getAll();
		return $cfg["FullServer"]["custom"];
	}

	public function getDefaultFullServerMessage(Player $player){
		$cfg = $this->getConfig()->getAll();
		$message = $cfg["FullServer"]["message"];
		$message = str_replace("{PLAYER}", $player->getName(), $message);
		$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
		$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
		$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
		return $this->translateColors("&", $message);
	}

	public function getFullServerMessage(){
		return $this->message_fullserver;
	}

	public function setFullServerMessage($message){
		$this->message_fullserver = $message;
	}


	public function isDefaultFirstJoinMessageEnabled(){
		$cfg = $this->getConfig()->getAll();
		return $cfg["FirstJoin"]["enable"];
	}

	public function getDefaultFirstJoinMessage(Player $player){
		$cfg = $this->getConfig()->getAll();
		$message = $cfg["FirstJoin"]["message"];
		$message = str_replace("{PLAYER}", $player->getName(), $message);
		$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
		$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
		$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
		return $this->translateColors("&", $message);
	}
	public function registerFirstJoin(Player $player){
		$cfg = new Config($this->getDataFolder() . "data/" . strtolower($player->getName() . ".dat"));
		$cfg->save();
	}


	public function hasJoinedFirstTime(Player $player){
		if(file_exists($this->getDataFolder() . "data/" . strtolower($player->getName() . ".dat"))){
			return false;
		}else{
			return true;
		}
	}

	public function isDefaultJoinMessageCustom(){
		$cfg = $this->getConfig()->getAll();
		return $cfg["Join"]["custom"];
	}

	public function isDefaultJoinMessageHidden(){
		$cfg = $this->getConfig()->getAll();
		return $cfg["Join"]["hide"];
	}

	public function getDefaultJoinMessage(Player $player){
		$cfg = $this->getConfig()->getAll();
		$message = $cfg["Join"]["message"];
		$message = str_replace("{PLAYER}", $player->getName(), $message);
		$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
		$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
		$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
		return $this->translateColors("&", $message);
	}

	public function getJoinMessage(){
		return $this->message_join;
	}

	public function setJoinMessage($message){
		$this->message_join = $message;
	}

	public function isQuitCustom(){
		$cfg = $this->getConfig()->getAll();
		return $cfg["Quit"]["custom"];
	}

	public function isQuitHidden(){
		$cfg = $this->getConfig()->getAll();
		return $cfg["Quit"]["hide"];
	}

	public function getDefaultQuitMessage(Player $player){
		$cfg = $this->getConfig()->getAll();
		$message = $cfg["Quit"]["message"];
		$message = str_replace("{PLAYER}", $player->getName(), $message);
		$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
		$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
		$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
		return $this->translateColors("&", $message);
	}

	public function getQuitMessage(){
		return $this->message_quit;
	}

	public function setQuitMessage($message){
		$this->message_quit = $message;
	}

	public function isDefaultWorldChangeMessageEnabled(){
		$cfg = $this->getConfig()->getAll();
		return $cfg["WorldChange"]["enable"];
	}

	public function getDefaultWorldChangeMessage(Player $player, Level $origin, Level $target){
		$cfg = $this->getConfig()->getAll();
		$message = $cfg["WorldChange"]["message"];
		$message = str_replace("{ORIGIN}", $origin->getName(), $message);
		$message = str_replace("{TARGET}", $target->getName(), $message);
		$message = str_replace("{PLAYER}", $player->getName(), $message);
		$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
		$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
		$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
		return $this->translateColors("&", $message);
	}

	public function getWorldChangeMessage(){
		return $this->message_world_change;
	}

	public function setWorldChangeMessage($message){
		$this->message_world_change = $message;
	}

	public function isDeathCustom(EntityDamageEvent $cause = null){
		$cfg = $this->getConfig()->getAll();
		if($cause instanceof EntityDamageEvent){
			if($cause->getCause() == EntityDamageEvent::CAUSE_CONTACT){
				return $cfg["Death"]["death-contact-message"]["custom"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_ATTACK){
				return $cfg["Death"]["kill-message"]["custom"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_PROJECTILE){
				return $cfg["Death"]["death-projectile-message"]["custom"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_SUFFOCATION){
				return $cfg["Death"]["death-suffocation-message"]["custom"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FALL){
				return $cfg["Death"]["death-fall-message"]["custom"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FIRE){
				return $cfg["Death"]["death-fire-message"]["custom"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FIRE_TICK){
				return $cfg["Death"]["death-on-fire-message"]["custom"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_LAVA){
				return $cfg["Death"]["death-lava-message"]["custom"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_DROWNING){
				return $cfg["Death"]["death-drowning-message"]["custom"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_EXPLOSION || $cause->getCause() == EntityDamageEvent::CAUSE_BLOCK_EXPLOSION){
				return $cfg["Death"]["death-explosion-message"]["custom"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_VOID){
				return $cfg["Death"]["death-void-message"]["custom"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_SUICIDE){
				return $cfg["Death"]["death-suicide-message"]["custom"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_MAGIC){
				return $cfg["Death"]["death-magic-message"]["custom"];
			}else{
				return $cfg["Death"]["custom"];
			}
		}else{
			return $cfg["Death"]["custom"];
		}
	}

	public function isDeathHidden(EntityDamageEvent $cause = null){
		$cfg = $this->getConfig()->getAll();
		if($cause instanceof EntityDamageEvent){
			if($cause->getCause() == EntityDamageEvent::CAUSE_CONTACT){
				return $cfg["Death"]["death-contact-message"]["hide"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_ATTACK){
				return $cfg["Death"]["kill-message"]["hide"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_PROJECTILE){
				return $cfg["Death"]["death-projectile-message"]["hide"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_SUFFOCATION){
				return $cfg["Death"]["death-suffocation-message"]["hide"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FALL){
				return $cfg["Death"]["death-fall-message"]["hide"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FIRE){
				return $cfg["Death"]["death-fire-message"]["hide"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FIRE_TICK){
				return $cfg["Death"]["death-on-fire-message"]["hide"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_LAVA){
				return $cfg["Death"]["death-lava-message"]["hide"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_DROWNING){
				return $cfg["Death"]["death-drowning-message"]["hide"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_EXPLOSION || $cause->getCause() == EntityDamageEvent::CAUSE_BLOCK_EXPLOSION){
				return $cfg["Death"]["death-explosion-message"]["hide"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_VOID){
				return $cfg["Death"]["death-void-message"]["hide"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_SUICIDE){
				return $cfg["Death"]["death-suicide-message"]["hide"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_MAGIC){
				return $cfg["Death"]["death-magic-message"]["hide"];
			}else{
				return $cfg["Death"]["hide"];
			}
		}else{
			return $cfg["Death"]["hide"];
		}
	}

	public function getDefaultDeathMessage(Player $player, $cause = null){
		$cfg = $this->getConfig()->getAll();
		if($cause instanceof EntityDamageEvent){
			if($cause->getCause() == EntityDamageEvent::CAUSE_CONTACT){
				$message = $cfg["Death"]["death-contact-message"]["message"];
				if($cause instanceof EntityDamageByBlockEvent){
					$message = str_replace("{BLOCK}", $cause->getDamager()->getName(), $message);
				}else{
					$message = str_replace("{BLOCK}", "Inconnu", $message);
				}
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_ATTACK){
				$message = $cfg["Death"]["kill-message"]["message"];
				$killer = $cause->getDamager();
				if($killer instanceof Living){
					$message = str_replace("{KILLER}", $killer->getName(), $message);
				}else{
					$message = str_replace("{KILLER}", "Inconnu", $message);
				}
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_PROJECTILE){
				$message = $cfg["Death"]["death-projectile-message"]["message"];
				$killer = $cause->getDamager();
				if($killer instanceof Living){
					$message = str_replace("{KILLER}", $killer->getName(), $message);
				}else{
					$message = str_replace("{KILLER}", "Inconnu", $message);
				}
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_SUFFOCATION){
				$message = $cfg["Death"]["death-suffocation-message"]["message"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FALL){
				$message = $cfg["Death"]["death-fall-message"]["message"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FIRE){
				$message = $cfg["Death"]["death-fire-message"]["message"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FIRE_TICK){
				$message = $cfg["Death"]["death-on-fire-message"]["message"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_LAVA){
				$message = $cfg["Death"]["death-lava-message"]["message"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_DROWNING){
				$message = $cfg["Death"]["death-drowning-message"]["message"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_EXPLOSION || $cause->getCause() == EntityDamageEvent::CAUSE_BLOCK_EXPLOSION){
				$message = $cfg["Death"]["death-explosion-message"]["message"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_VOID){
				$message = $cfg["Death"]["death-void-message"]["message"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_SUICIDE){
				$message = $cfg["Death"]["death-suicide-message"]["message"];
			}elseif($cause->getCause() == EntityDamageEvent::CAUSE_MAGIC){
				$message = $cfg["Death"]["death-magic-message"]["message"];
			}else{
				$message = $cfg["Death"]["message"];
			}
		}else{
			$message = $cfg["Death"]["message"];
		}
		$message = str_replace("{PLAYER}", $player->getName(), $message);
		$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
		$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
		$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
		return $this->translateColors("&", $message);
	}

	public function getDeathMessage(){
		return $this->message_death;
	}

	public function setDeathMessage($message){
		$this->message_death = $message;
	}

}
