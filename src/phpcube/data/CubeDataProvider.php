<?php
declare(strict_types=1);

namespace phpcube\data;

use phpcube\CubeSalary;
use pocketmine\player\Player;

final class CubeDataProvider
{
    /**
     * @param Player $player
     * @param int|float $amount
     * @return void
     */
    public static function addBedrockMoney(Player $player, int|float $amount) : void {
        $economy = \cooldogedev\BedrockEconomy\api\BedrockEconomyAPI::getInstance();
        $economy->addToPlayerBalance($player->getName(), $amount, function(bool $success) use ($player, $amount) : void {
            if ($success) {
                $unit = CubeSalary::getInstance()->config->get("eco_unit");
                $player->sendMessage(CubeSalary::getInstance()->config->get("prefix") .  "Вы успешно получили зарплату в размере: §a{$amount}{$unit}");
            } else {
                $player->sendMessage(CubeSalary::getInstance()->config->get("prefix") .  "§cОшибка транзакции BedrockEconomy");
            }
        });
    }

    /**
     * @param Player $player
     * @param int|float $amount
     * @return void
     */
    public static function addEcoMoney(Player $player, int|float $amount) : void {
        $economy = \onebone\economyapi\EconomyAPI::getInstance();
        $success = $economy->addMoney($player, $amount);
        if($success === $economy::RET_SUCCESS) {
            $unit = CubeSalary::getInstance()->config->get("eco_unit");
            $player->sendMessage(CubeSalary::getInstance()->config->get("prefix") .  "Вы успешно получили зарплату в размере: §a{$amount}{$unit}");
        } else {
            $player->sendMessage(CubeSalary::getInstance()->config->get("prefix") .  "§cОшибка транзакции EconomyAPI");
        }
    }

    /**
     * @param Player $player
     * @return string
     */
    public static function getPurePermsGroupString(Player $player) : string {
        $pp = \_64FF00\PurePerms\PurePerms::getInstance();
        $group = $pp->getUserDataMgr()->getGroup($player);
        return strval($group->getName()) ?? "";
    }

    /**
     * @param Player $player
     * @return string
     */
    public static function getRankSysGroupString(Player $player) : string {
        $rank = \IvanCraft623\RankSystem\RankSystem::getInstance();
        $group = $rank->getSessionManager()->get($player);
        return strval($group->getHighestRank()->getName()) ?? "";
    }
}