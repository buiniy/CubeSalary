<?php
declare(strict_types=1);

namespace phpcube\utils;

use phpcube\CubeSalary;
use phpcube\data\CubeDataProvider;
use pocketmine\player\Player;
use pocketmine\Server;

final class SalaryUtils{
    /**
     * @var array
     */
    public static array $cooldowns = [];

    /**
     * @param Player $player
     * @return int
     */
    public static function getSalaryForGroup(Player $player) : int {

        if(CubeSalary::getInstance()->config->getNested('plugin-groups.pure_perms_api') && CubeSalary::getInstance()->config->getNested('plugin-groups.rank_sys_api') ) {
            Server::getInstance()->getLogger()->error("§c§lПОЖАЛУЙСТА ИСПОЛЬЗУЙТЕ ИСКЛЮЧИТЕЛНЬНО 1 ПЛАГИН ПРАВ В НАСТРОЙКАХ КОНФИГА");
            Server::getInstance()->getLogger()->error("§c§lУБЕДИТЕСЬ В НАЛИЧИИ ПЛАГИНА RankSystem или PurePerms");
            return 0;
        }

        if(CubeSalary::getInstance()->config->getNested('plugin-groups.pure_perms_api')) {
            $groupString = CubeDataProvider::getPurePermsGroupString($player);
            return CubeSalary::getInstance()->config->getNested("salary-amount.{$groupString}", 0);
        }

        if(CubeSalary::getInstance()->config->getNested('plugin-groups.rank_sys_api')) {
            $groupString = CubeDataProvider::getRankSysGroupString($player);
            return CubeSalary::getInstance()->config->getNested("salary-amount.{$groupString}", 0);
        }

        return 0;
    }

    /**
     * @param Player $player
     * @return void
     */
    public static function giveMoney(Player $player) : void {

        if(CubeSalary::getInstance()->config->getNested('plugin-economy.eco_api') && CubeSalary::getInstance()->config->getNested('plugin-economy.bedrock_eco_api') ) {
            Server::getInstance()->getLogger()->error("§c§lПОЖАЛУЙСТА ИСПОЛЬЗУЙТЕ ИСКЛЮЧИТЕЛНЬНО 1 ПЛАГИН Экономики В НАСТРОЙКАХ КОНФИГА");
            Server::getInstance()->getLogger()->error("§c§lУБЕДИТЕСЬ В НАЛИЧИИ ПЛАГИНА BedrockEconomy или EconomyAPI");
            return;
        }

        $salary = self::getSalaryForGroup($player);
        $playerName = strtolower($player->getName());

        if($salary <= 0) {
            $player->sendMessage(CubeSalary::getInstance()->config->get("prefix") .  "§cДля вас нет зарплаты");
            return;
        }

        $cooldownTime = (int)CubeSalary::getInstance()->config->get("salary-cd", 60) * 60;
        $currTime = time();

        if (isset(self::$cooldowns[$playerName]) && $currTime - self::$cooldowns[$playerName] < $cooldownTime) {
            $timeLeft = $cooldownTime - ($currTime - self::$cooldowns[$playerName]);
            $player->sendMessage(CubeSalary::getInstance()->config->get("prefix") .  "§cПодождите: §a" . round($timeLeft / 60) . " мин§c, для использования снова.");
            return;
        }

        if(CubeSalary::getInstance()->config->getNested('plugin-economy.eco_api')) {
            CubeDataProvider::addEcoMoney($player, $salary);
            self::$cooldowns[$playerName] = $currTime;
            return;
        }

        if(CubeSalary::getInstance()->config->getNested('plugin-economy.bedrock_eco_api')) {
            CubeDataProvider::addBedrockMoney($player, $salary);
            self::$cooldowns[$playerName] = $currTime;
            return;
        }

    }
}