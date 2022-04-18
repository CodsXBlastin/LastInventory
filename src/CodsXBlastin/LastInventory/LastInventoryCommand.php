<?php

declare(strict_types=1);

namespace CodsXBlastin\LastInventory;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\Server;

final class LastInventoryCommand extends Command
{
    //pm4 has a stroke trying to handle anonymous classes
    final public function __construct()
    {
        parent::__construct("lastinventory", "LastInventory Root Command", "§r§c/lastinventory <player: string>", ["lastinv", "li"]);
        $this->setPermission("lastinventory.command");
    }

    final public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof ConsoleCommandSender || $sender->hasPermission($this->getPermission())) {
            if (!isset($args[0])) {
                Loader::sendErrorMessage($sender, "Argument #'0' Cannot be Equal to or Less Than 'null'.");
                return;
            }

            $player = Server::getInstance()->getPlayerByPrefix($args[0]);
            if ($player === null) {
                Loader::sendErrorMessage($sender, "Player Cannot be Equal to or Less Than 'null'.");
                return;
            }

            Loader::revive($player, $sender);
        } else Loader::sendErrorMessage($sender, "Missing Permission to Access this Command. Contact an Administrator If you Believe This to be Incorrect.");
    }
}
