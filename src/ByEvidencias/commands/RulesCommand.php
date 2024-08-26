<?php

declare(strict_types=1);

namespace ByEvidencias\command;

use ByEvidencias\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Vecnavium\FormsUI\SimpleForm;

class RulesCommand extends Command {

    public function __construct() {
        parent::__construct("rules", "Displays the server rules and sanctions.", null, ["sanctions"]);
        $this->setPermission("betterrules.cmd");
    }

    public function execute(CommandSender $sender, string $label, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return;
        }

        $this->sendBetterRulesUI($sender);
    }

    private function sendBetterRulesUI(Player $player): void {
        $config = Main::getInstance()->getConfig();
        $title = $this->translateColors($config->get("forms")["rules_menu"]["title"] ?? "Rules and Sanctions");
        $content = $this->translateColors($config->get("forms")["rules_menu"]["content"] ?? "Select an option:");
        $buttons = $config->get("forms")["rules_menu"]["buttons"] ?? ["Rules", "Sanctions"];
        
        $iconRules = "textures/ui/book_edit_default"; 
        $iconSanctions = "textures/ui/redX1"; 

        $form = new SimpleForm(function(Player $player, ?int $data) use ($buttons) {
            if ($data === null) {
                return;
            }

            if ($data < 0 || $data >= count($buttons)) {
                return;
            }

            switch ($data) {
                case 0:
                    $this->configMessage($player, "rules_message");
                    break;
                case 1:
                    $this->configMessage($player, "sanctions_message");
                    break;
            }
        });

        $form->setTitle($title);
        $form->setContent($content);
        $form->addButton($this->translateColors($buttons[0]), 0, $iconRules);
        $form->addButton($this->translateColors($buttons[1]), 0, $iconSanctions);

        $player->sendForm($form);
    }

    private function configMessage(Player $player, string $configKey): void {
        $config = Main::getInstance()->getConfig();
        $title = $this->translateColors($config->get("forms")[$configKey]["title"] ?? "Information");
        $contentArray = $config->get("forms")[$configKey]["content"] ?? [];
        $acceptButton = $this->translateColors($config->get("forms")[$configKey]["accept_button"] ?? "Accept");
        $confirmationMessage = $this->translateColors($config->get("forms")[$configKey]["confirmation_message"] ?? "Thank you for reading!");

        $content = implode("\n", array_map([$this, 'translateColors'], $contentArray));

        $form = new SimpleForm(function(Player $player, ?int $data) use ($confirmationMessage) {
            if ($data === null) {
                return;
            }

            if ($data === 0) {
                $player->sendMessage($confirmationMessage);
            }
        });

        $form->setTitle($title);
        $form->setContent($content);

        $iconAccept = "textures/ui/confirm"; 

        $form->addButton($acceptButton, 0, $iconAccept);

        $player->sendForm($form);
    }

    private function translateColors(string $message): string {
        return str_replace("&", "§", $message);
    }
}
