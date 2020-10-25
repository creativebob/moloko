<?php

namespace App\Notifications\System;

use App\Dispatch;
use App\Mailing;
use App\Subscriber;
use Illuminate\Support\Facades\Mail;
use Telegram;

class Email
{

    /**
     * Отправка почты
     */
    public static function send()
    {
        $to_name = 'Любимому клиенту';
        $to_email = 'creativebob@yandex.ru';
        $data = [
            'name' => "Антон Павлович",
            "body" => "Мы хотим продать вам интересные штучки!"
        ];

        Mail::send('system/templates/emails/offers/newyear2021/index', $data, function ($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)->subject('Новогодние подарки 2021');
            $message->from(config('mail.from.address'), config('mail.from.name'));
        });
    }


    /**
     * Рассылка писем для ВК
     */
    public static function sendMailingToSubscribers()
    {

        $destinations = [
            293282078,
            228265675
        ];

        foreach ($destinations as $destination) {
            $response = Telegram::sendMessage([
                'chat_id' => $destination,
                'text' => "Начинаем отправку"
            ]);
        }

        set_time_limit(0);
        \Auth::loginUsingId(4);

        $mailing = Mailing::with([
            'template',
        ])
            ->where('company_id', auth()->user()->company_id)
            ->first();

        $subscribersWithDispatchIds = Subscriber::whereHas('dispatches', function ($q) use ($mailing) {
            $q->where('mailing_id', $mailing->id);
        })
            ->where('company_id', auth()->user()->company_id)
            ->get([
                'id'
            ])
            ->pluck('id');

        $limit = 10;

        $subscribers = Subscriber::whereNotIn('id', $subscribersWithDispatchIds)
            ->limit($limit)
            ->get();

        $count = 0;
        foreach ($subscribers as $subscriber) {

            $data = [
                'subscriberId' => $subscriber->id,
                'token' => $subscriber->token
            ];

//            $path = 'vkusnyashka/templates/emails/offers/newyear2021/index';

            Mail::send($mailing->template->path, $data, function ($message) use ($mailing, $subscriber) {
                $message->to($subscriber->email, 'Любимому клиенту')->subject($mailing->subject);
                $message->from(config('mail.from.address'), config('mail.from.name'));
            });

            $subscriber->dispatches()->save(Dispatch::make([
                'email' => $subscriber->email,
                'mailing_id' => $mailing->id
            ]));

            $count++;
        }



        // Отправляем на каждый telegram
        foreach ($destinations as $destination) {
            $response = Telegram::sendMessage([
                'chat_id' => $destination,
                'text' => "По рассылке ВК отправлены {$count} писем"
            ]);
        }
    }
}
