<?php

namespace App\Observers\System\Traits;

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
            if ($item->site_id != 1) {
                $item->load('notifications');

                // TODO - 29.10.20 - Возможно переделываение блока
                $allow = $item->notifications->firstWhere('id', 4);

                $subscriber = Subscriber::firstOrCreate([
                    'email' => $item->email,
                    'site_id' => $item->site_id
                ], [
                    'name' => $item->name,
                    'denied_at' => isset($allow) ? null : now()
                ]);
            }
        }

        if ($item->getTable() == 'companies') {
            $subscriber = Subscriber::firstOrCreate([
                'email' => $item->email,
            ], [
                'name' => $item->name
            ]);
        }

        $item->subscribers()->associate($subscriber)->save();

    }

    /**
     * Обновление email подписчика
     *
     * @param $item
     */
    public function updateSubscriber($item)
    {
        if ($item->isDirty('email')) {
            $item->load([
                'subscriber'
            ]);

            $item->subscriber()->update([
                'email' => $item->email
            ]);
        }
    }

    /**
     * Архивация подписчика
     *
     * @param $item
     */
    public function archiveSubscriber($item)
    {
        $item->subscriber()->archive();
    }

}
