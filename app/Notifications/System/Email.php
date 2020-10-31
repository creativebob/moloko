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
     * Отправка письма
     */
    public static function send($item)
    {
        $item->load([
           'arciveSubscriber'
        ]);
        $subscriber = $item->subscriber;
        $data = [
            'subscriberId' => $subscriber->id,
            'token' => $subscriber->token
        ];

        Mail::send($mailing->template->path, $data, function ($message) use ($mailing, $dispatch) {
            $message->to($dispatch->email, 'Любимому клиенту')->subject($mailing->subject);
            $message->from(config('mail.from.address'), config('mail.from.name'));
        });

        $dispatch->update([
            'sended_at' => now()
        ]);
        if ($count > 0) {
            $destinations = [
                293282078,
                228265675
            ];

            // Отправляем на каждый telegram
            foreach ($destinations as $destination) {
                $response = Telegram::sendMessage([
                    'chat_id' => $destination,
                    'text' => "По рассылке [{$mailing->name}] отправлены {$count} писем"
                ]);
            }
        }

    }

    /**
     * Рассылка писем
     */
    public static function mailing()
    {
        set_time_limit(0);

        $mailing = Mailing::with([
            'template',
            'list.subscribers' => function ($q) {
                $q->valid()
                    ->active()
                    ->allow();
            },
        ])
            ->where('started_at', '<=', now())
            ->whereNull('ended_at')
            ->where('is_active', true)
            ->whereNotNull('mailing_list_id')
//            ->where('company_id', auth()->user()->company_id)
            ->oldest('started_at')
            ->first();
//        dd($mailing);

        if ($mailing) {
            \Auth::loginUsingId($mailing->author_id);

            if (empty($mailing->begined_at)) {
                $data = [];
                foreach ($mailing->list->subscribers as $subscriber) {
                    $data[] = Dispatch::make([
                        'email' => $subscriber->email,
                        'subscriber_id' => $subscriber->id,
                        'company_id' => $subscriber->company_id
                    ]);
                }
                $mailing->dispatches()->saveMany($data);

                $mailing->update([
                    'begined_at' => now()
                ]);
            }

            $mailing->load([
                'waitingDispatches' => function ($q) {
                    $q->with([
                        'subscriber'
                    ])
                        ->limit(35);
                }
            ]);

            $count = 0;
            foreach ($mailing->waitingDispatches as $dispatch) {

                $subscriber = $dispatch->subscriber;
                $data = [
                    'subscriberId' => $subscriber->id,
                    'token' => $subscriber->token
                ];

                Mail::send($mailing->template->path, $data, function ($message) use ($mailing, $dispatch) {
                    $message->to($dispatch->email, 'Любимому клиенту')->subject($mailing->subject);
                    $message->from(config('mail.from.address'), config('mail.from.name'));
                });

                $dispatch->update([
                   'sended_at' => now()
                ]);

                $count++;
            }

            $mailing->load('waitingDispatches');

            if ($mailing->waitingDispatches->count() == 0) {
                $mailing->update([
                    'ended_at' => now()
                ]);
            }

            if ($count > 0) {
                $destinations = [
                    293282078,
                    228265675
                ];

                // Отправляем на каждый telegram
                foreach ($destinations as $destination) {
                    $response = Telegram::sendMessage([
                        'chat_id' => $destination,
                        'text' => "По рассылке [{$mailing->name}] отправлены {$count} писем"
                    ]);
                }
            }
        }
    }
}
