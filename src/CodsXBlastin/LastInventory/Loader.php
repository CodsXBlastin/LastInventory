<?php

declare(strict_types=1);

namespace CodsXBlastin\LastInventory;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;

class Loader extends PluginBase
{
    static private $instance = null;

    static public $data;

    public function onEnable()
    {
        self::$instance = $this;
        @mkdir($this->getDataFolder());
		self::$data = new Config($this->getDataFolder() . "data.json", Config::JSON, []);
        $cm = $this->getServer()->getCommandMap();
        $cm->register("lastinventory", new class extends PluginCommand {
            public function __construct()
            {
                parent::__construct("lastinventory", Loader::getInstance());
                $this->setDescription("LastInventory Root Command");
                $this->setPermission("lastinventory.command");
                $this->setUsage("§r§c/{$this->getLabel()} <player: string>");
                $this->setAliases(["lastinv", "li", "revive"]);
            }

            public function execute(CommandSender $sender, string $commandLabel, array $args)
            {
                if ($sender->hasPermission("lastinventory.command") || $sender->isOp()) {
                    if (!isset($args[0])) {
                        $sender->sendMessage($this->getUsage());
                        return;
                    }
                    if ($player == null) {
                        $sender->sendMessage($this->getUsage());
                        return;
                    } else {
	            $player = Server::getInstance()->getPlayer($args[0]);
                    Loader::revive($player);
                    $sender->sendMessage("§r§aRestored {$player->getName()}'s Last Inventory");
                    $player->sendMessage("§r§aYour Last Inventory was Restored.");
                    }
                }
            }
        });
        $pm = $this->getServer()->getPluginManager();
        $pm->registerEvents(new class implements Listener {
            public function onDeath(PlayerDeathEvent $event)
            {
                $player = $event->getPlayer();
                $armors = $items = [];
                foreach ($player->getArmorInventory()->getContents() as $slot => $armor) {
                    $armors[$slot] = $armor->jsonSerialize();
                }
                foreach ($player->getInventory()->getContents() as $slot => $item) {
                    $items[$slot] = $item->jsonSerialize();
                }
                Loader::$data->set($player->getName(), [
                    "armor" => $armors,
                    "items" => $items,
                ]);
                Loader::$data->save();
            }
        }, $this);
    }

    static public function revive(Player $player): bool
    {
        if (self::$data->exists($player->getName())) {
            $data = self::$data->get($player->getName());
            $inventory = $player->getArmorInventory();
            foreach ($data["armor"] as $slot => $serializedArmor) {
                $inventory->setItem($slot, Item::jsonDeserialize($serializedArmor));
            }
            $inventory->sendContents($inventory->getViewers());
            $inventory = $player->getInventory();
            foreach ($data["items"] as $slot => $serializedItem) {
                $inventory->setItem($slot, Item::jsonDeserialize($serializedItem));
            }
            $inventory->sendContents($inventory->getViewers());
            return true;
        }
        return false;
    }

    static public function getInstance(): self
    {
        return self::$instance;
    }
}
