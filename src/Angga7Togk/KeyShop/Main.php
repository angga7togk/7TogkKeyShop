<?php
declare(strict_types=1);

namespace Angga7Togk\KeyShop;

use pocketmine\Server;
use pocketmine\player\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\command\Command; 
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\Config;

use Angga7Togk\KeyShop\Form\SimpleForm;

use onebone\economyapi\EconomyAPI;

class Main extends PluginBase implements Listener {
    public function onEnable() : void {
      $this->getServer()->getPluginManager()->registerEvents($this, $this);
      $this->saveResource("config.yml");
      $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
    }
  
    public function onCommand(CommandSender $sender, Command $cmd, String $label, Array $args) : bool {
    
      if($cmd->getName() == "keyshop"){
          $this->KeyShopMenu($sender);
      }
    
      return true;
    }
  
    public function KeyShopMenu($player){
      $form = new SimpleForm(function(Player $player, int $data = null){
        if($data === null){
          return true;
        }
        
        if($data === 0){
          return true;
        }
    
        $money = EconomyAPI::getInstance()->myMoney($player);
        if($money >= $this->config->get($data)["Key"]["Price"]){
              EconomyAPI::getInstance()->reduceMoney($player, $this->config->get($data)["Key"]["Price"]);
              $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandsender($this->getServer(), $this->getServer()->getLanguage()), "key " . $this->config->get($data)["Key"]["Name"] . " 1 \"".$player->getName()."\"");
              $player->sendMessage($this->config->get($data)["Message"]["Succes"]);
            } else {
              $player->sendMessage($this->config->get($data)["Message"]["Failed"]);
            }
      });
      $mymoney = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->myMoney($player);
      $form->setTitle($this->config->get("Title"));
      $form->setContent("§g>> §eHi, §b" . $player->getName() . "\n§g>> §eYour Balance §a" . $mymoney);
      $form->addButton("§l§cExit\n§r§8Tap To Exit", 0, "textures/ui/cancel");
      for($i = 1;$i <= 100;$i++){
          if($this->config->exists($i)){
              $form->addButton($this->config->get($i)["Button"]["Name"] . "\n§rPrice : " . $this->config->get($i)["Key"]["Price"], 0, "textures/blocks/trip_wire_source");
          }
      }
      $form->sendToPlayer($player);
      return $form;
    
    }
  
}
