<?php

declare(strict_types=1);

namespace wavycraft\wavybounty\api;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

use wavycraft\wavybounty\WavyBounty;

final class WavyBountyAPI {
    use SingletonTrait;

    protected Config $config;

    public function __construct() {
        $dataFolder = WavyBounty::getInstance()->getDataFolder();

        @mkdir($dataFolder . "database/");
        $this->config = new Config($dataFolder . "database/bountys.json");
    }

    public function setBounty(string $name, int $amount) : void{
        $this->config->set($name, $amount);
        $this->config->save();
    }

    public function removeBounty(string $name) : void{
        $this->config->remove($name);
        $this->config->save();
    }

    public function hasBounty(string $name) : bool{
        return $this->config->exists($name);
    }

    public function getBounty(string $name) : int{
        return $this->config->get($name);
    }

    public function getAllBountys() : array{
        return $this->config->getAll();
    }
}