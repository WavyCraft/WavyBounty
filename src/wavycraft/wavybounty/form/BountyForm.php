<?php

declare(strict_types=1);

namespace wavycraft\wavybounty\form;

use pocketmine\player\Player;

use wavycraft\wavybounty\api\WavyBountyAPI;

use terpz710\pocketforms\SimpleForm;
use terpz710\pocketforms\CustomForm;
use terpz710\pocketforms\ModalForm;

final class BountyForm {

    public static function sendMainForm(Player $player) : void{
        $form = (new SimpleForm())
            ->setTitle("Bounties")
            ->setContent("Select an option:")
            ->addButton("Search Bounty")
            ->addButton("See All Active Bounties")
            ->setCallback(function(Player $player, ?int $data) {
                if ($data === null) return;

                match ($data) {
                    0 => self::sendSearchForm($player),
                    1 => self::sendAllBountiesForm($player),
                };
            });
        $player->sendForm($form);
    }

    public static function sendSearchForm(Player $player) : void{
        $form = (new CustomForm())
            ->setTitle("Search Bounty")
            ->addInput("Enter player name:")
            ->setCallback(function(Player $player, ?array $data) {
                if ($data === null) return;

                $name = trim($data[0]);
                $bountyAPI = WavyBountyAPI::getInstance();

                if ($bountyAPI->hasBounty($name)) {
                    $amount = $bountyAPI->getBounty($name);
                    self::sendBountyModal($player, $name, $amount);
                } else {
                    self::sendNotFoundModal($player);
                }
            });
        $player->sendForm($form);
    }

    public static function sendAllBountiesForm(Player $player) : void{
        $bountyAPI = WavyBountyAPI::getInstance();
        $bounties = $bountyAPI->getAllBountys();

        if (empty($bounties)) {
            $player->sendMessage("There are currently no active bounties!");
            return;
        }

        $form = (new SimpleForm())
            ->setTitle("Active Bounties")
            ->setContent("Click a player to view their bounty:");

        foreach ($bounties as $name => $amount) {
            $form->addButton($name);
        }

        $form->setCallback(function(Player $player, ?int $data) use ($bounties) {
            if ($data === null) return;

            $names = array_keys($bounties);
            $targetName = $names[$data];
            $amount = $bounties[$targetName];

            self::sendBountyModal($player, $targetName, $amount);
        });

        $player->sendForm($form);
    }

    public static function sendBountyModal(Player $player, string $name, int $amount) : void{
        $form = (new ModalForm())
            ->setTitle("Bounty Info")
            ->setContent("Player: " . $name . "\nBounty: §a$" . $amount)
            ->setButton1("Close")
            ->setButton2("Go Back")
            ->setCallback(function(Player $player, bool $data) {
                if (!$data) self::sendMainForm($player);
            });
        $player->sendForm($form);
    }

    public static function sendNotFoundModal(Player $player) : void{
        $form = (new ModalForm())
            ->setTitle("Bounty Not Found")
            ->setContent("That player does not have a bounty!")
            ->setButton1("Close")
            ->setButton2("Go Back")
            ->setCallback(function(Player $player, bool $data) {
                if (!$data) self::sendMainForm($player);
            });
        $player->sendForm($form);
    }
}