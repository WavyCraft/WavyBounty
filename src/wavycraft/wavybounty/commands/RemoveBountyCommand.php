<?php

declare(strict_types=1);

namespace wavycraft\wavybounty\commands;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use pocketmine\utils\Config;

use wavycraft\wavybounty\WavyBounty;
use wavycraft\wavybounty\form\BountyForm;

use terpz710\messages\Messages;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;

class RemoveBountyCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("wavybounty.remove");

        $this->registerArgument(0, new RawStringArgument("target"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        $config = new Config(WavyBounty::getInstance()->getDataFolder() . "messages.yml");

        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be run in-game!");
            return;
        }

        $target = $args["target"];

        $bountyAPI = WavyBountyAPI::getInstance();

        if ($bountyAPI->hasBounty($target)) {
            $bountyAPI->removeBounty($target);
            $sender->sendMessage((string) new Messages($config, "removed-bounty", ["{name}"], [$target]));
        } else {
            $sender->sendMessage((string) new Messages($config, "no-bounty", ["{name}"], [$target]));
        }
    }
}