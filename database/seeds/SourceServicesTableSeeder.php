<?php

use Illuminate\Database\Seeder;
use App\SourceService;
use App\Source;

class SourceServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    	$yandex_id = Source::where('utm', 'yandex')->first()->id;
    	$google_id = Source::where('utm', 'google')->first()->id;
		$sms_id = Source::where('utm', 'sms')->first()->id;
		$facebook_id = Source::where('utm', 'facebook')->first()->id;
		$instagram_id = Source::where('utm', 'instagram')->first()->id;
		$vk_id = Source::where('utm', 'vk')->first()->id;
		$mail_id = Source::where('utm', 'mail')->first()->id;
		$rambler_id = Source::where('utm', 'rambler')->first()->id;
		$youtube_id = Source::where('utm', 'youtube')->first()->id;
		$ok_id = Source::where('utm', 'ok')->first()->id;
		$gis_id = Source::where('utm', '2gis')->first()->id;
		$avito_id = Source::where('utm', 'avito')->first()->id;
		$flamp_id = Source::where('utm', 'flamp')->first()->id;
		$mytarget_id = Source::where('utm', 'mytarget')->first()->id;
		$pinterest_id = Source::where('utm', 'pinterest')->first()->id;
		$letter_id = Source::where('utm', 'letter')->first()->id;
		$tiktok_id = Source::where('utm', 'tiktok')->first()->id;
		$clubhouse_id = Source::where('utm', 'clubhouse')->first()->id;
		$telegram_id = Source::where('utm', 'telegram')->first()->id;
		$whatsapp_id = Source::where('utm', 'whatsapp')->first()->id;
		$viber_id = Source::where('utm', 'viber')->first()->id;
		$qrcode_id = Source::where('utm', 'qrcode')->first()->id;

        SourceService::insert([
        	[
		        'name' => 'Директ',
		        'alias' => 'Direct',
		        'domain' => 'direct.yandex.ru',
                'author_id' => 1,
                'source_id' => $yandex_id,
        	],
        	[
		        'name' => 'Подбор слов',
		        'alias' => 'Wordstat',
		        'domain' => 'wordstat.yandex.ru',
                'author_id' => 1,
                'source_id' => $yandex_id,
        	],
        	[
		        'name' => 'Метрика',
		        'alias' => 'Metrika',
		        'domain' => 'metrika.yandex.ru',
                'author_id' => 1,
                'source_id' => $yandex_id,
        	],
        	[
		        'name' => 'Маркет',
		        'alias' => 'Market',
		        'domain' => 'market.yandex.ru',
                'author_id' => 1,
                'source_id' => $yandex_id,
        	],
        	[
		        'name' => 'Реклама',
		        'alias' => 'Ads',
		        'domain' => 'ads.google.com',
                'author_id' => 1,
                'source_id' => $google_id,
        	],
        	[
		        'name' => 'Аналитика',
		        'alias' => 'Analytics',
		        'domain' => 'analytics.google.com',
                'author_id' => 1,
                'source_id' => $google_id,
        	],
            [
                'name' => 'СМС рассылка',
                'alias' => 'send_sms',
                'domain' => 'sms.ru',
                'author_id' => 1,
                'source_id' => $sms_id,
			],
        	[
		        'name' => 'Web Мастер',
		        'alias' => 'webmaster',
		        'domain' => 'webmaster.yandex.ru',
                'author_id' => 1,
                'source_id' => $yandex_id,
        	],			
        	[
		        'name' => 'Facebook Pixel',
		        'alias' => 'facebookpixel',
		        'domain' => 'facebook.com',
                'author_id' => 1,
                'source_id' => $facebook_id,
        	],
        	[
		        'name' => 'VK Pixel',
		        'alias' => 'vkpixel',
		        'domain' => 'vk.com',
                'author_id' => 1,
                'source_id' => $vk_id,
			],
        	[
		        'name' => 'Почта Яндекс',
		        'alias' => 'mailyandex',
		        'domain' => 'mail.yandex.ru',
                'author_id' => 1,
                'source_id' => $yandex_id,
			],
        	[
		        'name' => 'Рейтинг',
		        'alias' => 'mailtop',
		        'domain' => 'top.mail.ru',
                'author_id' => 1,
                'source_id' => $mail_id,
			],
        	[
		        'name' => 'Топ-100',
		        'alias' => 'top100',
		        'domain' => 'top100.rambler.ru',
                'author_id' => 1,
                'source_id' => $rambler_id,
			],
        	[
		        'name' => 'Почта Мэйл',
		        'alias' => 'mail',
		        'domain' => 'mail.ru',
                'author_id' => 1,
                'source_id' => $mail_id,
			],
        	[
		        'name' => 'Почта Рамблер',
		        'alias' => 'mailrambler',
		        'domain' => 'mail.rambler.ru',
                'author_id' => 1,
                'source_id' => $rambler_id,
			],
        	[
		        'name' => 'Диск',
		        'alias' => 'Disk',
		        'domain' => 'disk.yandex.ru',
                'author_id' => 1,
                'source_id' => $yandex_id,
			],	
        	[
		        'name' => 'Аккаунт на YouTube',
		        'alias' => 'youtube',
		        'domain' => 'youtube.com',
                'author_id' => 1,
                'source_id' => $youtube_id,
			],
        	[
		        'name' => 'Профиль в vk',
		        'alias' => 'profile-vk',
		        'domain' => 'vk.com',
                'author_id' => 1,
                'source_id' => $vk_id,
			],
        	[
		        'name' => 'Профиль в ok',
		        'alias' => 'profile-ok',
		        'domain' => 'ok.ru',
                'author_id' => 1,
                'source_id' => $ok_id,
			],
        	[
		        'name' => 'Профиль в Facebook',
		        'alias' => 'profile-facebook',
		        'domain' => 'facebook.com',
                'author_id' => 1,
                'source_id' => $facebook_id,
			],
        	[
		        'name' => 'Профиль в Instagram',
		        'alias' => 'profile-instagram',
		        'domain' => 'instagram.com',
                'author_id' => 1,
                'source_id' => $instagram_id,
			],
        	[
		        'name' => 'Почта на Gmail',
		        'alias' => 'gmail',
		        'domain' => 'gmail.com',
                'author_id' => 1,
                'source_id' => $google_id,
			],
        	[
		        'name' => 'Почта на Rambler',
		        'alias' => 'mailrambler',
		        'domain' => 'mail.rambler.ru',
                'author_id' => 1,
                'source_id' => $rambler_id,
			],			
        	[
		        'name' => 'Аккаунт в 2GIS',
		        'alias' => '2gis',
		        'domain' => '2gis.ru',
                'author_id' => 1,
                'source_id' => $gis_id,
			],
        	[
		        'name' => 'Аккаунт на Avito',
		        'alias' => 'avito',
		        'domain' => 'avito.ru',
                'author_id' => 1,
                'source_id' => $avito_id,
			],
        	[
		        'name' => 'Профиль на Pinterest',
		        'alias' => 'pinterest',
		        'domain' => 'pinterest.ru',
                'author_id' => 1,
                'source_id' => $pinterest_id,
			],			
        	[
		        'name' => 'Профиль на Flamp',
		        'alias' => 'flamp',
		        'domain' => 'flamp.ru',
                'author_id' => 1,
                'source_id' => $flamp_id,
			],
        	[
		        'name' => 'Аккаунт на MyTarget',
		        'alias' => 'mytarget',
		        'domain' => 'target.my.com',
                'author_id' => 1,
                'source_id' => $mytarget_id,
			],
        	[
		        'name' => 'Аккаунт на Gmail (почтовый сервер)',
		        'alias' => 'mail-gmail',
		        'domain' => 'gmail.com',
                'author_id' => 1,
                'source_id' => $letter_id,
			],
        	[
		        'name' => 'Аккаунт в TikTok',
		        'alias' => 'tiktok',
		        'domain' => 'tiktok.com',
                'author_id' => 1,
                'source_id' => $tiktok_id,
			],
        	[
		        'name' => 'Аккаунт в Clubhouse',
		        'alias' => 'clubhouse',
		        'domain' => 'joinclubhouse.com',
                'author_id' => 1,
                'source_id' => $clubhouse_id,
			],			
        	[
		        'name' => 'Аккаунт пользователя Telegram',
		        'alias' => 'telegram',
		        'domain' => 'telegram.org',
                'author_id' => 1,
                'source_id' => $telegram_id,
			],
        	[
		        'name' => 'Telegram Бот',
		        'alias' => 'telegram-bot',
		        'domain' => 'telegram.org',
                'author_id' => 1,
                'source_id' => $telegram_id,
			],
        	[
		        'name' => 'Telegram канал',
		        'alias' => 'telegram-channel',
		        'domain' => 'telegram.org',
                'author_id' => 1,
                'source_id' => $telegram_id,
			],
        	[
		        'name' => 'Аккаунт WhatsApp',
		        'alias' => 'whatsapp',
		        'domain' => 'whatsapp.com',
                'author_id' => 1,
                'source_id' => $whatsapp_id,
			],
        	[
		        'name' => 'WhatsApp Бот',
		        'alias' => 'whatsapp-bot',
		        'domain' => 'whatsapp.com',
                'author_id' => 1,
                'source_id' => $whatsapp_id,
			],
        	[
		        'name' => 'Группа WhatsApp',
		        'alias' => 'whatsapp-group',
		        'domain' => 'whatsapp.com',
                'author_id' => 1,
                'source_id' => $whatsapp_id,
			],
        	[
		        'name' => 'Аккаунт Viber',
		        'alias' => 'viber',
		        'domain' => 'viber.com',
                'author_id' => 1,
                'source_id' => $viber_id,
			],
        	[
		        'name' => 'Группа в Viber',
		        'alias' => 'viber-group',
		        'domain' => 'viber.com',
                'author_id' => 1,
                'source_id' => $viber_id,
			],
        	[
		        'name' => 'Канал на YouTube',
		        'alias' => 'youtube-channel',
		        'domain' => 'youtube.com',
                'author_id' => 1,
                'source_id' => $youtube_id,
			],
        	[
		        'name' => 'Google Drive',
		        'alias' => 'google-drive',
		        'domain' => 'drive.google.com',
                'author_id' => 1,
                'source_id' => $google_id,
			],
        	[
		        'name' => 'Графическое изображение QR-кода',
		        'alias' => 'qrcode',
		        'domain' => 'qrcoder.ru',
                'author_id' => 1,
                'source_id' => $qrcode_id,
			],
        	[
		        'name' => 'Сообщество vk',
		        'alias' => 'vk-group',
		        'domain' => 'vk.com',
                'author_id' => 1,
                'source_id' => $vk_id,
			],
        	[
		        'name' => 'Vk Бот',
		        'alias' => 'vk-bot',
		        'domain' => 'vk.com',
                'author_id' => 1,
                'source_id' => $vk_id,
			],
        ]);
    }
}
