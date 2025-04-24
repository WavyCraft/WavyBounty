<?php

declare(strict_types=1);

namespace wavycraft\wavybounty;

use pocketmine\plugin\PluginBase;

use wavycraft\wavybounty\commands\SetBountyCommand;
use wavycraft\wavybounty\commands\RemoveBountyCommand;
use wavycraft\wavybounty\commands\BountyListCommand;

use CortexPE\Commando\PacketHooker;

class WavyBounty extends PluginBase {

    protected static self $instance;

    protected function onLoad() : void{
        self::$instance;
    }

    protected function onEnable() : void{
        $this->saveResource("messages.yml");

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $this->getServer()->getCommandMap()->registerAll("WavyBounty", [
            new SetBountyCommand($this, "setbounty", "Set a bounty on a player"),
            new RemoveBountyCommand($this, "removebounty", "Remove a players bounty"),
            new BountyListCommand($this, "bountys", "See all active bountys")
        ]);

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

    public static function getInstance() : self{
        return self::$instance;
    }
}