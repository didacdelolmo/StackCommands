<?php

declare(strict_types=1);


namespace diduhless\stackcommands;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class StackCommands extends PluginBase {

    /** @var array */
    private $commands_data;

    public function onLoad() {
        $this->saveResource("commands.json");
    }

    public function onEnable() {
        $this->commands_data = json_decode(file_get_contents($this->getDataFolder() . "commands.json"), true);
        foreach($this->commands_data as $command_data) {
            $this->getServer()->getCommandMap()->register("stackcommands", new PluginCommand($command_data["command_name"], $this));
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if(!$sender instanceof Player) {
            return true;
        }

        foreach($this->commands_data as $command_data) {
            if(strtolower($command->getName()) === strtolower($command_data["command_name"])) {
                foreach($command_data["executed_commands"] as $stacked_command) {
                    $stacked_command = str_replace("%player", $sender->getName(), $stacked_command);
                    $stacked_command = str_replace("%x", $sender->getX(), $stacked_command);
                    $stacked_command = str_replace("%y", $sender->getY(), $stacked_command);
                    $stacked_command = str_replace("%z", $sender->getZ(), $stacked_command);

                    $this->getServer()->dispatchCommand($sender, $stacked_command);
                }
            }
        }
        return false;
    }

}