<?php

namespace App\Notifications\System;

use App\User;
use Telegram;
use Telegram\Bot\Exceptions\TelegramResponseException;

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
}
