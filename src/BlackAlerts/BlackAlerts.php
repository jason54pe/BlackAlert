<?php

namespace BlackAlerts;

use pocketmine\Player;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

use BlackAlerts\Commands\Commands;
use BlackAlerts\Events\CustomAlertsMotdUpdateEvent;

class BlackAlerts extends PluginBase {
    
	const PREFIX = "&b[&4Black&cAlerts&b] ";
	
	const API_VERSION = "1.0.0";
	
	private $cfg;
	
	private static $instance = null;
	
	public function onLoad(){
	    if(!self::$instance instanceof BlackAlerts){
	        self::$instance = $this;
	    }
	}
	
    public function onEnable(){
    	@mkdir($this->getDataFolder());
    	$this->saveDefaultConfig();
    	$this->cfg = $this->getConfig()->getAll();
    	$this->getCommand("customalerts")->setExecutor(new Commands($this));
    	$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    	$this->getScheduler()->scheduleRepeatingTask(new MotdTask($this), 20);
    }

    public function replaceVars($str, array $vars){
        foreach($vars as $key => $value){
            $str = str_replace("{" . $key . "}", $value, $str);
        }
        return $str;
    }

    public static function getAPI(){
        return self::$instance;
    }

    public function getVersion(){
    	return $this->getVersion();
    }

    public function getAPIVersion(){
    	return self::API_VERSION;
    }

    public function isMotdCustom() : bool {
    	return $this->cfg["Motd"]["custom"];
    }

    public function getMotdMessage(){
        return TextFormat::colorize($this->replaceVars($this->cfg["Motd"]["message"], array(
            "MAXPLAYERS" => $this->getServer()->getMaxPlayers(),
            "TOTALPLAYERS" => count($this->getServer()->getOnlinePlayers()),
            "TIME" => date($this->cfg["datetime-format"]))));
    }
    
    public function updateMotd(){
        $cevent = new CustomAlertsMotdUpdateEvent();
        if($this->isMotdCustom()){
            $cevent->setMessage($this->getMotdMessage());
        }else{
            $cevent->setMessage($this->getServer()->getMotd());
        }
        $this->getServer()->getPluginManager()->callEvent($cevent);
        $this->getServer()->getNetwork()->setName($cevent->getMessage());
    }

    public function isOutdatedClientMessageCustom() : bool {
    	return $this->cfg["OutdatedClient"]["custom"];
    }

    public function getOutdatedClientMessage(Player $player){
        return TextFormat::colorize($this->replaceVars($this->cfg["OutdatedClient"]["message"], array(
            "PLAYER" => $player->getName(),
            "MAXPLAYERS" => $this->getServer()->getMaxPlayers(),
            "TOTALPLAYERS" => count($this->getServer()->getOnlinePlayers()),
            "TIME" => date($this->cfg["datetime-format"]))));
    }

    public function isOutdatedServerMessageCustom() : bool {
    	return $this->cfg["OutdatedServer"]["custom"];
    }

    public function getOutdatedServerMessage(Player $player){
        return TextFormat::colorize($this->replaceVars($this->cfg["OutdatedServer"]["message"], array(
            "PLAYER" => $player->getName(),
            "MAXPLAYERS" => $this->getServer()->getMaxPlayers(),
            "TOTALPLAYERS" => count($this->getServer()->getOnlinePlayers()),
            "TIME" => date($this->cfg["datetime-format"]))));
    }

    public function isWhitelistMessageCustom() : bool {
    	return $this->cfg["WhitelistedServer"]["custom"];
    }

    public function getWhitelistMessage(Player $player){
        return TextFormat::colorize($this->replaceVars($this->cfg["WhitelistedServer"]["message"], array(
            "PLAYER" => $player->getName(),
            "MAXPLAYERS" => $this->getServer()->getMaxPlayers(),
            "TOTALPLAYERS" => count($this->getServer()->getOnlinePlayers()),
            "TIME" => date($this->cfg["datetime-format"]))));
    }

    public function isFullServerMessageCustom() : bool {
    	$cfg = $this->getConfig()->getAll();
    	return $cfg["FullServer"]["custom"];
    }

    public function getFullServerMessage(Player $player){
        return TextFormat::colorize($this->replaceVars($this->cfg["FullServer"]["message"], array(
            "PLAYER" => $player->getName(),
            "MAXPLAYERS" => $this->getServer()->getMaxPlayers(),
            "TOTALPLAYERS" => count($this->getServer()->getOnlinePlayers()),
            "TIME" => date($this->cfg["datetime-format"]))));
    }

    public function isFirstJoinMessageEnabled() : bool {
    	return $this->cfg["FirstJoin"]["enable"];
    }

