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
        
        $this->config = new Config($dataFolder . "database/playerlist.json", Config::JSON, []);
    }

    public function inFile(string $name) : bool{
        $list = $this->config->getAll();
        return in_array($name, $list, true);
    }

    public function insertName(string $name) : void{
        $list = $this->config->getAll();
        if (!in_array($name, $list, true)) {
            $list[] = $name;
            $this->config->setAll($list);
            $this->config->save();
        }
    }

    public function removeName(string $name) : void{
        $list = $this->config->getAll();
        $list = array_values(array_filter($list, fn($n) => $n !== $name));
        $this->config->setAll($list);
        $this->config->save();
    }

    public function getPlayerList() : array{
        return $this->config->getAll();
    }
}
