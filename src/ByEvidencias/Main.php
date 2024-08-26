<?php

declare(strict_types=1);

namespace ByEvidencias;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use ByEvidencias\command\RulesCommand;

class Main extends PluginBase {

    private static ?Main $instance = null;

    public static function getInstance(): Main {
        return self::$instance;
    }

    protected function onEnable(): void {
        self::$instance = $this;

        $this->saveDefaultConfig();

        $this->getServer()->getCommandMap()->register("rules", new RulesCommand());
    }

    protected function onDisable(): void {
    }

    public function getCustomConfig(string $fileName = "config.yml"): Config {
        return new Config($this->getDataFolder() . $fileName, Config::YAML);
    }
}