    public function getFirstJoinMessage(Player $player){
        return TextFormat::colorize($this->replaceVars($this->cfg["FirstJoin"]["message"], array(
            "PLAYER" => $player->getName(),
            "MAXPLAYERS" => $this->getServer()->getMaxPlayers(),
            "TOTALPLAYERS" => count($this->getServer()->getOnlinePlayers()),
            "TIME" => date($this->cfg["datetime-format"]))));
    }
 
    public function isJoinMessageCustom() : bool {
    	return $this->cfg["Join"]["custom"];
    }

    public function isJoinMessageHidden() : bool {
    	return $this->cfg["Join"]["hide"];
    }

    public function getJoinMessage(Player $player){
        return TextFormat::colorize($this->replaceVars($this->cfg["Join"]["message"], array(
            "PLAYER" => $player->getName(),
            "MAXPLAYERS" => $this->getServer()->getMaxPlayers(),
            "TOTALPLAYERS" => count($this->getServer()->getOnlinePlayers()),
            "TIME" => date($this->cfg["datetime-format"]))));
    }

    public function isQuitMessageCustom(){
    	return $this->cfg["Quit"]["custom"];
    }

    public function isQuitMessageHidden(){
    	return $this->cfg["Quit"]["hide"];
    }

    public function getQuitMessage(Player $player){
        return TextFormat::colorize($this->replaceVars($this->cfg["Quit"]["message"], array(
            "PLAYER" => $player->getName(),
            "MAXPLAYERS" => $this->getServer()->getMaxPlayers(),
            "TOTALPLAYERS" => count($this->getServer()->getOnlinePlayers()),
            "TIME" => date($this->cfg["datetime-format"]))));
    }

    public function isWorldChangeMessageEnabled(){
    	return $this->cfg["WorldChange"]["enable"];
    }

    public function getWorldChangeMessage(Player $player, Level $origin, Level $target){
        return TextFormat::colorize($this->replaceVars($this->cfg["WorldChange"]["message"], array(
    	    "ORIGIN" => $origin->getName(),
    	    "TARGET" => $target->getName(),
    	    "PLAYER" => $player->getName(),
    	    "MAXPLAYERS" => $this->getServer()->getMaxPlayers(),
    	    "TOTALPLAYERS" => count($this->getServer()->getOnlinePlayers()),
    	    "TIME" => date($this->cfg["datetime-format"]))));
    }

    public function isDeathMessageCustom(EntityDamageEvent $cause = null){
        if(!$cause){
            return $this->cfg["Death"]["custom"];
        }
        switch($cause->getCause()){
            case EntityDamageEvent::CAUSE_CONTACT:
                return $this->cfg["Death"]["death-contact-message"]["custom"];
            case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
                return $this->cfg["Death"]["kill-message"]["custom"];
            case EntityDamageEvent::CAUSE_PROJECTILE:
                return $this->cfg["Death"]["death-projectile-message"]["custom"];
            case EntityDamageEvent::CAUSE_SUFFOCATION:
                return $this->cfg["Death"]["death-suffocation-message"]["custom"];
            case EntityDamageEvent::CAUSE_FALL:
                return $this->cfg["Death"]["death-fall-message"]["custom"];
            case EntityDamageEvent::CAUSE_FIRE:
                return $this->cfg["Death"]["death-fire-message"]["custom"];
            case EntityDamageEvent::CAUSE_FIRE_TICK:
                return $this->cfg["Death"]["death-on-fire-message"]["custom"];
            case EntityDamageEvent::CAUSE_LAVA:
                return $this->cfg["Death"]["death-lava-message"]["custom"];
            case EntityDamageEvent::CAUSE_DROWNING:
                return $this->cfg["Death"]["death-drowning-message"]["custom"];
            case EntityDamageEvent::CAUSE_ENTITY_EXPLOSION:
            case EntityDamageEvent::CAUSE_BLOCK_EXPLOSION:
                return $this->cfg["Death"]["death-explosion-message"]["custom"];
            case EntityDamageEvent::CAUSE_VOID:
                return $this->cfg["Death"]["death-void-message"]["custom"];
            case EntityDamageEvent::CAUSE_SUICIDE:
                return $this->cfg["Death"]["death-suicide-message"]["custom"];
            case EntityDamageEvent::CAUSE_MAGIC:
                return $this->cfg["Death"]["death-magic-message"]["custom"];
            default:
                return $this->cfg["Death"]["custom"];
        }
    }

