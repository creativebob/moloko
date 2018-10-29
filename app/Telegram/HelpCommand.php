<?php

namespace Telegram\Bot\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class Help1Command extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'help';

    /**
     * @var string Command Description
     */
    protected $description = 'Помощь, выводит список доступных команд';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $commands = $this->telegram->getCommands();

        $text = "Привет! Я - \"Марс Бот\", вот список доступных команд:\r\n";
        foreach ($commands as $name => $handler) {
            $text .= sprintf('/%s - %s'.PHP_EOL, $name, $handler->getDescription());
        }

        $this->replyWithMessage(compact('text'));

        return 'ok';
    }
}
