<?php
declare(strict_types=1);

namespace phpcube;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use phpcube\command\SalaryCommand;
use pocketmine\utils\SingletonTrait;

final class CubeSalary extends PluginBase {
    use SingletonTrait;

    /**
     * @var Config
     */
    public Config $config;

    public const COMMAND_PERMISSION = "cmd.salary";
	
    public function onEnable() : void {

        self::setInstance($this);

        Server::getInstance()->getLogger()->info("§c§lCubeSalary§r§f - §eРазработано студией - §cvk.com/phpcube");

        $this->saveDefaultConfig();

        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        $this->getServer()->getCommandMap()->register("salary", new SalaryCommand);
    }

}
