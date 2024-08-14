<?php
declare(strict_types=1);

namespace phpcube\command;

use phpcube\utils\SalaryUtils;
use pocketmine\command\Command;
use pocketmine\player\Player;
use pocketmine\command\CommandSender;

use phpcube\CubeSalary;
use phpcube\form\{SimpleForm};

class SalaryCommand extends Command {
    public CubeSalary $loader;

    public static string $prefix = "";
    public static string $command = "";
    public static string $desc = "";
    public static array $aliases = [];

    public function getLoader() : CubeSalary {
        return CubeSalary::getInstance();
    }

    public function __construct() {

        self::$prefix = $this->getLoader()->config->get("prefix", "salary.prefix.null");
        self::$command = $this->getLoader()->config->get("command");
        self::$desc = $this->getLoader()->config->get("desc", "salary.description.null");
        self::$aliases = $this->getLoader()->config->get("aliases");

        parent::__construct(self::$command, self::$desc);
        $this->setPermission(CubeSalary::COMMAND_PERMISSION);

        $this->setAliases(self::$aliases ?? []);
    }

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
		if (!$sender instanceof Player) {
			$sender->sendMessage(self::$prefix . "§cТолько игроку");
			return false;
		}
        $this->showSalaryForm($sender);
        return true;
	}

    public static function showSalaryForm(Player $player): void {
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if (!is_null($data)) {
                if ($data == 0) {
                    SalaryUtils::giveMoney($player);
                }
            }
        });
        $form->setTitle(self::$prefix);
        $salary = SalaryUtils::getSalaryForGroup($player);
        $unit = CubeSalary::getInstance()->config->get("eco_unit");
        $cooldownTime = (int)CubeSalary::getInstance()->config->get("salary-cd", 60) * 60;

        $form->setContent("§aЗарплата вашей привилегии: §a{$salary}{$unit}§f, каждые: §a" . round($cooldownTime / 60)  . " мин.");
        $form->addButton("§c§lПолучить зарплату");

        $player->sendForm($form);
    }


}