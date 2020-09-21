<?php

namespace App\Exports;

use App\Client;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientsExport implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{

    use Exportable;

    /**
     * Название листа
     *
     * @return string
     */
    public function title(): string
    {
        return 'Клиенты';
    }

    /**
     * Заголовки столцов
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Id',
            'Имя',
            'Дата рождения',
            'Пол',
            'Город',
            'Адрес',
            'Телефон',
            'Telegram',
            'Email',
            'Потерянный',
            'Лояльность',
            'VIP-статус',
            'VIP-статус по вычислениям',
            'В черном списке',
            'Первый источник',
            'Скидка',
            'Поинты',
            'Дата первого заказа',
            'Дата последнего заказа',
            'Срок жизни',
            'Кол-во заказов',
            'Частота заказов',
            'Среднее время между покупками',
            'Клиентский капитал',
            'Средний чек',
            'Ценность клиента',
            'Пожизненная ценность',
            'Кол-во заказов по акции',
            'Коэффициент использования промоакций',
            'RFM-анализ',
            'ABC-анализ',
//            'XYZ-анализ',
//            'Комбинация ABC и XYZ анализов',
            'Динамика активности',
            'Архив',
            'Логин',
            'Доступ',
        ];
    }

    /**
     * Данные в коллекции
     *
     * @return array|\Illuminate\Support\Collection
     */
    public function collection()
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('clients', 'false', getmethod('index'));

        $clients = Client::with([
            'clientable' => function ($q) {
                $q->with([
                    'main_phones',
                    'location.city'
                ]);
            },
            'loyalty',
            'actual_blacklist',
            'source'
        ])

            ->companiesLimit($answer)
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->filter()
            ->where('orders_count', '>', 0)
            ->orderBy('id', 'desc')
//            ->get([
//                'loyalty_id',
//                'is_lost',
//                'is_vip',
//                'is_vip_abc',
//                'source_id',
//                'first_order_date',
//                'last_order_date',
//                'lifetime',
//                'orders_count',
//                'purchase_frequency',
//                'ait',
//                'customer_equity',
//                'average_order_value',
//                'customer_value',
//                'ltv',
//                'use_promo_count',
//                'promo_rate',
//                'rfm',
//                'abc',
//                'xyz',
//                'abcxyz',
//                'activity',
//                'archive',
//            ])
            ->get();

        $items = [];
        foreach ($clients as $client) {
//            dd($client);

            $gender = null;

            if ($client->clientable->getTable() == 'users') {
                switch ($client->clientable->gender) {
                    case (1):
                        $gender = 'мужской';
                        break;
                    case (2):
                        $gender = 'женский';
                        break;
                }
            }

            $client = [
                'id' => $client->id,
                'name' => $client->clientable->name,
                'birthday_date' => optional($client->clientable->birthday_date)->format('d.m.Y'),
                'gender' => $gender,
//                'city' => $client->clientable->location->city->name,
//                'address' => $client->clientable->location->address,
                'city' => optional(optional($client->clientable->location)->city)->name,
                'address' => optional($client->clientable->location)->address,
                'phone' => $client->clientable->main_phone->phone,
                'telegram' => $client->clientable->telegram,
                'email' => $client->clientable->email,
                'is_lost' => $client->is_lost,
                'loyalty_id' => $client->loyalty->name,
                'is_vip' => $client->is_vip,
                'is_vip_abc' => $client->is_vip_abc,
                'is_blacklist' => isset($client->actual_blacklist) ? 1 : '',
                'source_id' => optional($client->source)->name,
                'discount' => $client->discount,
                'points' => $client->points,
                'first_order_date' => optional($client->first_order_date)->format('d.m.Y'),
                'last_order_date' => optional($client->last_order_date)->format('d.m.Y'),
                'lifetime' => $client->lifetime,
                'orders_count' => $client->orders_count,
                'purchase_frequency' => $client->purchase_frequency,
                'ait' => $client->ait,
                'customer_equity' => $client->customer_equity,
                'average_order_value' => $client->average_order_value,
                'customer_value' => $client->customer_value,
                'ltv' => $client->ltv,
                'use_promo_count' => $client->use_promo_count,
                'promo_rate' => $client->promo_rate,
                'rfm' => $client->rfm,
                'abc' => $client->abc,
//                'xyz' => $client->xyz,
//                'abcxyz' => $client->abcxyz,
                'activity' => $client->activity,
                'archive' => $client->archive,
                'login' => $client->clientable->login,
                'access_block' => ($client->clientable->access_block == 1) ? 'Заблокирован' : '',
            ];
            $items[] = collect($client);
        }
        $items = collect($items);
//        dd($items->first());

        return $items;
    }
}
