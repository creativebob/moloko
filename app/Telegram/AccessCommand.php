<?php

namespace App\Telegram;


use App\TelegramMessage;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class AccessCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "access";

    /**
     * @var string Command Description
     */
    protected $description = "Получение Telegram ID пользователя";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        // Пишем в базу сообщение пользователя и выводим его id
        $message = $this->getUpdate();

        $message_id = isset($message['message']['message_id']) ? $message['message']['message_id'] : null;
        $update_id = isset($message['update_id']) ? $message['update_id'] : null;

        $from_id = isset($message['message']['from']['id']) ? $message['message']['from']['id'] : null;
        $from_is_bot = isset($message['message']['from']['is_bot']) ? $message['message']['from']['is_bot'] : null;
        $from_first_name = isset($message['message']['from']['first_name']) ? $message['message']['from']['first_name'] : null;
        $from_last_name = isset($message['message']['from']['last_name']) ? $message['message']['from']['last_name'] : null;
        $from_username = isset($message['message']['from']['username']) ? $message['message']['from']['username'] : null;
        $from_language_code = isset($message['message']['from']['language_code']) ? $message['message']['from']['language_code'] : null;

        $chat_id = isset($message['message']['chat']['id']) ? $message['message']['chat']['id'] : null;
        $chat_first_name = isset($message['message']['chat']['first_name']) ? $message['message']['chat']['first_name'] : null;
        $chat_last_name = isset($message['message']['chat']['last_name']) ? $message['message']['chat']['last_name'] : null;
        $chat_username = isset($message['message']['chat']['username']) ? $message['message']['chat']['username'] : null;
        $chat_type = isset($message['message']['chat']['type']) ? $message['message']['chat']['type'] : null;

        $message = isset($message['message']['text']) ? $message['message']['text'] : null;
        $date = isset($message['message']['date']) ? $message['message']['date'] : null;

        $tel_msg = TelegramMessage::firstOrCreate([
            'chat_id' => $chat_id,
            'message' => $message
        ],
        [
            'message_id' => $message_id,
            'update_id' => $update_id,
            'from_id' => $from_id,
            'from_is_bot' => $from_is_bot,
            'from_first_name' => $from_first_name,
            'from_last_name' => $from_last_name,
            'from_username' => $from_username,
            'from_language_code' => $from_language_code,
            'chat_first_name' => $chat_first_name,
            'chat_last_name' => $chat_last_name,
            'chat_username' => $chat_username,
            'chat_type' => $chat_type,
            'date' => $date
        ]);
        
        $text = 'Ваш Telegram ID: '. $tel_msg->chat_id;

        $this->replyWithMessage(compact('text'));

        // return 'ok';

    }
}