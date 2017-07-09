<?php

namespace HittmanA\pmpm;

use pocketmine\event;
use pocketmine\Server;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\utils\Utils;
use pocketmine\utils\Config;

class Plugin
{
    function __construct($name, $sender)
    {
        $this->name = $name;
        set_time_limit(0);
        $url = "https://poggit.pmmp.io/releases.json?name=".$this->name;
        $register = Utils::getURL($url);
        $JSONParsedRegister = json_decode($register);
        if(count($JSONParsedRegister) != 0) {
            $sender->sendMessage("'".$name."' was found on Poggit. Downloading now...");
            $latestVersion = $JSONParsedRegister[0];
            $pluginVersion = $latestVersion->version;
            $this->version = $pluginVersion;
            $sender->sendMessage("Found the requested version. Downloading version ".$this->version."...");
            $poggitURL = $latestVersion->artifact_url;
            $this->url = $poggitURL;
            $pluginName = $latestVersion->project_name;
            $this->name = $pluginName;
            $this->download_url = $poggitURL."/".$pluginName.".phar";
            $this->ready_to_download = true;
        }else{
            $sender->sendMessage("Sorry but '".$name."' isn't listed on Poggit!");
            $this->ready_to_download = false;
        }
    }
}