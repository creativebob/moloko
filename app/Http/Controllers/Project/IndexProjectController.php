<?php

namespace App\Http\Controllers\Project;

// Модель
use App\Lead;
use App\Location;
use App\City;
use App\Site;
use App\Page;
use App\Source;
use App\Choice;
use App\User;
use App\Staffer;

// Запрос
use Illuminate\Http\Request;

// Геоплагин
use App\Http\Controllers\Project\GeopluginController;

// Куки, для контроля форм
use Illuminate\Support\Facades\Cookie;

// Карбон (дата и время)
use Carbon\Carbon;

// Кеш
use Illuminate\Support\Facades\Cache;

// Телеграм
use Telegram;
// use Telegram\Bot\Api;

use Illuminate\Support\Facades\App;

use Illuminate\Config\Repository;

use App\Http\Controllers\Controller;

class IndexProjectController extends Controller
{

    // Настройки сконтроллера
    public function __construct()
    {
        $this->site = Site::where('domain', 'vorotamars.ru')->first();
    }

    // Пишем в куки utm-метки
    public function utm($request)
    {
        if (isset($request->utm_source)) {
            Cookie::queue('utm-source', $request->utm_source, 135000);
        }
        if (isset($request->utm_term)) {
            Cookie::queue('utm-term', $request->utm_term, 135000);
        }
        if (isset($request->utm_content)) {
            Cookie::queue('utm-content', $request->utm_content, 135000);
        }
        if (isset($request->utm_campaign)) {
            Cookie::queue('utm-campaign', $request->utm_campaign, 135000);
        }
        if (isset($request->utm_term)) {
            Cookie::queue('utm-medium', $request->utm_medium, 135000);
        }
    }

    public function start(Request $request)
    {

        $city = $this->site->company->filials->first()->location->city;
        Cookie::queue('city_id', $city->id, 135000);

        $this->utm($request);

        return redirect()->action('Project\IndexProjectController@index', ['alias' => 'company']);
    }

    // Смена города
    public function change_city($city_id, $page_alias = null)
    {
        Cookie::queue('city_id', $city_id, 135000);
        return redirect()->action('Project\IndexProjectController@index', ['alias' => $page_alias]);
    }

    // Общие страницы
    public function index(Request $request, $alias = null)
    {

        if (empty(Cookie::get('city_id'))) {
            $city = $this->site->company->filials->first()->location->city;
            Cookie::queue('city_id', $city->id, 135000);
        }
        $site = $this->site;
        // $page = $site->pages->where(['alias' => $alias, 'display' => 1])->first();
        $page = $site->pages->where('alias', $alias)->where('display', 1)->first();
        // dd($page);

        // Проверяем на существование страницы
        if ($page == null) {
            $error_message = 'Такой страницы не существует... Как вы вообще попали на нее? Советуем вам перейти на любую существующую через меню.';

            return view('project.errors.404', compact('error_message', 'site'));
        }

        $this->utm($request);

        return view('project.'.$alias.'.index', compact('site', 'page'));
    }

    // Спец. страницы

    // Команда
    public function team(Request $request)
    {

        $site = $this->site;
        $page = $site->pages->where('alias', 'team')->first();

        $staff = Staffer::with('user', 'position')
        ->whereHas('filial.location.city', function ($query) {
            $query->where('id', Cookie::get('city_id'));
        })
        ->where('company_id', $site->company_id)
        ->where('display', 1)
        ->whereNotNull('user_id')
        ->whereNull('moderation')
        ->orderBy('sort')
        ->get();

        // Проверяем на существоввание страницы
        if ($page == null) {
            abort(404, 'Такой страницы не существует...');
        }

        return view('project.team.index', compact('site', 'page', 'staff'));
    }

    public function question(Request $request)
    {
        // dd($request);
        $staffer = Staffer::with('user', 'position')->findOrFail($request->id);

        return view('project.team.modal', compact('staffer'));
    }

    // Контакты
    public function contacts(Request $request)
    {
        $this->utm($request);

        $page = $this->site->pages->where('alias', 'contacts')->first();

        // Проверяем на существоввание страницы
        if ($page == null) {
            abort(404, 'Такой страницы не существует...');
        }

        return view('project.contacts.index', [
            'site' => $this->site,
            'page' => $page
        ]);
    }

