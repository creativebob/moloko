<?php

namespace App\Http\Controllers\Project;

use App\Campaign;
use App\Client;
use App\Company;
use App\Discount;
use App\Http\Controllers\Traits\EstimateControllerTrait;
use App\Http\Controllers\Traits\LeadControllerTrait;
use App\Http\Controllers\Traits\UserControllerTrait;
use App\Lead;
use App\LegalForm;
use App\Models\Project\Estimate;
use App\Models\Project\Subscriber;
use App\Phone;
use App\Source;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Telegram;
use Telegram\Bot\Exceptions\TelegramResponseException;

class FormController extends BaseController
{
    /**
     * FormController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    use UserControllerTrait;
    use LeadControllerTrait;
    use EstimateControllerTrait;

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (empty($request->main_phone)) {
            abort(403, 'Не указан номер телефона!');
        }

        logs('leads_from_project')->info("============== НАЧАЛО СОЗДАНИЯ ЛИДА С САЙТА  ===============================");

        // TODO - 03.12.19 - Вынести в отдельные методы для сайта
        // Получаем сайт
        $site = $this->site;
        $filial = $this->site->filial;

        $description = $request->description;

        $firstName = isset($request->first_name) ? $request->first_name : 'Клиент не указал имя';
        $secondName = $request->second_name;

        $name = $firstName;
        if (isset($secondName)) {
            $name = $firstName . ' ' . $secondName;
        }

        $user = auth()->user();

        // Если пользователь АВТОРИЗОВАН
        if ($user) {
            $cleanPhone = $user->main_phone->phone;
            logs('leads_from_project')->info("Пользователь авторизован, id: [{$user->id}]");
        } else {
            // Если пользователь НЕ авторизован
            // Ищем по номеру телефона
            $cleanPhone = cleanPhone($request->main_phone);

            $user = User::where('company_id', $site->company_id)
                ->where(function ($q) use ($site) {
                    $q->where('site_id', $site->id)
                        ->orWhereNull('site_id');
                })
                ->whereHas('main_phones', function ($q) use ($cleanPhone) {
                    $q->where('phone', $cleanPhone);
                })
                ->first();
//                    dd($user);

            if ($user) {
                logs('leads_from_project')->info("Пользователь найден по номеру телефона, id: [{$user->id}]");

                // Обновляем имя пользователя если его нет
                if (($user->first_name == '' || empty($user->first_name)) && isset($request->first_name)) {
                    $user->first_name = $request->first_name;
                    $user->second_name = isset($request->second_name) ? $request->second_name : null;
                    if ($user->second_name) {
                        $user->name = $user->first_name . ' ' . $user->second_name;
                    } else {
                        $user->name = $user->first_name;
                    }
                    $user->saveQuietly();
                }
            } else {
                $usersCount = User::withoutTrashed()
                    ->count();
                $userNumber = $usersCount + 1;

                $user = new User;
                $user->login = 'user_' . $userNumber;
                $user->password = bcrypt(str_random(12));
                $user->access_code = rand(1000, 9999);

                $user->access_block = 0;
                $user->user_type = 0;

                $user->first_name = $firstName;
                $user->second_name = $secondName;
                $user->name = $user->first_name . ' ' . $user->second_name;

                $user->nickname = $user->name;

                $user->location_id = create_location($request, 1, $filial->location->city_id);

                $user->site_id = $site->id;
                $user->company_id = $site->company_id;
                $user->filial_id = $filial->id;
                $user->author_id = 1;
                $user->saveQuietly();

                if ($user) {

                    // Если номера нет, пишем или ищем новый и создаем связь
                    $newPhone = Phone::firstOrCreate([
                        'phone' => $cleanPhone
                    ], [
                        'crop' => substr($cleanPhone, -4),
                    ]);

                    $user->phones()->attach($newPhone->id, [
                        'phone_entity_type' => 'App\User',
                        'main' => 1
                    ]);

                    logs('leads_from_project')->info("Создан пользователь, id: [{$user->id}]");

                } else {
                    abort(403, 'Ошибка при создании пользователя по номеру телефона!');
                }
            }
        }

        // Формируем имя записи в лида
        if (empty($name)) {
            $name = $user->first_name . ' ' . $user->second_name;
        }

        $companyName = $request->company_name;
        $cleanCompanyName = null;
        $company = null;
        $organization = null;

        // Обработка имени компании
        if ($companyName) {
            $cleanCompanyName = str_replace('"', "", $companyName);
            $cleanCompanyName = str_replace('\'', "", $cleanCompanyName);

            $legalForms = LegalForm::get();

            $cleanCompanyNameLowerCase = mb_strtolower($cleanCompanyName, 'UTF-8');
            $item = null;
            foreach ($legalForms as $legalForm) {
                $valueLowerCase = mb_strtolower($legalForm->name, 'UTF-8');
                if (preg_match("/(^|\s)" . $valueLowerCase . "\s/i", $cleanCompanyNameLowerCase, $matches)) {
                    $cleanCompanyNameLowerCase = str_replace($matches[0], "", $cleanCompanyNameLowerCase);
                    $item = $legalForm;
                }
            }

            $cleanCompanyName = \Str::title($cleanCompanyNameLowerCase);
            $company = Company::where('name', $cleanCompanyName)
                ->first();
        }

        // Ищем клиента
        $user->load([
            'organizations' => function ($q) use ($site) {
                $q->wherePivot('company_id', $site->company_id);
            }
        ]);

        $client = null;
        if ($company) {
            $organization = $company;

            $client = Client::where([
                'company_id' => $site->company_id,
                'clientable_type' => 'App\Company'
            ])
                ->where('clientable_id', $organization->id)
                ->first();

            logs('leads_from_project')->info("Найдена компания, id: [{$organization->id}]. Проверена на клиента.");
        } else {
            if ($cleanCompanyName) {
                if ($user->organizations->isNotEmpty()) {
                    $organizationsIds = $user->organizations->pluck('id');

                    $client = Client::where([
                        'company_id' => $site->company_id,
                        'clientable_type' => 'App\Company'
                    ])
                        ->whereIn('clientable_id', $organizationsIds)
                        ->first();

                    if ($client) {
                        $organization = $client->clientable;
                    } else {
                        $organization = $user->organizations->first();
                    }

                    $comment = "Пользователь указал имя компании - {$companyName}";

                    logs('leads_from_project')->info("Не найдена компания. Добавлена первая организация пользователя, id: [{$organization->id}]. Проверена на клиента.");
                }
            } else {
                $curCompany = $site->company;
                $setting = $curCompany->settings->firstWhere('alias', 'search-company-priority');
                if ($setting) {
                    if ($user->organizations->isNotEmpty()) {
                        $organizationsIds = $user->organizations->pluck('id');

                        $client = Client::where([
                            'company_id' => $site->company_id,
                            'clientable_type' => 'App\Company'
                        ])
                            ->whereIn('clientable_id', $organizationsIds)
                            ->first();

                        if ($client) {
                            $organization = $client->clientable;
                        } else {
                            $organization = $user->organizations->first();
                        }

                        logs('leads_from_project')->info("Не найдена компания. Добавлена первая организация пользователя, id: [{$organization->id}]. Проверена на клиента.");
                    }

                    logs('leads_from_project')->info("Найдена настройка на приоритет компании, осуществлен поиск первой организации пользователя.");
                } else {
                    $client = $client = Client::where([
                        'company_id' => $site->company_id,
                        'clientable_type' => 'App\User',
                        'clientable_id' => $user->id
                    ])
                        ->first();
                    logs('leads_from_project')->info("Нет настройки на приоритет компании, осуществлен поиск клиента пользователя.");
                }
            }
        }

        // Создание ЛИДА ======================================================================
        $lead = new Lead;
        $lead->user_id = $user->id;
        $lead->organization_id = optional($organization)->id;
        $lead->client_id = optional($client)->id;

        $lead->email = $request->email ?? '';
        $lead->name = $name;
        $lead->company_name = $cleanCompanyName;
        $lead->private_status = $cleanCompanyName ? 1 : 0;
        $lead->location_id = create_location($request, 1, $filial->location->city_id);
        $lead->need_delivery = $request->get('need_delivery', 0);
        $lead->description = $description;
        $lead->stage_id = $request->stage_id ?? 2;
        $lead->badget = 0;
        $lead->lead_method_id = 2;
        $lead->draft = false;

        $lead->site_id = $site->id;

        // Филиал и торговая точка
        $lead->filial_id = $filial->id;
        $lead->outlet_id = $filial->outletId;

        $lead->company_id = $site->company_id;
        $lead->author_id = 1;

        // Ловим utm метки
        $utm_source = null;
        if ($request->cookie('utm_source') != null) {
            $utm_source = "Площадка: " . $request->cookie('utm_source');
            $lead->source_id = Source::where('utm', $request->cookie('utm_source'))->value('id');
            $lead->utm_source = $request->cookie('utm_source');
        }

        $utm_term = null;
        if ($request->cookie('utm_term') != null) {
            $utm_term = "Клиент искал: " . $request->cookie('utm_term');
            $lead->utm_term = $request->cookie('utm_term');
        }

        if ($request->cookie('utm_content') != null) {
            $lead->utm_content = $request->cookie('utm_content');
        }

        if ($request->cookie('utm_medium') != null) {
            $lead->utm_medium = $request->cookie('utm_medium');
        }

        if ($request->cookie('utm_campaign') != null) {
            $lead->campaign_id = Campaign::where('external', $request->cookie('utm_campaign'))->value('id');
            $lead->utm_campaign = $request->cookie('utm_campaign');
        }

        if ($request->cookie('prom') != null) {
            $lead->prom = $request->cookie('prom');
        }

        // TODO - 10.12.20 - Авторасчет времени отгрузки (Доработка)
        if ($this->site->filial->outlets->first()->settings->firstWhere('alias', 'shipment_at-calculate')) {
            $lead->shipment_at = now()->addSeconds($this->site->filial->outlets->first()->extra_time);
        }

        $lead->saveQuietly();

        logs('leads_from_project')
            ->info("Создан лид с сайта с id :[{$lead->id}], сайт: [{$site->id}]");

        Cookie::queue(Cookie::forget('utm_source'));
        Cookie::queue(Cookie::forget('utm_term'));
        Cookie::queue(Cookie::forget('utm_content'));
        Cookie::queue(Cookie::forget('utm_campaign'));
        Cookie::queue(Cookie::forget('utm_medium'));

        logs('leads_from_project')
            ->info("Очищены рекламные куки");
        // ------------------------------------------- Конец создаем лида ---------------------------------------------

        // Если номера нет, пишем или ищем новый и создаем связь
        $new_phone = Phone::firstOrCreate([
            'phone' => $cleanPhone
        ], [
            'crop' => substr($cleanPhone, -4),
        ]);

        $lead->phones()->attach($new_phone->id, [
            'phone_entity_type' => 'App\Lead',
            'main' => 1,
        ]);

        // Создаем заказ для лида
        $estimate = Estimate::create([
            'lead_id' => $lead->id,
            'filial_id' => $lead->filial_id,
            'client_id' => $lead->client_id,
            'company_id' => $lead->company_id,
            'date' => today(),
            'number' => $lead->id,
            'author_id' => 1,
            'is_main' => true
        ]);
        logs('leads_from_project')->info("Создана смета с id: [{$estimate->id}]");


        $discounts = Discount::where('archive', false)
            ->whereHas('entity', function ($q) {
                $q->where('alias', 'estimates');
            })
            ->where('begined_at', '<=', now())
            ->where(function ($q) {
                $q->where('ended_at', '>=', now())
                    ->orWhereNull('ended_at');
            })
            ->where('company_id', $site->company_id)
            ->get();

        if ($discounts) {
            $estimate->discounts()->attach($discounts->pluck('id'));
        }

        // Аггрегируем значения сметы
        $estimate->load([
            'goods_items.price_goods',
            'services_items',
        ]);

        $cost = 0;
        $amount = 0;
        $points = 0;

        $priceDiscount = 0;
        $catalogsItemDiscount = 0;
        $estimateDiscount = 0;
        $clientDiscount = 0;
        $manualDiscount = 0;

        $total = 0;
        $totalPoints = 0;
        $totalBonuses = 0;

        $count = 0;

        $catalogsFoodsIds = [];

        if ($estimate->goods_items->isNotEmpty()) {
            $cost += $estimate->goods_items->sum('cost');
            $amount += $estimate->goods_items->sum('amount');
            $points += $estimate->goods_items->sum('points');

            $priceDiscount += $estimate->goods_items->sum('price_discount');
            $catalogsItemDiscount += $estimate->goods_items->sum('catalogs_item_discount');
            $estimateDiscount += $estimate->goods_items->sum('estimate_discount');
            $clientDiscount += $estimate->goods_items->sum('client_discount_currency');
            $manualDiscount += $estimate->goods_items->sum('manual_discount_currency');

            $total += $estimate->goods_items->sum('total');
            $totalPoints += $estimate->goods_items->sum('total_points');
            $totalBonuses += $estimate->goods_items->sum('total_bonuses');

            $count += $estimate->goods_items->sum('count');

            $groupedGoodsItems = $estimate->goods_items->groupBy('price_goods.catalogs_goods_id');
            $catalogsFoodsIds = $groupedGoodsItems->keys();
        }

//        if ($estimate->services_items->isNotEmpty()) {
//            $cost += $estimate->services_items->sum('cost');
//            $amount += $estimate->services_items->sum('amount');
//            $total += $estimate->services_items->sum('total');
//        }

        // Скидки
        $discountCurrency = 0;
        $discountPercent = 0;
        if ($total > 0) {
            $discountCurrency = $amount - $total;
            $discountPercent = $discountCurrency * 100 / $amount;
        }

        // Маржа
        $marginCurrency = $total - $cost;
        if ($total > 0) {
            $marginPercent = ($marginCurrency / $total * 100);
        } else {
            $marginPercent = $marginCurrency * 100;
        }

        $estimate->cost = $cost;
        $estimate->amount = $amount;
        $estimate->points = $points;

        $estimate->price_discount = $priceDiscount;
        $estimate->catalogs_item_discount = $catalogsItemDiscount;
        $estimate->estimate_discount = $estimateDiscount;
        $estimate->client_discount = $clientDiscount;
        $estimate->manual_discount = $manualDiscount;

        $estimate->discount_currency = $discountCurrency;
        $estimate->discount_percent = $discountPercent;

        $estimate->total = $total;
        $estimate->total_points = $totalPoints;
        $estimate->total_bonuses = $totalBonuses;

        $estimate->margin_currency = $marginCurrency;
        $estimate->margin_percent = $marginPercent;

        $estimate->save();

        $estimate->catalogs_goods()->sync($catalogsFoodsIds);



        // Обновляем бюджет лида
        $lead->badget = $total;
        $lead->order_amount_base = $total;
        $lead->saveQuietly();

        // Формируем сообщение
        $message = "Заказ с сайта: №" . $lead->id . "\r\n";

        if ($site->domains->count() > 1) {
            $message .= "Город: " . $site->filial->location->city->name . "\r\n";
        }

        $message .= "Имя клиента: " . $lead->name . "\r\n";

        $phone = cleanPhone($request->main_phone);
        $message .= "Тел: " . decorPhone($phone) . "\r\n";


        $message .= "\r\n";

        $message .= $request->message;

        $message .= "\r\n";
        if ($utm_source) {
            $message .= $utm_source . "\r\n";
        }
        if ($utm_term) {
            $message .= $utm_term . "\r\n";
        }

        $lead->notes()->create([
            'company_id' => $site->company_id,
            'body' => $message,
            'author_id' => 1,
        ]);

        $destinations = \App\User::whereHas('staff', function ($query) {
            $query->whereHas('position', function ($query) {
                $query->whereHas('notifications', function ($query) {
                    $query->where('notification_id', 1);
                });
            });
        })
            ->where('company_id', $site->company_id)
            ->whereNotNull('telegram')
            ->get([
                'telegram'
            ]);

        if (isset($destinations)) {

            // Отправляем на каждый telegram
            foreach ($destinations as $destination) {
                if (isset($destination->telegram)) {
                    try {
                        $response = Telegram::sendMessage([
                            'chat_id' => $destination->telegram,
                            'text' => $message
                        ]);
                    } catch (TelegramResponseException $exception) {
                        // Юзера нет в боте, не отправляем ему мессагу
                    }
                }
            }
        }

        logs('leads_from_project')->info("============== Создан лид с сайта ===============================

            ");

        return redirect()->route('project.success');
    }

    /**
     * Оформление подписки
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscribe(Request $request)
    {
        $data = $request->input();
        $data['is_self'] = 1;
        $data['site_id'] = $this->site->id;
        $data['token'] = $this->getToken(\Str::random(30));
        $data['company_id'] = $this->site->company_id;
        $data['author_id'] = 1;

        $subscriber = Subscriber::make($data);
        $subscriber->save();

        return redirect()->route('project.subscribed');
    }

    public function getToken($token) {
        $res = \App\Subscriber::where('token', $token)
            ->first();
        if ($res) {
            $token = \Str::random(30);
            $this->getToken($token);
        } else {
            return $token;
        }
    }
}
