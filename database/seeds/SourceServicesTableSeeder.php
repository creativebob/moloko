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
		$vk_id = Source::where('utm', 'vk')->first()->id;

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
        ]);
    }
}