    public function show(Request $request, $city, $alias, $link)
    {
        $content = json_decode(file_get_contents(env('CRM_DOMAIN').'/api/'.$city.'/'.$alias.'/'.$link.'?token='.env('API_TOKEN').'&domain='.env('SITE_DOMAIN')), true);
        // dd($content);

        $site = Site::with('departments.location.city', 'departments.main_phone')->findOrFail(2);

        $cities = [];
        foreach ($site->departments as $department) {
            $cities[] = $department->location->city->alias;
        }

        if (!in_array($city, $cities)) {
            abort(404, 'Такого филиала не существует...');
        }

        return view($alias.'.show', compact('mass', 'alias', 'content', 'city'));
    }


    // --------------------------------------- Отправка письма и оповещения в telegram ------------------------------------
    public function sending(Request $request)
    {
        // $response = Telegram::getMe();
        // $botId = $response->getId();
        // $firstName = $response->getFirstName();
        // $username = $response->getUsername();

        // Общие настрйки
        // $city = City::where('tag', $city_alias)->first();
        $site_domain = env('PROJECT_DOMAIN');
        $mail_from = "order@" . env('PROJECT_DOMAIN');


        // Заголовки email
        $header = "Content-type: text/html; charset=\"utf8\" \r\n";
        $header .= "From: " . $mail_from . " \r\n";
        $header .= "Subject: Сообщение с сайта " . $site_domain;

        // Дата для записи в бд
        $date_order = Carbon::now()->timezone('Asia/Irkutsk')->format('Y-m-d H:i');
        $date_start = Carbon::now()->timezone('Asia/Irkutsk')->format('Y-m-d');
        $time_task = Carbon::now()->timezone('Asia/Irkutsk')->addMinutes(15)->format('H:i');
        $date = Carbon::now()->timezone('Asia/Irkutsk')->format('dmy');

        // Города (статика)
        switch (Cookie::get('city_id')) {
            case 1:
            $city_id = 1;
            $city_name = 'Иркутск';
            $filial_id = 1;
            break;

            case 2:
            $city_id = 2;
            $city_name = 'Улан-Удэ';
            $filial_id = null;
            break;
        }

        // Создаем лида
        $lead = new Lead;

        if ($request->cookie('utm-source') != null) {
            $utm_source = "\r\nПлощадка: " . Cookie::get('utm-source');
            $source = Source::where('utm', Cookie::get('utm-source'))->first();
            $lead->source_id = $source->id;
        } else {
            $utm_source = '';
        }

        if ($request->cookie('utm-term') != null) {
            $utm_term = "\r\nКлиент искал: " . Cookie::get('utm-term');
            $lead->utm_term = Cookie::get('utm-term');
        } else {
            $utm_term = '';
        }

        if ($request->cookie('utm-content') != null) {
            $lead->utm_content = Cookie::get('utm-content');
        }

        if ($request->cookie('utm-campaign') != null) {
            $lead->campaign_id = Cookie::get('utm-campaign');
        }


        $timenow = Carbon::now()->timezone('Asia/Irkutsk')->format('H:i');

        $lead->name = "Контакт с сайта";

        // Форма звонка
        if ($request->form == 'form-call') {

            // Если не было отправок от пользователя
            if ($request->cookie('form-call') == null) {
                // dd($request);

                $form_name = 'form-call';

                // Формируем email
                $name = $request->name;
                $main_phone = $request->main_phone;
                $remark = $request->remark;

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
                $message .= "Город: " . $city_name."\r\n".$utm_source.$utm_term;

                $choice_id = $request->category_id;
                $choice_type = 'App\GoodsCategory';

                $lead->name = $name;
                $lead->lead_type_id = 1; // Обычный

            }
        }

        // Форма обратой связи
        if ($request->form == 'form-feedback') {

            // Если не было отправок от пользователя
            if ($request->cookie('form-feedback') == null) {
                // dd($request);

                $form_name = 'form-feedback';

                // Формируем email
                $name = 'Вопрос с сайта';
                $main_phone = $request->main_phone;
                $remark = $request->remark;
                $question = $request->question;

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
                $lead->lead_type_id = 1; // Обычный

            }
        }

        // Форма замера
        if ($request->form == 'form-measurement') {

            // Если не было отправок от пользователя
            if($request->cookie('form-measurement') == null) {

                $form_name = 'form-measurement';

                // Формируем email
                $name = $request->name;
                $main_phone = $request->main_phone;
                $address = $request->address;
                $date = $request->date;
                $time = $request->time;

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

                $choice_id = $request->category_id;
                $choice_type = 'App\ServicesCategory';

                $lead->name = $name;
                $lead->lead_type_id = 1; // Обычный

            }
        }

        // Форма уличных ворот
        if ($request->form == 'form-street_gates') {

            // Если не было отправок от пользователя
            if($request->cookie('form-street_gates') == null) {

                $form_name = 'form-street_gates';

                // Формируем email
                $main_phone = $request->main_phone;
                $width = $request->width;
                $height = $request->height;
                $type = $request->type;

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

                $choice_id = $request->category_id;
                $choice_type = 'App\GoodsCategory';
                $lead->lead_type_id = 1; // Обычный

            }
        }

        // Форма секционных ворот
        if ($request->form == 'form-section_gates') {

            // Если не было отправок от пользователя
            if($request->cookie('form-section_gates') == null) {

                $form_name = 'form-section_gates';

                // Формируем email
                $name = $request->name;
                $main_phone = $request->main_phone;

                $width = $request->width;
                $height = $request->height;
                // $left_wall = $request->left_wall; // Левый пристенок
                // $right_wall = $request->right_wall; // Правый пристенок
                // $lintel = $request->lintel; // Притолока
                // $length = $request->length; // Длина гаража

                $option = $request->option;
                $gate = $request->gate; // Калитка

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

                $choice_id = $request->category_id;
                $choice_type = 'App\GoodsCategory';

                $lead->name = $name;
                $lead->lead_type_id = 1; // Обычный

            }
        }

        // Форма забора
        if ($request->form == 'form-fence') {

            // Если не было отправок от пользователя
            if($request->cookie('form-fence') == null) {

                $form_name = 'form-fence';

                // Формируем email
                $main_phone = $request->main_phone;
                $name = $request->name;
                $height = $request->height;
                $width = $request->width;
                $type = $request->type;
                $foundation = $request->foundation;

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

                $choice_id = $request->category_id;
                $choice_type = 'App\GoodsCategory';

                $lead->name = $name;
                $lead->lead_type_id = 1; // Обычный

            }
        }

        // Форма ангара
        if ($request->form == 'form-hangar') {

            // Если не было отправок от пользователя
            if($request->cookie('form-hangar') == null) {

                $form_name = 'form-hangar';

                // Формируем email
                $main_phone = $request->main_phone;
                $height = $request->height;
                $width = $request->width;
                $length = $request->length;

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

                $choice_id = $request->category_id;
                $choice_type = 'App\GoodsCategory';
                $lead->lead_type_id = 1; // Обычный

            }
        }

        // Форма сервисного центра
        if ($request->form == 'form-service_center') {

            // Если не было отправок от пользователя
            if($request->cookie('form-service_center') == null) {
                // dd($request);

                $form_name = 'form-service_center';

                // Формируем email
                $name = $request->name;
                $main_phone = $request->main_phone;
                $type = $request->type;

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

                $choice_id = $request->category_id;
                $choice_type = 'App\ServicesCategory';

                $lead->name = $name;
                $lead->lead_type_id = 3; // Сервисное

            }
        }

        // Форма отправки в другой город
        if ($request->form == 'form-city') {

            // Если не было отправок от пользователя
            if($request->cookie('form-city') == null) {
                // dd($request);

                $form_name = 'form-city';

                // Формируем email
                $name = $request->name;
                $main_phone = $request->main_phone;
                $city = $request->city;

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
                $lead->lead_type_id = 1; // Обычный

            }
        }

        if (isset($form_name)) {

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
            // $dest = [];
            // foreach ($destinations as $telegram_id) {
            //     # code...
            // }
                // dd($telegram_destinations);

            // $destinations_email = ["makc_berluskone@mail.ru"];
            // $telegram_destinations = ["293282078","296553060","295132857", "311241212", "460642600", "228265675", "-284455497", "669765237"];
            // $telegram_destinations = ["228265675"];

            // 293282078 Леша
            // 296553060 Юра
            // 295132857 Коля
            // 254040191 Вася
            // 282109089 Руслан
            // 311241212 Алексей Владимирович
            // 183726286 Игорь
            // 460642600 Анна
            // 228265675 Максим
            // 669765237 Юля

            // $destinations_group_telegram = '-284455497';

            ;

            // Пишем локацию
            $lead_address = isset($address) ? $address : null;

            $location = Location::firstOrCreate(['country_id' => 1, 'city_id' => $city_id, 'address' => $lead_address], ['author_id' => 1]);

            // Заполняем умолчания
            $lead->company_id = 1;
            $lead->filial_id = $filial_id;
            $lead->author_id = 1;
            $lead->site_id = 2; // Сайт компании
            $lead->manager_id = 1;
            $lead->location_id = $location->id;
            $lead->stage_id = 2; // Этап - Обращение
            $lead->lead_method_id = 2; // Обращение с сайта
            $lead->display = 1;

            // $date_start = '2018-05-30';
            // $max_leads = Lead::where(['manager_id' => 1])->whereDay('created_at', Carbon::now()->format('d'))->max('serial_number');
            // $serial_number = $max_leads + 1;
            // $lead->serial_number = $serial_number;

            // $lead->case_number = $date."/".$serial_number."/";

            $count_leads_site = Lead::where('site_id', 2)->whereDate('created_at', Carbon::now()->format('Y-m-d'))->count();
            $serial_num_site = $count_leads_site + 1;

            if ($choice_type != null) {
                $lead->choice_id = $choice_id;
                $lead->choice_type = $choice_type;
            }

            $lead->save();


            if ($lead) {

                // Телефон
                $phones = add_phones($request, $lead);

                // $choice = new Choice;
                // $choice->lead_id = $lead->id;
                // $choice->choices_id = $old_lead->service->choise_id;
                // $choice->choices_type = $old_lead->service->choise_type;
                // $choice->save();

                // if ($choice == false) {
                //     dd('Ошибка записи предпочтения лида.');
                // }

                $lead->notes()->create([
                    'company_id' => 1,
                    'body' => $message,
                    'author_id' => 1,
                ]);



                $message = "Номер заявки: " . $serial_num_site . " (id:" . $lead->id . ")\r\n" . $message;

                // Отправляем на каждый email
                foreach ($destinations_email as $email) {
                    mail($email, $subject, $email_message, $header);
                }

                send_message($telegram_destinations, $message);

                // // Отправляем на каждый telegram
                // foreach ($telegram_destinations as $item) {

                //     $response = Telegram::sendMessage([
                //         'chat_id' => $item->telegram_id,
                //         'text' => $message
                //     ]);
                // }

                // Кидаем в группу лидов
                $response = Telegram::sendMessage([
                    'chat_id' => '-284455497',
                    'text' => $message
                ]);

                // Пишем сессию
                Cookie::queue($form_name, 1, 1440);

                return redirect()->action('Project\IndexProjectController@success');

            } else {
                abort(403, 'Ошибка отправки, вернитесь на нужную страницу и повторите попытку');
            }


        } else {
            return redirect()->action('Project\IndexProjectController@error');
        }
    }

