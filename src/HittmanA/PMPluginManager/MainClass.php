<?php

namespace HittmanA\PMPluginManager;
use pocketmine\event;
use pocketmine\Server;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\utils\Utils;
use pocketmine\utils\Config;
use pocketmine\command\ConsoleCommandSender;

use HittmanA\PMPluginManager\Plugin;

class MainClass extends PluginBase implements Listener 
{
    public function onEnable()
        {
            @mkdir($this->getDataFolder());
            $this->pluginInfo = new Config($this->getDataFolder() . "pluginInformation.json", Config::JSON, []);
            $this->getLogger()->info("PMPM enabled v2.0.5");
            $this->getServer()->getPluginManager()->registerEvents($this, $this);
        }
    public function onDisable()
        {
            $this->getLogger()->info("PMPM disabled v2.0.5");
        }
    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){ 
        if($cmd->getName() == "download"){
            if(count($args) != 0) {
                $pluginToDownload = $args[0];
                if(!isset($this->pluginInfo->$pluginToDownload)) {
                    $sender->sendMessage("Looking for plugin '".$pluginToDownload."' in the Poggit registry");
                    $plugin = new Plugin($pluginToDownload, $sender);
                    if($plugin->ready_to_download == true) {
                        set_time_limit(0);
                        $fp = fopen ("./plugins/$pluginToDownload.phar", 'w+');
                        $code=Utils::getURL($plugin->download_url);
                        fwrite($fp, $code);
                        fclose($fp);
                        $sender->sendMessage($pluginToDownload." has been installed successfully!");
                        
                        $this->pluginInfo->set($pluginToDownload,[
                                                "name" => $pluginToDownload,
                                                "version" => $plugin->version,
                                                "download_url" => $plugin->download_url,
                                                "autoUpdateToLatest" => "true"
                                                ]);
                        $this->pluginInfo->save(true);
                        
                        $sender->sendMessage("Reloading server...");
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender(), 'reload');
                        
                        return true; 
                    }else{
                        return true;
                    }
                }else{
                    $sender->sendMessage("You already have that plugin installed!");
                    return true;
                }
            }
        }elseif($cmd->getName() == "remove"){
            if(count($args) != 0) {
                $pluginToRemove = $args[0];
                if(isset($this->pluginInfo->$pluginToRemove)) {
                    $success = unlink("./plugins/".$pluginToRemove.".phar");
                    $this->pluginInfo->remove($pluginToRemove);
                    if($success) {
                        $sender->sendMessage("Plugin successfully deleted! Reloading server...");
                        $this->pluginInfo->save(true);
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender(), 'reload');
                    }else{
                        $sender->sendMessage("Plugin was not able to be deleted...");
                    }
                    return true;
                }else{
                    $sender->sendMessage("That plugin isn't installed...");
                    return true;
                }
            }
        }elseif($cmd->getName() == "__UpdateSelf") {
            $this->getServer()->dispatchCommand(new ConsoleCommandSender(), 'remove PMPM');
            $this->getServer()->dispatchCommand(new ConsoleCommandSender(), 'download PMPM');
        }
    }
}
