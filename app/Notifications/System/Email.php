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

        set_time_limit(0);
        \Auth::loginUsingId(4);

        $mailing = Mailing::with([
            'template',
            'list.items'
//            => function ($q) use ($subscribersWithDispatchIds) {
//                $q->whereNotIn('entity_id', $subscribersWithDispatchIds);
//            }
        ])
            ->where('company_id', auth()->user()->id)
            ->first();

        $subscribersWithDispatchIds = Subscriber::whereHas('dispatches', function ($q) {
            $q->where('mailing_id', 1);
        })
            ->where('company_id', auth()->user()->id)
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

        $destinations = [
            293282078,
            228265675
        ];

        // Отправляем на каждый telegram
        foreach ($destinations as $destination) {
            $response = Telegram::sendMessage([
                'chat_id' => $destination,
                'text' => "По рассылке ВК отправлены {$count} писем"
            ]);
        }
    }
}
