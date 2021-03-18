<?php

use Illuminate\Database\Seeder;

class SourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sources')->insert([
        	 // [
		        //  'name' => 'Яндекс',
		        //  'description' => null,
          //        'domain' => 'yandex.ru',
          //        'utm' => 'yandex',
          //        'author_id' => 1,
        	 // ],
          //    [
          //        'name' => 'Google',
          //        'description' => null,
          //        'domain' => 'google.com',
          //        'utm' => 'google',
          //        'author_id' => 1,
          //    ],
          //    [
          //        'name' => 'Мэйл',
          //        'description' => null,
          //        'domain' => 'mail.ru',
          //        'utm' => 'mail',
          //        'author_id' => 1,
          //    ],
          //    [
          //        'name' => 'Вконтакте',
          //        'description' => null,
          //        'domain' => 'vk.com',
          //        'utm' => 'vk',
          //        'author_id' => 1,
          //    ],
          //    [
          //        'name' => 'Фэйсбук',
          //        'description' => null,
          //        'domain' => 'facebook.com',
          //        'utm' => 'facebook',
          //        'author_id' => 1,
          //    ],
          //    [
          //        'name' => 'Инстаграм',
          //        'description' => null,
          //        'domain' => 'instagram.com',
          //        'utm' => 'instagram',
          //        'author_id' => 1,
          //    ],
          //    [
          //        'name' => 'Одноклассники',
          //        'description' => null,
          //        'domain' => 'ok.ru',
          //        'utm' => 'ok',
          //        'author_id' => 1,
          //    ],
          //    [
          //        'name' => '2GIS',
          //        'description' => null,
          //        'domain' => '2gis.ru',
          //        'utm' => '2gis',
          //        'author_id' => 1,
          //    ],
          //    [
          //        'name' => 'Авито',
          //        'description' => null,
          //        'domain' => 'avito.ru',
          //        'utm' => 'avito',
          //        'author_id' => 1,
          //    ],
          //    [
          //        'name' => 'SMS.RU',
          //        'description' => null,
          //        'domain' => 'sms.ru',
          //        'utm' => 'sms',
          //        'author_id' => 1,
          //    ],
          //   [
          //       'name' => 'Рамблер',
          //       'description' => null,
          //       'domain' => 'rambler.ru',
          //       'utm' => 'rambler',
          //       'author_id' => 1,
          //   ],
          //   [
          //       'name' => 'Pinterest',
          //       'description' => null,
          //       'domain' => 'pinterest.ru',
          //       'utm' => 'pinterest',
          //       'author_id' => 1,
          //   ],
          //   [
          //       'name' => 'Фламп',
          //       'description' => 'Отзывы о компаниях вашего города',
          //       'domain' => 'flamp.ru',
          //       'utm' => 'flamp',
          //       'author_id' => 1,
          //   ],
          //   [
          //       'name' => 'MyTarget',
          //       'description' => 'Отзывы о компаниях вашего города',
          //       'domain' => 'target.my.com',
          //       'utm' => 'mytarget',
          //       'author_id' => 1,
          //   ],
          //   [
          //       'name' => 'YouTube',
          //       'description' => 'Сервис видеохостинга',
          //       'domain' => 'youtube.com',
          //       'utm' => 'youtube',
          //       'author_id' => 1,
          //   ],
          //   [
          //       'name' => 'Email рассылка',
          //       'description' => 'Рассылка писем на электронную почту',
          //       'domain' => 'gmail.com',
          //       'utm' => 'letter',
          //       'author_id' => 1,
          //   ],
            [
                'name' => 'TikTok',
                'description' => 'Социальная сеть TikTok',
                'domain' => 'tiktok.com',
                'utm' => 'tiktok',
                'author_id' => 1,
            ],
            [
                'name' => 'Clubhouse',
                'description' => 'Социальная сеть Clubhouse',
                'domain' => 'joinclubhouse.com',
                'utm' => 'clubhouse',
                'author_id' => 1,
            ],
            [
                'name' => 'Telegram',
                'description' => 'Мессенджер Телеграм',
                'domain' => 'telegram.org',
                'utm' => 'telegram',
                'author_id' => 1,
            ],
            [
                'name' => 'WhatsApp',
                'description' => 'Мессенджер WhatsApp',
                'domain' => 'whatsapp.com',
                'utm' => 'whatsapp',
                'author_id' => 1,
            ],
            [
                'name' => 'Viber',
                'description' => 'Мессенджер Viber',
                'domain' => 'viber.com',
                'utm' => 'viber',
                'author_id' => 1,
            ],
            [
                'name' => 'QR-код',
                'description' => 'Графический QR-код',
                'domain' => 'qrcoder.ru',
                'utm' => 'qrcode',
                'author_id' => 1,
            ],
        ]);
    }
}
