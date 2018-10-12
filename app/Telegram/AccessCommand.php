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
    public function handle($arguments)
    {
        // Пишем в базу сообщение пользователя и выводим его id
        $message = $this->getUpdate();
        
        $tel_msg = new TelegramMessage;

        $tel_msg->message_id = isset($message['message']['message_id']) ? $message['message']['message_id'] : null;
        $tel_msg->update_id = isset($message['update_id']) ? $message['update_id'] : null;

        $tel_msg->from_id = isset($message['message']['from']['id']) ? $message['message']['from']['id'] : null;
        $tel_msg->from_is_bot = isset($message['message']['from']['is_bot']) ? $message['message']['from']['is_bot'] : null;
        $tel_msg->from_first_name = isset($message['message']['from']['first_name']) ? $message['message']['from']['first_name'] : null;
        $tel_msg->from_last_name = isset($message['message']['from']['last_name']) ? $message['message']['from']['last_name'] : null;
        $tel_msg->from_username = isset($message['message']['from']['username']) ? $message['message']['from']['username'] : null;
        $tel_msg->from_language_code = isset($message['message']['from']['language_code']) ? $message['message']['from']['language_code'] : null;

        $tel_msg->chat_id = isset($message['message']['chat']['id']) ? $message['message']['chat']['id'] : null;
        $tel_msg->chat_first_name = isset($message['message']['chat']['first_name']) ? $message['message']['chat']['first_name'] : null;
        $tel_msg->chat_last_name = isset($message['message']['chat']['last_name']) ? $message['message']['chat']['last_name'] : null;
        $tel_msg->chat_username = isset($message['message']['chat']['username']) ? $message['message']['chat']['username'] : null;
        $tel_msg->chat_type = isset($message['message']['chat']['type']) ? $message['message']['chat']['type'] : null;

        $tel_msg->message = isset($message['message']['text']) ? $message['message']['text'] : null;
        $tel_msg->date = isset($message['message']['date']) ? $message['message']['date'] : null;

        $tel_msg->save();

        if ($tel_msg) {
            $text = 'Ваш Telegram ID: '.$tel_msg->chat_id;
        } else {
            $text = 'Произошла ошибка, попробуйте снова через некоторое время иил обратитесь к администратору.';
        }
        
        $this->replyWithMessage(compact('text'));

    }
}