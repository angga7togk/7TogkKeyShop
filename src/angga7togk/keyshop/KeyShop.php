<?php

namespace angga7togk\keyshop;

use jojoe77777\FormAPI\SimpleForm;
use onebone\economyapi\EconomyAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class KeyShop extends PluginBase
{
    public $config;
    public int $i;
    const prefix = TF::GOLD."[KeyShop] ".TF::RESET;
    public function onEnable(): void
    {
        $this->saveResource("config.yml");
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
    }

    /** @param CommandSender $sender
	 * @param Command $cmd
	 * @param string $label
	 * @param array $args
	 */
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool
    {   
        if($sender instanceof ConsoleCommandSender){
            $sender->sendMessage(self::prefix.TF::RED."please use command in game!");
            return false;
        }
        if ($cmd->getName() == "keyshop") {
            $this->KeyShopMenu($sender);
        }
        return true;
    }

    public function KeyShopMenu($player)
    {
        $form = new SimpleForm(function (Player $player, $data) {
            if ($data === null || $data === 0) {
                return true;
            }
            $money = EconomyAPI::getInstance()->myMoney($player);
            if ($money >= $this->config->get($data)["Key"]["Price"]) {
                EconomyAPI::getInstance()->reduceMoney($player, $this->config->get($data)["Key"]["Price"]);
                $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandsender($this->getServer(), $this->getServer()->getLanguage()), "key " . $this->config->get($data)["Key"]["Name"] . " 1 \"" . $player->getName() . "\"");
                $player->sendMessage(self::prefix.$this->config->get($data)["Message"]["Succes"]);
            } else {
                $player->sendMessage(self::prefix.$this->config->get($data)["Message"]["Failed"]);
            }
        });
        $mymoney = EconomyAPI::getInstance()->myMoney($player);
        $form->setTitle($this->config->get("Title"));
        $form->setContent("§g>> §eHi, §b" . $player->getName() . "\n§g>> §eYour Balance §a" . $mymoney);
        $form->addButton("§l§cExit\n§r§8Tap To Exit", 0, "textures/ui/cancel");
        for ($i = 1; $i <= 100; $i++) {
            if ($this->config->exists("$i")) {
                $form->addButton($this->config->get("$i")["Button"]["Name"] . "\n§rPrice : " . $this->config->get("$i")["Key"]["Price"], 0, "textures/blocks/trip_wire_source");
            }
        }
        $player->sendForm($form);
    }

}
