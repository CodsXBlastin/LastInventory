<?php

declare(strict_types=1);

namespace CodsXBlastin\LastInventory;

use Exception;
use JsonException;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

final class Loader extends PluginBase implements Listener
{
    use SingletonTrait {
        setInstance as private;
        getInstance as private getSingletonInstance;
    }

    public static Config $data;

    final public function onEnable(): void
    {
        @mkdir($this->getDataFolder());

        self::$data = new Config($this->getDataFolder() . "data.json", Config::JSON, []);

        $cm = $this->getServer()->getCommandMap();
        $cm->register("lastinventory", new LastInventoryCommand());

        $pm = $this->getServer()->getPluginManager();
        $pm->registerEvents($this, $this);
    }

    /**
     * @throws JsonException
     */
    final public static function revive(Player $player, Player|CommandSender $reviver): bool
    {
        if (self::$data->exists($player->getName())) {
            $data = self::$data->get($player->getName());
            $armor = $player->getArmorInventory();
            foreach ($data["armor"] as $slot => $serializedArmor) {
                $armor->setItem($slot, Item::jsonDeserialize($serializedArmor));
            }
            $inventory = $player->getInventory();
            foreach ($data["items"] as $slot => $serializedItem) {
                $inventory->setItem($slot, Item::jsonDeserialize($serializedItem));
            }
            $player->getXpManager()->setCurrentTotalXp($data["experience"]);
            $player->sendMessage(TextFormat::RESET . TextFormat::GREEN . "Your Inventory has Been Restored.");
            $reviver->sendMessage(TextFormat::RESET . TextFormat::GREEN . "Successfully Restored {$player->getName()}'s Inventory.");
            self::$data->remove($player->getName());
            self::$data->save();
            return true;
        }
        self::sendErrorMessage($reviver, "Unable to Restore {$player->getName()}'s Inventory.");
        return false;
    }

    final public static function sendErrorMessage(Player|CommandSender $player, string $message): void
    {
        $player->sendMessage(TextFormat::RESET . TextFormat::RED . $message);
    }

    /**
     * @throws Exception
     */
    final public function get(Player $player): array
    {
        if (!self::$data->exists($player->getName())) {
            $this->getLogger()->alert("Unable to Find Player Data relating to {$player->getName()}.");
            return self::$data->get($player->getName()) ?? [];
        }
        return self::$data->get($player->getName());
    }

    /**
     * @throws JsonException
     */
    final public function onDeath(PlayerDeathEvent $event)
    {
        $player = $event->getPlayer();
        $armors = $items = [];
        foreach ($player->getArmorInventory()->getContents() as $slot => $armor) {
            $armors[$slot] = $armor->jsonSerialize();
        }
        foreach ($player->getInventory()->getContents() as $slot => $item) {
            $items[$slot] = $item->jsonSerialize();
        }
        $exp = $player->getXpManager()->getCurrentTotalXp();
        self::$data->set($player->getName(), [
            "armor" => $armors,
            "items" => $items,
            "experience" => $exp
        ]);
        self::$data->save();
    }
}
