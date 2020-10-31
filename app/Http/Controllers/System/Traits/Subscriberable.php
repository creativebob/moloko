<?php

namespace App\Http\Controllers\System\Traits;

use App\Subscriber;

trait Subscriberable
{

    /**
     * Поиск / сохранение подписчика
     *
     * @param $item
     */
    public function storeSubscriber($item)
    {
        if ($item->getTable() == 'users') {
            $subscriber = Subscriber::firstOrCreate([
                'email' => $item->email,
                'site_id' => $item->site_id
            ]);

            $subscriber->update([
                'subscriberable_id' => $item->id,
                'subscriberable_type' => 'App\User',
                'client_id' => optional($item->client)->id,
                'name' => $item->name,
                'denied_at' => null,
                'is_self' => 0
            ]);
        }

//        if ($item->getTable() == 'companies') {
//            $subscriber = Subscriber::firstOrCreate([
//                'email' => $item->email,
//            ], [
//                'name' => $item->name
//            ]);
//        }
    }

    /**
     * Обновление email подписчика
     *
     * @param $item
     */
    public function updateSubscriber($item)
    {
        $email = request()->email;

        $item->load([
            'archiveSubscriber',
            'client'
        ]);
        $subscriber = $item->archiveSubscriber;

        if ($email) {
            if ($email != $item->email) {
                if (isset($subscriber)) {
                    if (isset($subscriber->archived_at)) {
                        $subscriber->unarchive();
                    }
                    $subscriber->update([
                        'email' => $email,
                        'client_id' => optional($item->client)->id,
                    ]);
                } else {
                    $subscriber = Subscriber::firstOrCreate([
                        'email' => $email,
                        'site_id' => $item->site_id
                    ]);

                    $subscriber->update([
                        'subscriberable_id' => $item->id,
                        'subscriberable_type' => 'App\User',
                        'name' => $item->name,
                        'denied_at' => null,
                        'is_self' => 0,
                        'client_id' => optional($item->client)->id,
                    ]);
                }
            }
        } else {
            if (isset($user->subscriber)) {
                $subscriber->archive();
            }
        }
    }

    /**
     * Архивация подписчика
     *
     * @param $item
     */
    public function archiveSubscriber($item)
    {
        $item->load([
            'archiveSubscriber',
        ]);
        $subscriber = $item->archiveSubscriber;

        if ($subscriber) {
            if (empty($subscriber->archived_at)) {
                $subscriber()->archive();
            }
        }
    }
}
