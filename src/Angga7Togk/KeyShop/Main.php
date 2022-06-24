<?php
declare(strict_types=1);

namespace Angga7Togk\KeyShop;

use pocketmine\Server;
use pocketmine\player\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\command\Command; 
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\utils\Config;

use Angga7Togk\KeyShop\Form\SimpleForm;

use onebone\economyapi\EconomyAPI;

class Main extends PluginBase {
    
    public Config $config;
    public $eco;
    
    public function onEnable() : void {
      $this->getServer()->getPluginManager()->registerEvents($this, $this);
      $this->saveDefaultConfig();
      $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
      $this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
    }
  
    public function onCommand(CommandSender $sender, Command $cmd, String $label, Array $args) : bool {
    
      if($cmd->getName() == "keyshop"){
          $this->KeyShopMenu($sender);
      }
    
      return true;
    }
  
    public function KeyShopMenu($player){
      $money = $this->eco->myMoney($player);
      $form = new SimpleForm(function(Player $player, int $data = null){
        if($data === null){
          return true;
        }
        if($money >= $this->config->get($data)["Key"]["Price"]) {
            $this->eco->reduceMoney($player, $this->config->get($data)["Key"]["Price"]);
            $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandsender($this->getServer(), $this->getServer()->getLanguage()), "key " . $this->config->get($data)["Key"]["Name"] . " 1 " . $player->getName());
            $player->sendMessage("§aSuccesfully buy a key");
        } else {
            $player->sendMessage("§cFailed buy a key");
        }
      });
      $form->setTitle("§l7TogkKeyShop");
      $form->setContent(">> §eHi, §a" . $player->getName() . "\n>> §eYour Money §a" . $money . "$"); 
      $form->addButton("§l§cExit\n§rTap To Exit", 0, "textures/ui/cancel");
      for($i = 1;$i <= 10;$i++){
          if($this->config->exists($i)){
              $form->addButton($this->config->get($i)["Button"]["Name"] . "\n§r" . $this->config->get($i)["Button"]["Sub-Name"]);
          }
      }
      $form->sendToPlayer($player);
      return $form;
    
    }
  
}
