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

class MainClass extends PluginBase implements Listener 
{
    public function onEnable()
        {
        $this->getLogger()->info("PMPM enabled v1.0.0");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        }
    public function onDisable()
        {
        $this->getLogger()->info("PMPM disabled v1.0.0");
        }
public function onCommand(CommandSender $sender,Command $cmd,$label,array $args){ 
    if($cmd->getName() == "download"){ 
        $plugin = $args[0];
        $sender->sendMessage("Downloading plugin ".$args[0]."...");
        set_time_limit(0);
        $url = "https://pmpm-hittmana.c9users.io/plugins/download/$plugin.zip";
        //This is the file where we save the    information
        $fp = fopen ("$plugin.zip", 'w+');
        //Here is the file we are downloading, replace spaces with %20
        $code=Utils::getURL($url);
        fwrite($fp, $code);
        fclose($fp);
        $zip = new \ZipArchive();
        $res = $zip->open("$plugin.zip");
        if ($res === TRUE) {
            $zip->extractTo("plugins/");
            $zip->close();
            //unlink("$plugin.zip");
            $sender->sendMessage($plugin." has been installed successfully!"); 
            $sender->sendMessage("Reloading server...");
        } else {
            $sender->sendMessage($plugin." has failed to install!"); 
        }
        return true; 
    }
}
}
