<?php

namespace App\Notifications\System;

use App\User;
use Telegram;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Illuminate\Support\Facades\Mail;

class Notifications
{
    /**
     * Отправляем сообщение в телеграм всем подписанным на него юзерам
     *
     * @param $notificationId
     * @param string $message
     * @param null $companyId
     */
    public static function sendNotification($notificationId, $message = '', $companyId = null)
    {
        $destinations = User::whereHas('staff', function ($q) use ($notificationId) {
            $q->whereHas('position', function ($q) use ($notificationId) {
                $q->whereHas('notifications', function ($q) use ($notificationId) {
                    $q->where('notification_id', $notificationId);
                });
            });
        })
            ->where('company_id', $companyId)
            ->whereNotNull('telegram')
            ->get([
                'telegram'
            ]);

        if ($destinations->isNotEmpty()) {

            // Отправляем на каждый telegram
            foreach ($destinations as $destination) {
                if (isset($destination->telegram)) {

                    try {
                        $response = Telegram::sendMessage([
                            'chat_id' => $destination->telegram,
                            'text' => $message
                        ]);
                    } catch (TelegramResponseException $exception) {
                        // Юзера нет в боте, не отправляем ему мессагу
                    }
                }
            }
        }
    }

    // Тестируем отправку почты
    public static function sendMail()
    {
        $to_name = 'Любимому клиенту';
        $to_email = 'creativebob@yandex.ru';
        $data = array('name'=>"Антон Павлович", "body" => "Мы хотим продать вам интересные штучки!", 'subscribe_id' => '1', 'token' => 'hd7h34sdf3gkjdbfk3b4i9dk3igf');

        Mail::send('vkusnyashka/templates/emails/offers/newyear2021/index', $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)->subject('Новогодние подарки 2021');
            $message->from('smpcreativebob@gmail.com','Вкусняшка');
        });
    }


}
