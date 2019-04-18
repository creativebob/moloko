<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Entity;

use App\Site;
use App\City;
use App\Lead;
use App\Source;
use App\User;
use App\Location;
use App\Phone;

use Carbon\Carbon;

use Telegram;

class AppController extends Controller
{

    // --------------------------------------- Отправка данных формы ------------------------------------
    public function lead_store(Request $request)
    {

        $response = $request->response;

        // dd($request);
        $site = Site::where('api_token', $response['api_token'])->first();

        $mail_from = "order@".$site->domain;

        // Заголовки email
        $header = "Content-type: text/html; charset=\"utf8\" \r\n";
        $header .= "From: " . $mail_from . " \r\n";
        $header .= "Subject: Сообщение с сайта " . $site->domain;

        // Дата для записи в бд
        $date_order = Carbon::now()->timezone('Asia/Irkutsk')->format('Y-m-d H:i');
        $date_start = Carbon::now()->timezone('Asia/Irkutsk')->format('Y-m-d');
        $time_task = Carbon::now()->timezone('Asia/Irkutsk')->addMinutes(15)->format('H:i');
        $date = Carbon::now()->timezone('Asia/Irkutsk')->format('dmy');

        $city = City::where('alias', $response['city_alias'])->first();
        if (!$city) {
            $city = City::where('alias', 'irkutsk')->first();
        }



        $city_id = $city->id;
        $city_name = $city->name;

        $filial_id = $site->company->filials->where('location.city_id', $city_id)->first()->id;

        // Создаем лида
        $lead = new Lead;

        if (isset($response['utm_source'])) {
            $utm_source = "\r\nПлощадка: " . $response['utm_source'];
            $lead->source_id = Source::where('utm', $response['utm_source'])->first()->id;
        } else {
            $utm_source = '';
        }

        if (isset($response['utm_term'])) {
            $utm_term = "\r\nКлиент искал: " . $response['utm_term'];
            $lead->utm_term = $response['utm_term'];
        } else {
            $utm_term = '';
        }

        if (isset($response['utm_content'])) {
            $lead->utm_content = $response['utm_content'];
        }

        if (isset($response['utm_campaign'])) {
            $lead->campaign_id = $response['utm_campaign'];
        }

        $timenow = Carbon::now()->timezone('Asia/Irkutsk')->format('H:i');

        $lead->name = "Контакт с сайта";

        // Форма звонка
        if ($response['form'] == 'form-call') {

            // Формируем email
            $name = $response['name'];
            $main_phone = $response['phone'];
            $remark = $response['remark'];

            $email_message = "
            <html>
            <head>
            <style type=\"text/css\">
            table tr td
            {
            }
            </style>
            </head>
            <body>
            <h2>Заказ звонка с сайта!</h2>
            <p>$remark</p>
            <table>
            </table><br>
            <span>Имя клиента: <strong>$name</strong></span><br>
            <span>Телефон клиента: <strong>$main_phone</strong></span><br>
            <span>Время поступления заявки: $timenow</span><br>
            <span>Город: $city_name</span><br>
            <body>
            </html>
            ";
            $subject = "Вам написали письмо с сайта!";



            // Формируем сообщение в telegram
            $message  = "Филиал: " . $city_name . "\r\n";

            $message .= "Клиент: " . $name . "\r\n";
            $message .= "Тел: " . $main_phone . "\r\n";
            $message .= "Сообщение: " . $remark . "\r\n";
            $message .= "Город: " . $city_name."\r\n";
            $message .=$utm_source.$utm_term;

            $choice_id = $response['category_id'] == 0 ? null : $response['category_id'];
            $choice_type = 'App\GoodsCategory';

            $lead->name = $name;
            $lead->lead_type_id = 1;
        }

        // Форма обратой связи
        if ($response['form'] == 'form-feedback') {

            // Формируем email
            $name = 'Вопрос с сайта';
            $main_phone = $response['phone'];
            $remark = $response['remark'];
            $question = $response['question'];

            $email_message = "
            <html>
            <head>
            <style type=\"text/css\">
            table tr td
            {
            }
            </style>
            </head>
            <body>
            <h2>Вопрос с сайта!</h2>
            <p>$remark</p>
            <p>$question</p>
            <table>
            </table><br>
            <span>Телефон клиента: <strong>$main_phone</strong></span><br>
            <span>Время поступления заявки: $timenow</span><br>
            <span>Город: $city_name</span><br>
            <body>
            </html>
            ";
            $subject = "Вам написали письмо с сайта!";

            // Формируем сообщение в telegram
            $message  = "Филиал: " . $city_name . "\r\nКлиент: " . $name . "\r\nТел: " . $main_phone . "\r\nСообщение: " . $remark . "\r\nВопрос: " . $question . "\r\nГород: " . $city_name."\r\n".$utm_source.$utm_term;

            $choice_id = null;
            $choice_type = null;

            $lead->name = $name;
            $lead->lead_type_id = 1;
        }

        // Форма замера
        if ($response['form'] == 'form-measurement') {

            // Формируем email
            $name = $response['name'];
            $main_phone = $response['phone'];
            $address = $response['address'];
            $date = $response['date'];
            $time = isset($response['time']) ? $response['time'] : '10:00';

            $email_message = "
            <html>
            <head>
            <style type=\"text/css\">
            table tr td
            {
            }
            </style>
            </head>
            <body>
            <h2>Вызов специалиста на замер:</h2>
            <table>
            <span>Дата замера: <strong>$date</strong></span><br>
            <span>Время замера: <strong>$time</strong></span><br>
            </table><br>
            <span>Адрес места замера: <strong>$address</strong></span><br>
            <span>Имя клиента: <strong>$name</strong></span><br>
            <span>Телефон клиента: <strong>$main_phone</strong></span><br>
            <span>Время отправки заявки: $timenow</span><br>
            <body>
            </html>
            ";

            $subject  = "Вызов специалиста на замер";

            // Формируем сообщение в telegram
            $message  = "Филиал: " . $city_name . "\r\n". "Клиент: " . $name . "\r\n" . "Тел: " . $main_phone . "\r\n\r\nВызов замерщика: \r\n\r\n" . "Дата замера: " . $date . "\r\n" . "Время замера: " . $time . "\r\n". "Адрес: " . $address."\r\n".$utm_source.$utm_term;

            $choice_id = $response['category_id'];
            $choice_type = 'App\ServicesCategory';

            $lead->name = $name;
            $lead->lead_type_id = 1;
        }

        // Форма уличных ворот
        if ($response['form'] == 'form-street_gates') {

            // Формируем email
            $main_phone = $response['phone'];
            $width = $response['width'];
            $height = $response['height'];
            $type = $response['type'];

            $email_message = "
            <html>
            <head>
            <style type=\"text/css\">
            table tr td
            {
            }
            </style>
            </head>
            <body>
            <h2>Заказ расчета стоимости уличных ворот по указанным параметрам</h2>
            <table>
            </table><br>
            <span>Тип ворот: <strong>$type</strong></span><br>
            <span>Ширина проема: <strong>$width</strong></span><br>
            <span>Высота проема: <strong>$height</strong></span><br>
            <span>Телефон клиента: <strong>$main_phone</strong></span><br>
            <span>Время поступления заявки: $timenow</span><br>
            <body>
            </html>
            ";

            $subject  = "Заказ расчета стоимости уличных ворот";

            $message  = "Филиал: " . $city_name . "\r\n". "Тел: " . $main_phone . "\r\n\r\nЗаказ расчета стоимости уличных ворот по указанным параметрам: \r\n\r\n" . "Тип ворот: " . $type . "\r\n" . "Ширина проема: " . $width . "\r\n". "Высота проема: " . $height."\r\n".$utm_source.$utm_term;

            $choice_id = $response['category_id'];
            $choice_type = 'App\GoodsCategory';
            $lead->lead_type_id = 1;
        }

        // Форма секционных ворот
        if ($response['form'] == 'form-section_gates') {

            // Формируем email
            $name = $response['name'];
            $main_phone = $response['phone'];

            $width = $response['width'];
            $height = $response['height'];
            // $left_wall = $response['left_wall']; // Левый пристенок
            // $right_wall = $response['right_wall']; // Правый пристенок
            // $lintel = $response['lintel']; // Притолока
            // $length = $response['length']; // Длина гаража

            $option = $response['option'];
            $gate = $response['gate'];

            // <span>Левый пристенок: <strong>$left_wall</strong> мм.</span><br>
            // <span>Правый пристенок: <strong>$right_wall</strong> мм.</span><br>
            // <span>Притолка: <strong>$lintel</strong></span> мм.<br>
            // <span>Длина гаража: <strong>$length</strong></span> мм.<br>

            $email_message = "
            <html>
            <head>
            <style type=\"text/css\">
            table tr td
            {
            }
            </style>
            </head>
            <body>
            <h2>Заказ расчета стоимости секционных ворот по следующим параметрам</h2>
            <table>
            <span>Ширина проема: <strong>$width</strong> мм.</span><br>
            <span>Высота проема: <strong>$height</strong> мм.</span><br>
            <span>Опции: <strong>$option</strong></span><br>
            <span>Калитка: <strong>$gate</strong></span><br>
            </table><br>
            <span>Имя клиента: $name</span><br>
            <span>Телефон клиента: <strong>$main_phone</strong></span><br>
            <span>Время: $timenow</span><br>
            <body>
            </html>
            ";

            $subject  = "Заказ расчета стоимости секционных ворот";

            // Левый пристенок: " . $left_wall . "\r\nПравый пристенок: " . $right_wall . "\r\nПритолка: " . $lintel . "\r\nДлина гаража: " . $length . "\r\n

            $message  = "Филиал: " . $city_name . "\r\nКлиент: " . $name . "\r\nТел: " . $main_phone . "\r\n\r\nЗаказ расчета стоимости секционных ворот по следующим параметрам: \r\n\r\nШирина проема: " . $width . "\r\nВысота проема: " . $height . "\r\nОпции: " . $option . "\r\nКалитка: " . $gate."\r\n".$utm_source.$utm_term;

            $choice_id = $response['category_id'];
            $choice_type = 'App\GoodsCategory';

            $lead->name = $name;
            $lead->lead_type_id = 1;
        }

        // Форма забора
        if ($response['form'] == 'form-fence') {

            // Формируем email
            $main_phone = $response['phone'];
            $name = $response['name'];
            $height = $response['height'];
            $width = $response['width'];
            $type = $response['type'];
            $foundation = $response['foundation'];

            $email_message = "
            <html>
            <head>
            <style type=\"text/css\">
            table tr td
            {
            }
            </style>
            </head>
            <body>
            <h2>Заказ расчета стоимости забора по указанным параметрам</h2>
            <table>
            </table><br>
            <span>Тип забора: <strong>$type</strong></span><br>
            <span>Тип фундамента: <strong>$foundation</strong></span><br>
            <span>Ширина забора: <strong>$width</strong></span><br>
            <span>Высота забора: <strong>$height</strong></span><br>
            <span>Телефон клиента: <strong>$main_phone</strong></span><br>
            <span>Время поступления заявки: $timenow</span><br>
            <body>
            </html>
            ";

            $subject = "Заказ расчета стоимости забора";

            $message  = "Филиал: " . $city_name . "\r\nИмя клиента: " . $name . "\r\nТел: " . $main_phone . "\r\n\r\nЗаказ расчета стоимости забора по указанным параметрам: \r\n\r\nТип забора: " . $type . "\r\nШирина забора: " . $width . "\r\nВысота забора: " . $height . "\r\nТип фундамента: " . $foundation."\r\n".$utm_source.$utm_term;

            // switch ($type) {
            //     case "Эко":
            //     $service_id = 42;
            //     break;
            //     case "Комфорт":
            //     $service_id = 40;
            //     break;
            //     case "Премиум":
            //     $service_id = 60;
            //     break;
            //     case "3D":
            //     $service_id = 61;
            //     break;
            //     // default: $service_id = 42;
            // }

            $choice_id = $response['category_id'];
            $choice_type = 'App\GoodsCategory';

            $lead->name = $name;
            $lead->lead_type_id = 1;
        }

        // Форма ангара
        if ($response['form'] == 'form-hangar') {

            // Формируем email
            $main_phone = $response['phone'];
            $height = $response['height'];
            $width = $response['width'];
            $length = $response['length'];

            $email_message = "
            <html>
            <head>
            <style type=\"text/css\">
            table tr td
            {
            }
            </style>
            </head>
            <body>
            <h2>Заказ расчета стоимости ангара по указанным параметрам</h2>
            <table>
            </table><br>
            <span>Ширина ангара: <strong>$width</strong></span><br>
            <span>Высота ангара: <strong>$height</strong></span><br>
            <span>Длина ангара: <strong>$length</strong></span><br>
            <span>Телефон клиента: <strong>$main_phone</strong></span><br>
            <span>Время поступления заявки: $timenow</span><br>
            <body>
            </html>
            ";

            $subject = "Заказ расчета стоимости забора";

            $message  = "Филиал: " . $city_name .  "\r\nТел: " . $main_phone . "\r\n\r\nЗаказ расчета стоимости ангара по указанным параметрам: \r\n\r\nШирина ангара: " . $width . "\r\nВысота ангара: " . $height ."\r\nДлина ангара: " . $length."\r\n".$utm_source.$utm_term;

            $choice_id = $response['category_id'];
            $choice_type = 'App\GoodsCategory';
            $lead->lead_type_id = 1;
        }

        // Форма сервисного центра
        if ($response['form'] == 'form-service_center') {

            // Формируем email
            $name = $response['name'];
            $main_phone = $response['phone'];
            $type = $response['type'];

            $email_message = "
            <html>
            <head>
            <style type=\"text/css\">
            table tr td
            {
            }
            </style>
            </head>
            <body>
            <h2>Заказ звонка с сайта!</h2>
            <table>
            </table><br>
            <span>Имя клиента: <strong>$name</strong></span><br>
            <span>Телефон клиента: <strong>$main_phone</strong></span><br>
            <span>Тип обращения: <strong>$type</strong></span><br>
            <span>Время поступления заявки: $timenow</span><br>
            <span>Город: $city_name</span><br>
            <body>
            </html>
            ";
            $subject = "Вам написали письмо с сайта!";

            // Формируем сообщение в telegram
            $message  = "Филиал: " . $city_name . "\r\nКлиент: " . $name . "\r\nТел: " . $main_phone . "\r\nСервисное обращение: " . $type . "\r\nГород: " . $city_name."\r\n".$utm_source.$utm_term;

            $choice_id = $response['category_id'];
            $choice_type = 'App\ServicesCategory';

            $lead->name = $name;
            $lead->lead_type_id = 3;
        }

        // Форма отправки в другой город
        if ($response['form'] == 'form-city') {

            // Формируем email
            $name = $response['name'];
            $main_phone = $response['phone'];
            $city = $response['city'];

            $email_message = "
            <html>
            <head>
            <style type=\"text/css\">
            table tr td
            {
            }
            </style>
            </head>
            <body>
            <h2>Заказ звонка с сайта!</h2>
            <p>Меня интересует возможность отправки в $city</p>
            <table>
            </table><br>
            <span>Имя клиента: <strong>$name</strong></span><br>
            <span>Телефон клиента: <strong>$main_phone</strong></span><br>
            <span>Время поступления заявки: $timenow</span><br>
            <span>Город: $city_name</span><br>
            <body>
            </html>
            ";
            $subject = "Вам написали письмо с сайта!";

            // Формируем сообщение в telegram
            $message  = "Филиал: " . $city_name . "\r\nКлиент: " . $name . "\r\nТел: " . $main_phone . "\r\nСообщение: Меня интересует возможность отправки в " . $city . "\r\nГород: " . $city_name."\r\n".$utm_source.$utm_term;

            $choice_id = null;
            $choice_type = null;

            $lead->name = $name;
            $lead->lead_type_id = 1;
        }

        $form_name = $response['form'];

        $destinations_email = ["info@vorotamars.ru"];

        $telegram_destinations = User::whereHas('staff', function ($query) {
            $query->whereHas('position', function ($query) {
                $query->whereHas('notifications', function ($query) {
                    $query->where('notification_id', 1);
                });
            });
        })
        ->where('telegram_id', '!=', null)
        ->get(['telegram_id']);

        // Пишем локацию
        $lead_address = isset($address) ? $address : null;

        $location = Location::firstOrCreate(['country_id' => 1, 'city_id' => $city_id, 'address' => $lead_address], ['author_id' => 1]);

        // Заполняем умолчания
        $lead->company_id = $site->company_id;
        $lead->filial_id = $filial_id;
        $lead->author_id = 1;
        $lead->site_id = $site->id;
        $lead->manager_id = 1;
        $lead->location_id = $location->id;
        $lead->stage_id = 2;
        $lead->lead_method_id = 2;
        $lead->display = 1;

        $count_leads_site = Lead::where('site_id', $site->id)->whereDate('created_at', Carbon::now()->format('Y-m-d'))->count();
        $serial_num_site = $count_leads_site + 1;

        if ($choice_type != null) {
            $lead->choice_id = $choice_id;
            $lead->choice_type = $choice_type;
        }

        $lead->save();


        if ($lead) {

            // Пишем или ищем новый и создаем связь
            $phone = Phone::firstOrCreate(
                ['phone' => cleanPhone($main_phone)
            ], [
                'crop' => substr(cleanPhone($main_phone), -4),
            ]);
                // dd($phone);
            $lead->phones()->attach($phone->id, ['main' => 1]);

            $lead->notes()->create([
                'company_id' => $site->company_id,
                'body' => $message,
                'author_id' => 1,
            ]);

            $message = "Номер заявки: " . $serial_num_site . " (id:" . $lead->id . ")\r\n" . $message;

                // Отправляем на каждый email
            foreach ($destinations_email as $email) {
                mail($email, $subject, $email_message, $header);
            }

            send_message($telegram_destinations, $message);

            // Кидаем в группу лидов
            $response = Telegram::sendMessage([
                'chat_id' => '-284455497',
                'text' => $message
            ]);
        }

        // Отвечаем сайту
        return response()->json([
            'status' => 'ok',
        ]);
    }
}