    public function isDeathMessageHidden(EntityDamageEvent $cause = null){
        if(!$cause){
            return $this->cfg["Death"]["hide"];
        }
        switch($cause->getCause()){
            case EntityDamageEvent::CAUSE_CONTACT:
                return $this->cfg["Death"]["death-contact-message"]["hide"];
            case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
                return $this->cfg["Death"]["kill-message"]["hide"];
            case EntityDamageEvent::CAUSE_PROJECTILE:
                return $this->cfg["Death"]["death-projectile-message"]["hide"];
            case EntityDamageEvent::CAUSE_SUFFOCATION:
                return $this->cfg["Death"]["death-suffocation-message"]["hide"];
            case EntityDamageEvent::CAUSE_FALL:
                return $this->cfg["Death"]["death-fall-message"]["hide"];
            case EntityDamageEvent::CAUSE_FIRE:
                return $this->cfg["Death"]["death-fire-message"]["hide"];
            case EntityDamageEvent::CAUSE_FIRE_TICK:
                return $this->cfg["Death"]["death-on-fire-message"]["hide"];
            case EntityDamageEvent::CAUSE_LAVA:
                return $this->cfg["Death"]["death-lava-message"]["hide"];
            case EntityDamageEvent::CAUSE_DROWNING:
                return $this->cfg["Death"]["death-drowning-message"]["hide"];
            case EntityDamageEvent::CAUSE_ENTITY_EXPLOSION:
            case EntityDamageEvent::CAUSE_BLOCK_EXPLOSION:
                return $this->cfg["Death"]["death-explosion-message"]["hide"];
            case EntityDamageEvent::CAUSE_VOID:
                return $this->cfg["Death"]["death-void-message"]["hide"];
            case EntityDamageEvent::CAUSE_SUICIDE:
                return $this->cfg["Death"]["death-suicide-message"]["hide"];
            case EntityDamageEvent::CAUSE_MAGIC:
                return $this->cfg["Death"]["death-magic-message"]["hide"];
            default:
                return $this->cfg["Death"]["hide"];
        }
    }

    public function getDeathMessage(Player $player, EntityDamageEvent $cause = null){
        $array = array(
            "PLAYER" => $player->getName(),
            "MAXPLAYERS" => $this->getServer()->getMaxPlayers(),
            "TOTALPLAYERS" => count($this->getServer()->getOnlinePlayers()),
            "TIME" => date($this->cfg["datetime-format"]));
        if(!$cause){
            $message = $this->cfg["Death"]["message"];
        }else{
            switch($cause->getCause()){
                case EntityDamageEvent::CAUSE_CONTACT:
                    $message = $this->cfg["Death"]["death-contact-message"]["message"];
                    if($cause instanceof EntityDamageByBlockEvent){
                        $array["BLOCK"] = $cause->getDamager()->getName();
                        break;
                    }
                    $array["BLOCK"] = "Unknown";
                    break;
                case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
                    $message = $this->cfg["Death"]["kill-message"]["message"];
                    $killer = $cause->getDamager();
                    if($killer instanceof Living){
                        $array["KILLER"] = $killer->getName();
                        break;
                    }
                    $array["KILLER"] = "Unknown";
                    break;
                case EntityDamageEvent::CAUSE_PROJECTILE:
                    $message = $this->cfg["Death"]["death-projectile-message"]["message"];
                    $killer = $cause->getDamager();
                    if($killer instanceof Living){
                        $array["KILLER"] = $killer->getName();
                        break;
                    }
                    $array["KILLER"] = "Unknown";
                    break;
                case EntityDamageEvent::CAUSE_SUFFOCATION:
                    $message = $this->cfg["Death"]["death-suffocation-message"]["message"];
                    break;
                case EntityDamageEvent::CAUSE_FALL:
                    $message = $this->cfg["Death"]["death-fall-message"]["message"];
                    break;
                case EntityDamageEvent::CAUSE_FIRE:
                    $message = $this->cfg["Death"]["death-fire-message"]["message"];
                    break;
                case EntityDamageEvent::CAUSE_FIRE_TICK:
                    $message = $this->cfg["Death"]["death-on-fire-message"]["message"];
                    break;
                case EntityDamageEvent::CAUSE_LAVA:
                    $message = $this->cfg["Death"]["death-lava-message"]["message"];
                    break;
                case EntityDamageEvent::CAUSE_DROWNING:
                    $message = $this->cfg["Death"]["death-drowning-message"]["message"];
                    break;
                case EntityDamageEvent::CAUSE_ENTITY_EXPLOSION:
                case EntityDamageEvent::CAUSE_BLOCK_EXPLOSION:
                    $message = $this->cfg["Death"]["death-explosion-message"]["message"];
                    break;
                case EntityDamageEvent::CAUSE_VOID:
                    $message = $this->cfg["Death"]["death-void-message"]["message"];
                    break;
                case EntityDamageEvent::CAUSE_SUICIDE:
                    $message = $this->cfg["Death"]["death-suicide-message"]["message"];
                    break;
                case EntityDamageEvent::CAUSE_MAGIC:
                    $message = $this->cfg["Death"]["death-magic-message"]["message"];
                    break;
                default:
                    $message = $this->cfg["Death"]["message"];
                    break;
            }
        }
        return TextFormat::colorize($this->replaceVars($message, $array));
    }
}
