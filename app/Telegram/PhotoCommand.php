<?php

namespace App\Telegram;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "photo";

    /**
     * @var string Command Description
     */
    protected $description = "Получаем свое изображение";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {

        $text = "Ваше изображение:\r\n";
        foreach ($commands as $name => $handler) {
            $text .= sprintf('/%s - %s'.PHP_EOL, $name, $handler->getDescription());
        }

        $this->replyWithMessage(compact('text'));
    }
}