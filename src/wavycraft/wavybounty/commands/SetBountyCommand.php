<?php

declare(strict_types=1);

namespace wavycraft\wavybounty\commands;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use pocketmine\utils\Config;

use wavycraft\wavybounty\WavyBounty;
use wavycraft\wavybounty\api\WavyBountyAPI;
use wavycraft\wavybounty\player\PlayerList;

use wavycraft\wavyeconomy\api\WavyEconomyAPI;

use terpz710\messages\Messages;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;

class SetBountyCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("wavybounty.set");

        $this->registerArgument(0, new RawStringArgument("target"));
        $this->registerArgument(1, new IntegerArgument("amount"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        $config = new Config(WavyBounty::getInstance()->getDataFolder() . "messages.yml");

        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be run in-game!");
            return;
        }

        $target = $args["target"];
        $amount = $args["amount"];

        if ($amount <= 0) {
            $sender->sendMessage((string) new Messages($config, "invalid-amount"));
            return;
        }

        $playerlist = PlayerList::getInstance();
        $bountyAPI = WavyBountyAPI::getInstance();

        if ($playerlist->inFile($target)) {
            $sender->sendMessage((string) new Messages($config, "player-does-not-exist", ["{name}"], [$target]));
            return;
        }

        if ($bountyAPI->hasBounty($target)) {
            $sender->sendMessage((string) new Messages($config, "bounty-already-active", ["{name}"], [$target]));
            return;
        }

        $balance = WavyEconomyAPI::getInstance()->getBalance($sender->getName());

        if ($balance < $amount) {
            $sender->sendMessage((string) new Messages($config, "not-enough-money", ["{balance}", "{amount}"], [number_format($balance), number_format($amount)]));
            return;
        }

        WavyEconomyAPI::getInstance()->removeMoney($sender->getName(), $amount);
        $bountyAPI->setBounty($target, $amount);

        $sender->sendMessage((string) new Messages($config, "set-bounty", ["{amount}", "{name}"], [number_format($amount), $target]));
    }
}
