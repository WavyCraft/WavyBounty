<?php

declare(strict_types=1);

namespace wavycraft\wavybounty\commands;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use wavycraft\wavybounty\form\BountyForm;

use CortexPE\Commando\BaseCommand;

class BountyListCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("wavybounty.list");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game!");
            return;
        }

        BountyForm::sendMainForm($sender);
    }
}