    // -------------------------------------------- Страницы статуса отправки ---------------------------------------------
    // Отправлено
    public function success(Request $request)
    {
        return view('project.send_status.success', [
            'alias' => 'success',
        ]);
    }

    // Ошибка
    public function error(Request $request)
    {
        return view('project.send_status.error', [
            'alias' => 'error',
        ]);
    }

    // --------------------------------------------- База знаний -----------------------------------------------------------
    // Пока статично
    public function knowledge(Request $request, $link)
    {

        $page = $this->site->pages->where('alias', 'knowledge')->first();
        return view('project.knowledge.'.$link, [
            'site' => $this->site,
            'page' => $page
        ]);
    }

    public function telegram(Request $request, $city_alias)
    {


        // false, если класс не найден

        $response = Telegram::getMe();
        $botId = $response->getId();
        $firstName = $response->getFirstName();
        $username = $response->getUsername();

        dd($response);

        $name = 'тест';

        Telegram::setName($name);

        $command = Telegram::getName($name);
        dd($command);
    }

    // Сбрасываем кеш
    public function delete_cache(Request $request, $domain)
    {
        if ($domain == 'vorotamars') {
            $lol = Cache::forget('vorotamars');

            if($lol) {
                dd('ok');
            } else {
                dd('no');
            }
        } else {
            echo 'Чо балуешься?';
        }
    }
}
