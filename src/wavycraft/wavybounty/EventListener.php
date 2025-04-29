<?php

declare(strict_types=1);

namespace wavycraft\wavybounty;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\entity\EntityDeathEvent;

use pocketmine\player\Player;

use pocketmine\utils\Config;

use wavycraft\wavybounty\api\WavyBountyAPI;
use wavycraft\wavybounty\player\PlayerList;

use wavycraft\wavyeconomy\api\WavyEconomyAPI;

class EventListener implements Listener {

    public function join(PlayerJoinEvent $event) : void{
        $name = $event->getPlayer()->getName();
        $playerlist = PlayerList::getInstance();

        if (!$playerlist->inFile($name)) {
            $playerlist->insertName($name);
        }
    }

    public function death(EntityDeathEvent $event) : void{
        $entity = $event->getEntity();
        $lastDamageCause = $entity->getLastDamageCause();
        $config = new Config(WavyBounty::getInstance()->getDataFolder() . "messages.yml");

        if ($entity instanceof Player && $lastDamageCause !== null && $lastDamageCause->getDamager() instanceof Player) {
            $victim = $entity;
            $killer = $lastDamageCause->getDamager();
            $api = WavyBountyAPI::getInstance();

            if ($api->hasBounty($victim->getName())) {
                $bountyAmount = $api->getBounty($victim->getName());
                WavyEconomyAPI::getInstance()->addMoney($killer->getName(), $bountyAmount);
                $api->removeBounty($victim->getName());
                $killer->sendMessage((string) new Messages($config, "claimed-bounty", ["{amount}", "{name}"], [number_format($bountyAmount), $victim->getName()]));
            }
        }
    }
}
