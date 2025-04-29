<?php

declare(strict_types=1);

namespace wavycraft\wavybounty\player;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

use wavycraft\wavybounty\WavyBounty;

final class PlayerList {
    use SingletonTrait;

    protected Config $config;

    public function __construct() {
        $dataFolder = WavyBounty::getInstance()->getDataFolder();

        @mkdir($dataFolder . "database/");
        $this->config = new Config($dataFolder . "database/playerlist.json");
    }

    public function inFile(string $name) : bool{
        return $this->config->exist($name);
    }

    public function insertName(string $name) : void{
        $this->config->set($name);
        $this->config->save();
    }

    public function removeName(string $name) : void{
        $this->config->remove($name);
        $this->config->save();
    }

    public function getPlayerList() : array{
        return $this->config->getAll();
    }
}
