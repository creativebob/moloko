<?php

namespace App\Http\Controllers\System\External;

use App\Article;
use App\ArticlesGroup;
use App\CatalogsGoodsItem;
use App\Client;
use App\ContractsClient;
use App\Estimate;
use App\Goods;
use App\GoodsCategory;
use App\Http\Controllers\Controller;
use App\Http\Controllers\System\Traits\Clientable;
use App\Http\Controllers\Traits\UserControllerTrait;
use App\Lead;
use App\Models\Project\EstimatesGoodsItem;
use App\Models\System\External\Price;
use App\Models\System\External\PricesType;
use App\Models\System\External\User as ParseUser;
use App\Payment;
use App\Phone;
use App\PricesGoods;
use App\Staffer;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RollHouseController extends Controller
{
    /**
     * RollHouseController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    use Clientable;
    use UserControllerTrait;

    public function setCompanyId()
    {
        $res = EstimatesGoodsItem::whereNull('company_id')->update([
            'company_id' => 1
        ]);
        return "{$res} проставлено";

    }


    /**
     * Проверка соответствия категорий товаров
     *
     * @return string
     */
    public function externalCategories()
    {
        set_time_limit(0);

        $categoriesIds = [
            1 => 2,
            2 => 1,
            3 => 3,
            4 => 5,
            5 => 6,
            7 => 8,
            12 => 7,
            13 => 17,
            14 => 18,
            16 => 9,
            17 => 11,
            19 => 31,
            21 => 20,
            22 => 40,
            23 => 41,
            24 => 30,
            25 => 45,
            26 => 25,
            27 => 42,
            28 => 27,
            29 => 29,
            30 => 28,
            31 => 36,
            32 => 37,
            33 => 48,
            34 => 38,
            35 => 25,
            36 => 46,
            37 => 44,
            38 => 43,
            39 => 39,
            40 => 4,
            41 => 32,
            42 => 47
        ];

        $oldCategories = PricesType::get();
        $categories = GoodsCategory::get();

        $msg = "Сопоставление категорий:<br>";
        $msg .= "Старая/Новая:<br>";
        foreach($categoriesIds as $oldId => $categoryId) {
            $msg .= $oldCategories->firstWhere('id', $oldId)->name . " / " . $categories->firstWhere('id', $categoryId)->name . "<br>";
        }

        return $msg;
    }

    /**
     * Установка найденым товарам external
     *
     * @return string
     */
    public function externalId()
    {
        set_time_limit(0);
        $prices = Price::get();
        $articles = Article::whereNull('external')
            ->whereHas('cur_goods', function ($q) {
                $q->where('archive', false);
            })
            ->get();

        $count = 0;
        foreach($prices as $price) {
            $curArticles = $articles->where('name', $price->name);
            if($curArticles) {
                if ($curArticles->count() > 1) {
                    echo "Артикулов больше 1, название - {$price->name}, id = {$curArticles->implode('cur_goods.id', ', ')}<br>";
                } else if ($curArticles->count() == 1) {
                    $curArticles->first()->update([
                        'external' => $price->id
                    ]);
                    $count++;
                }
            }
        }
        return "<br>{$count} артикулам проставлен внешний id";
    }

    /**
     * Перенос товаров из старой базы РХ в систему
     *
     * @return string
     */
    public function externalGoods()
    {
        set_time_limit(0);

        // Срощенные id категорий товаров
        $categoriesIds = [
            1 => 2,
            2 => 1,
            3 => 3,
            4 => 5,
            5 => 6,
            7 => 8,
            12 => 7,
            13 => 17,
            14 => 18,
            16 => 9,
            17 => 11,
            19 => 31,
            21 => 20,
            22 => 40,
            23 => 41,
            24 => 30,
            25 => 45,
            26 => 25,
            27 => 42,
            28 => 27,
            29 => 29,
            30 => 28,
            31 => 36,
            32 => 37,
            33 => 48,
            34 => 38,
            35 => 25,
            36 => 46,
            37 => 44,
            38 => 43,
            39 => 39,
            40 => 4,
            41 => 32,
            42 => 47
        ];

        $manufacturerCategoriesIds = [1,2,3,4,5,7,12,13,14,16,17,21,22,23,26,33,35,36,37,38,39,40,41,42];

        $volumesCategoriesIds = [19,24,28,29,30,31,32,41];

        $count = 0;
        $total = 0;
        $lostCount = 0;
        $existsCount = 0;

        $prices = Price::get();
//        dd($prices->first());
//        $goods = Goods::get();
//        $articles = Article::get();

        $goodsCategories = GoodsCategory::get();

        foreach($prices as $price) {

            $article = Article::where('external', (string) $price->id)
                ->whereHas('cur_goods', function ($q) {
                    $q->where('archive', false);
                })
                ->first();
//            $article = $articles->firstWhere('external', $price->id);
            if ($article) {
                // Если у нас уже есть такой товар с проставленным внешним id
                $existsCount++;

            } else {
                // если нет, то заводим

//                dd($categoriesIds);

                    // Если id категории товара есть в нашем массиве
                    $goodsCategory = $goodsCategories->firstWhere('id', $categoriesIds[$price->price_type_id]);
                    if ($goodsCategory) {
//                        // Если найдена категория, создаем товар и крепим его к прайсам
//
////                        dd($price);
//                        // Создаем группу
                        $articlesGroup = ArticlesGroup::create([
                            'name' => $price->name,
                            'units_category_id' => 6,
                        ]);

                        // Пишем к группе связь с категорией
                        $goodsCategory->groups()->syncWithoutDetaching($articlesGroup->id);

                        $data = [
                            'name' => $price->name,
                            'description' => $price->desc,
                            'articles_group_id' => $articlesGroup->id,
                            'external' => $price->id,
                            'unit_id' => 32,
                            'manufacturer_id' => in_array($price->price_type_id, $manufacturerCategoriesIds) ? 1 : null,
//                            'cost_default' => $price->cost,
                        ];

                        if (in_array($price->price_type_id, $volumesCategoriesIds)) {
                            $data['unit_weight_id'] = 8;
                            $data['weight'] = 0;
                            $data['unit_volume_id'] = 28;
                            $data['volume'] = $price->weight * 0.000001;
                        } else {
                            $data['unit_weight_id'] = 7;
                            $data['weight'] = $price->weight * 0.001;
                            $data['unit_volume_id'] = 30;
                            $data['volume'] = 0;
                        }

                        $newArticle = Article::create($data);

                        $newArticle->update([
                            'created_at' => $price->created,
                            'draft' => false,
                            'timestamps' => false
                        ]);

                        $curGoods = Goods::create([
                            'article_id' => $newArticle->id,
                            'price_unit_category_id' => 6,
                            'price_unit_id' => 32,
                            'category_id' => $goodsCategory->id
                        ]);

                        // TODO - 15.06.20 - Добить создание прайса (Решить вопрос с разделами)

                        $total++;
                    } else {
                        // Если не найдена
                    }

            }

            $count++;
        }

        return "Создано {$total} товаров, потеряно {$lostCount} товаров, существовало {$existsCount}, Всего проходов {$count}";
    }

    public function externalPrices()
    {
        set_time_limit(0);

        $catalogsGoodsItemsIds = [
            1 => [
                1 => 1,
                12 => 12,
                39 => 12,
                40 => 13,
                41 => 13,
                2 => 2,
                51 => 64,
                14 => 14,
                15 => 15,
                3 => 3,
                4 => 4,
                5 => 5,
                6 => 6,
                7 => 7,
                8 => 8,
                16 => 16,
                17 => 17,
                18 => 18,
                19 => 19,
                20 => 20,
                21 => 21,
                22 => 22,
                23 => 23,
                24 => 24,
                9 => 9,
                37 => 87,
                38 => 66,
                10 => 10,
                31 => 55,
                32 => 54,
                33 => 56,
                34 => 58,
                35 => 57,
                26 => 68,
                27 => 69,
                28 => 71,
                29 => 70,
                30 => 72,
                36 => 73,
                11 => 11,
                25 => 51,
                49 => 74,
                50 => 75,
                42 => 78,
                46 => 79,
                43 => 80,
                45 => 81,
                47 => 82,
                44 => 83,
                48 => 85,
                53 => 84,
                52 => 49,
            ],
            2 => [
                1 => 25,
                12 => 26,
                39 => 26,
                40 => 27,
                41 => 27,
                2 => 28,
                51 => 65,
                14 => 30,
                15 => 31,
                3 => 29,
                4 => 32,
                5 => 33,
                6 => 34,
                7 => 35,
                8 => 36,
                16 => 37,
                17 => 38,
                18 => 39,
                19 => 40,
                20 => 41,
                21 => 42,
                22 => 43,
                23 => 44,
                24 => 45,
                9 => 46,
                37 => 94,
                38 => 67,
                10 => 47,
                31 => 61,
                32 => 62,
                33 => 60,
                34 => 59,
                35 => 63,
                26 => 88,
                27 => 89,
                28 => 91,
                29 => 90,
                30 => 92,
                36 => 93,
                11 => 48,
                25 => 52,
                49 => 76,
                50 => 77,
                42 => 95,
                46 => 96,
                43 => 97,
                45 => 98,
                47 => 99,
                44 => 100,
                48 => 102,
                53 => 101,
                52 => 50,
            ]
        ];

        $prices = Price::get();

        $count = 0;
        $countArchive = 0;

        foreach ($prices as $price) {
//            dd($price);
            // TODO - 16.06.20 - Выбрать артикулы без прайсов (которые добавили парсером сегодня)
            $article = Article::with([
                'cur_goods'
            ])
                ->where('external', (string) $price->id)
                ->whereHas('cur_goods', function ($q) {
                    $q->doesnthave('prices');
                })
                ->first();
            if ($article) {
                // Если у нас уже есть такой товар с проставленным внешним id
                $curGoods = $article->cur_goods;


                foreach([1,2] as $filialId) {
                    $catalogGoodsItemId = $catalogsGoodsItemsIds[$filialId][$curGoods->category_id];
//                    dd($catalogGoodsItemId);
                    $catalogGoodsItem = CatalogsGoodsItem::find($catalogGoodsItemId);
//                    dd($catalogGoodsItem);

                    if ($catalogGoodsItem) {
                        $cur_price_goods = PricesGoods::create([
                            'catalogs_goods_item_id' => $catalogGoodsItem->id,
                            'catalogs_goods_id' => $catalogGoodsItem->catalogs_goods_id,
                            'goods_id' => $curGoods->id,
                            'filial_id' => $filialId,
                            'currency_id' => 1,
                            'price' => $price->cost,
                            'point' => $price->rh ?? 0,
                            'is_hit' => isset($price->hit),
                        ]);

                        $cur_price_goods->update([
                           'display' => false
                        ]);

                        if ($curGoods-> archive == 1) {
                            $countArchive++;
                        } else {
                            $count++;
                        }
                    } else {
                        echo $catalogGoodsItemId . "<br>";
                    }
                }
            }
        }

        return "Прайсы готовы. Товаров в архиве {$countArchive} / 2, Без архива {$count} / 2, всего " . ($count + $countArchive) . "/ 2";
    }

    /**
     * Полный перенос старой базы РХ в систему
     *
     * @param Request $request
     * @return string
     */
    public function oldBase(Request $request)
    {
        set_time_limit(0);

//        $this->externalId();
//        $this->externalGoods();

        define("ANGARSK", 3);
        define("USOLYE", 2);

        $oldUsers = ParseUser::whereIn('branch_id', [ANGARSK, USOLYE])
//            ->has('checks')
            ->with([
                'checks' => function ($q) {
                    $q->whereDate('created', '>', '2016-03-03')
                    ->whereNull('employer_id')
                    ->where(function ($q) {
                        $q->where('reject', 2)
                            ->orWhereNull('reject');
                    })
//                        ->where('reject', '!==', 1)
                    ;
                }
            ])
            ->where('is_parse', false)
//            ->where('phone', 89086504979)
            ->get()
//            ->orderByDesc('id')
//            ->get()
            ->random(10);
//         ;
//        dd($oldUsers);

        $authUser = auth()->user();
        $companyId = 1;
        $dateCheck = Carbon::create('2019-12-17');

        $pricesGoods = PricesGoods::with([
            'goods.article'
        ])
            ->where('archive', false)
            ->get();

        foreach($oldUsers as $oldUser) {

            // Смотрим телефон в нашей БД
            $phone = Phone::where('phone', $oldUser->phone)
                ->with([
                    'user_owner' => function ($q) {
                        $q->where('site_id', 2);
                    }
                ])
                ->whereHas('user_owner', function ($q) {
                    $q->where('site_id', 2);
                })
                ->first();
//            dd($phone);

            if ($phone) {
//                dd($oldUser);
                // Если найден, проверяем есть ли user
                $user = $phone->user_owner->first();

                if ($user) {
//                    dd($user);
//                    $user->save();
                }

                $phone = $user->main_phone;

            } else {
                // если телефона нет в нашей БД, заводим юзера

                if ($oldUser->branch_id) {
                    $city_id = ($oldUser->branch_id == ANGARSK) ? 2 : 4;
                } else {
                    $city_id = 2;
                }
                $filial_id = ($city_id == 2) ? 1 : 2;

                $userNumber = User::withTrashed()
                    ->count();
                $userNumber = $userNumber + 1;

                $user = new User;
                $user->login = "user_{$userNumber}";
                $user->password = bcrypt(str_random(12));
                $user->access_code = rand(1000, 9999);

                $res = getNameUser($oldUser->name);
                $user->first_name = $res['first_name'];
                $user->second_name = $res['second_name'];
                $user->patronymic = $res['patronymic'];
                $user->gender = $res['gender'];

                $user->name = $oldUser->name;

                $user->access_block = 0;
                $user->user_type = 0;

                if ($oldUser->birthday){
                    $user->birthday_date = Carbon::parse($oldUser->birthday)->format('d.m.Y');
                }

                $request->address = $oldUser->address;
                $user->location_id = create_location($request, 1, $city_id);

                $user->site_id = 2;
//                $user->email = $oldUser->email;

                $user->filial_id = $filial_id;
                $user->created_at = $oldUser->created;
                $user->save([
                    'timestamps' => false
                ]);

                if ($user) {
                    // Пишем или находим номер телефона
                    $new_phone = Phone::firstOrCreate([
                        'phone' => cleanPhone($oldUser->phone)
                    ], [
                        'crop' => substr(cleanPhone($oldUser->phone), -4),
                    ]);

                    $user->phones()->attach($new_phone->id, ['main' => 1]);

                    $phone = $new_phone;

                }
            }

            // Пишем смс оповещение
            if (is_null($oldUser->sms_deny)) {
                $user->notifications()->sync([3]);
            }

            if ($oldUser->checks->isNotEmpty()) {

                $user->load('client');

                if (isset($user->client)) {
                    $client = $user->client;

                } else {
                    // Сохраняем пользователя как клиента, т.к. у него есть заказы в старой базе
                    $client = Client::make([
                        'description' => $oldUser->desc,
                        'discount' => $oldUser->discount ?? 0,
                        'points' => $oldUser->rh ?? 0,
                    ]);

                    $user->client()->save($client);

                    $user->load('client');
                    $client = $user->client;
                    $client->created_at = $oldUser->created;
                    $client->save([
                        'timestamps' => false
                    ]);

                    if ($oldUser->state == 1) {
                        $client->blacklists()->create([
                            'description' => $oldUser->desc,
                        ]);
                    }
                }

                foreach($oldUser->checks as $check) {

                    $estimate = Estimate::where('external', $check->id)
                        ->first();
                    if (! $estimate) {
                        if ($check->branch_id) {
                            $city_id = ($check->branch_id == ANGARSK) ? 2 : 4;
                        } else {
                            $city_id = 2;
                        }
                        $filial_id = ($city_id == 2) ? 1 : 2;

                        // Дата до запуска и стол (не сайт)
                        if ($check->created < $dateCheck || isset($check->table)) {

                            // Если не отмененный заказ
                            if ($check->progress == 2) {
                                $lead = Lead::with([
                                        'estimates'
                                    ])
                                    ->whereDate('created_at', $check->created)
                                    ->where('stage_id', 13)
                                    ->whereHas('estimates', function ($q) {
                                        $q->where('is_dismissed', true);
                                    })
                                    ->where('client_id', $client->id)
                                    ->first();

                                if ($lead) {
//                                    dd($lead);
                                    foreach($lead->estimates as $estimate) {
                                        $estimate->update([
                                            'is_main' => false
                                        ]);
                                    }

                                } else {
                                    $lead = new Lead;

                                    // Добавляем локацию
                                    $request->address = $check->address;
                                    $lead->location_id = create_location($request, 1, $city_id);

                                    $lead->company_id = $companyId;
                                    $lead->filial_id = $filial_id;
                                    $lead->name = ($user->name == '' || $user->name == ' ' || is_null($user->name)) ? null : $user->name;
                                    $lead->company_name = NULL;

                                    $lead->draft = null;
                                    $lead->author_id = hideGod($authUser);

                                    // TODO - 10.06.20 - Менеджер пока Серебро
                                    $lead->manager_id = 4;

                                    $lead->client_id = $client->id;
                                    $lead->stage_id = ($check->progress == 2) ? 12 : 13;
                                    $lead->lead_type_id = 1;

                                    $lead->lead_method_id = ($check->table) ? 3 : 1;
                                    $lead->display = true;

                                    $lead->badget = $check->summa;
                                    $lead->created_at = $check->created;

                                    $lead->is_create_parse = true;
//                                $lead->is_link_parse = true;
//                        dd($lead);
                                    $lead->save([
                                        'timestamps' => false
                                    ]);

                                    $lead_number = getLeadNumbers($authUser, $lead);
                                    $lead->case_number = $check->id;
                                    $lead->serial_number = $lead_number['serial'];

                                    $lead->save([
                                        'timestamps' => false
                                    ]);

                                    if ($lead) {
                                        $lead->phones()->attach($phone->id, ['main' => 1]);
                                    }
                                }

                            } else {
                                $lead = Lead::with([
                                        'estimates'
                                    ])
                                    ->whereDate('created_at', $check->created)
                                    ->where('stage_id', 13)
                                    ->whereHas('estimates', function ($q) {
                                        $q->where('is_dismissed', true);
                                    })
                                    ->where('client_id', $client->id)
                                    ->first();

                                if ($lead) {
                                    foreach($lead->estimates as $estimate) {
                                        $estimate->update([
                                            'is_main' => false
                                        ]);
                                    }
                                } else {
                                    $lead = new Lead;

                                    // Добавляем локацию
                                    $request->address = $check->address;
                                    $lead->location_id = create_location($request, 1, $city_id);

                                    $lead->company_id = $companyId;
                                    $lead->filial_id = $filial_id;
                                    $lead->name = ($user->name == '' || $user->name == ' ' || is_null($user->name)) ? null : $user->name;
                                    $lead->company_name = NULL;

                                    $lead->draft = null;
                                    $lead->author_id = hideGod($authUser);

                                    // TODO - 10.06.20 - Менеджер пока Серебро
                                    $lead->manager_id = 4;

                                    $lead->client_id = $client->id;
                                    $lead->stage_id = ($check->progress == 2) ? 12 : 13;
                                    $lead->lead_type_id = 1;

                                    $lead->lead_method_id = ($check->table) ? 3 : 1;
                                    $lead->display = true;

                                    $lead->badget = $check->summa;
                                    $lead->created_at = $check->created;

                                    $lead->is_create_parse = true;
//                                $lead->is_link_parse = true;
//                        dd($lead);
                                    $lead->save([
                                        'timestamps' => false
                                    ]);

                                    $lead_number = getLeadNumbers($authUser, $lead);
                                    $lead->case_number = $check->id;
                                    $lead->serial_number = $lead_number['serial'];

                                    $lead->save([
                                        'timestamps' => false
                                    ]);

                                    if ($lead) {
                                        $lead->phones()->attach($phone->id, ['main' => 1]);
                                    }
                                }
                            }

                            if ($lead) {
                                $estimate = Estimate::create([
                                    'lead_id' => $lead->id,
                                    'client_id' => $client->id,
                                    'filial_id' => $lead->filial_id,

                                    'discount' => 0,
                                    'discount_percent' => 0,

                                    'margin_currency' => 0,
                                    'margin_percent' => 0,

                                    'amount' => $check->summa,
                                    'total' => $check->summa,

                                    'number' => $lead->case_number,
                                    'date' => $check->created->format('d.m.Y'),
                                    'registered_at' => $check->created,

                                    'company_id' => $companyId,
                                    'author_id' => hideGod($authUser),

                                    'is_main' => 1,
                                    'is_dismissed' => ($check->progress == 2) ? 0 : 1,
                                    'registered_at' => $check->created,
                                    'saled_at' => $check->created,

                                    'created_at' => $check->created,
                                    'timestamps' => false,

                                    'external' => $check->id

                                ]);

                                if ($check->progress != 2) {
                                    echo "Сметы [{$estimate->id}] должна быть списана - {$estimate->is_dismissed}, в старой базе - {$check->dismissed}<br>";
                                }

                                // Сохраняем состав сметы
                                $check->load('consists.price');

                                $estimatesGoodsItemsInsert = [];
                                $consistCount = 0;
                                foreach ($check->consists as $consist) {

                                    $prices = $pricesGoods->filter(function ($price) use ($consist, $lead){
                                        if ($price->filial_id == $lead->filial_id) {
                                            if ($price->goods->article->external == $consist->price_id) {
                                                return $price;
                                            }
                                        }
                                    });

                                    if ($prices->isNotEmpty()) {
                                        if ($prices->count() > 1) {
                                            echo "Несколько артикулов с external {$consist->price_id}<br>";
                                        }
                                        $priceGoods = $prices->first();
                                        $count = $consist->count;
                                        $data = [
                                            'currency_id' => 1,
                                            'goods_id' => $priceGoods->goods->id,
                                            'price_id' => $priceGoods->id,
                                            'author_id' => 1,
                                            'price' => $consist->summa ?? 0,
                                            'count' => $count ?? 0,
                                            'cost' => $priceGoods->goods->article->cost_default * $count,
                                            'amount' => $count * $consist->summa,
                                            'points' => $consist->rh ?? 0,

                                            'created_at' => $consist->created,
                                            'timestamps' => false
                                        ];

                                        $data['discount_percent'] = $consist->discont;
                                        $data['discount_currency'] = ($data['amount'] / 100) * $data['discount_percent'];

                                        if ($data['points'] > 0) {
                                            $data['total'] = 0;
                                        } else {
                                            $data['total'] = $data['amount'] - $data['discount_currency'];
                                        }


                                        $data['margin_currency'] = $data['total'] - $data['cost'];
                                        if ($data['total'] > 0) {
                                            $data['margin_percent'] = ($data['margin_currency'] / $data['total']) * 100;
                                        } else {
                                            $data['margin_percent'] = 0;
                                        }

                                        $estimatesGoodsItemsInsert[] = EstimatesGoodsItem::make($data);
//                                dd($estimatesGoodsItemsInsert);
                                    }

                                    $consistCount++;
                                }

                                $estimate->goods_items()->saveMany($estimatesGoodsItemsInsert);
                                $estimate->load('goods_items');
                                if ($estimate->goods_items->count() != $check->consists->count()) {
                                    echo "У сметы [{$estimate->id}] не сходится состав, у нас {$estimate->goods_items->count()}, у него {$check->consists->count()}<br>";
                                } else {
                                    echo "У сметы [{$estimate->id}] сходится состав<br>";
                                }



                                // Обновляем смету
                                $estimate->load([
                                    'goods_items',
                                ]);

                                $cost = 0;
                                $amount = 0;
                                $total = 0;
                                $points = 0;
                                $discount_items_currency = 0;

                                if ($estimate->goods_items->isNotEmpty()) {
                                    $cost += $estimate->goods_items->sum('cost');
                                    $amount += $estimate->goods_items->sum('amount');
                                    $total += $estimate->goods_items->sum('total');
                                    $points += $estimate->goods_items->sum('points');
                                    $discount_items_currency += $estimate->goods_items->sum('discount_currency');
                                }

                                if ($amount > 0) {
                                    $discount = (($amount * $estimate->discount_percent) / 100);

                                    $margin_currency = $total - $cost;

                                    if ($total > 0) {
                                        $margin_percent = ($margin_currency / $total * 100);
                                    } else {
                                        $margin_percent = 0;
                                    }

                                    $data = [
                                        'cost' => $cost,
                                        'amount' => $amount,
                                        'discount' => $discount,
                                        'total' => $total,
                                        'points' => $points,
                                        'discount_items_currency' => $discount_items_currency,
                                        'margin_currency' => $margin_currency,
                                        'margin_percent' => $margin_percent,
                                        'timestamps' => false
                                    ];

                                } else {
                                    $data = [
                                        'cost' => 0,
                                        'amount' => 0,
                                        'discount' => 0,
                                        'total' => 0,
                                        'points' => 0,
                                        'discount_items_currency' => 0,
                                        'margin_currency' => 0,
                                        'margin_percent' => 0,
                                        'timestamps' => false
                                    ];
                                }

                                $estimate->update($data);

                                $estimate->save([
                                    'created_at' => $check->created,
                                    'timestamps' => false
                                ]);

                                if ($check->summa != $estimate->total) {
                                    echo "Не совпала сумма на смете {$estimate->id}: Наша - {$estimate->total}, Его - {$check->summa}<br>";

                                    if ($check->summa > $estimate->total) {
                                        $losses = ($check->summa - $estimate->total);

                                        if ($estimate->points > 0) {
                                            $estimate->losses_from_points = $losses;
                                            $estimate->save([
                                                'timestamps' => false
                                            ]);
                                        } else {
                                            $estimate->surplus = $losses;
                                            $estimate->save([
                                                'timestamps' => false
                                            ]);
                                        }

                                    } else {
                                        echo "В смете [{$estimate->id}] наш total больше чем его summa<br>";
                                    }
                                }

                                $diff = $check->summa - ($check->cash + $check->cashless);
                                if ($diff != 0) {
                                    echo "В смете [{$estimate->id}] разница между оплатой и суммой заказа = {$diff}<br>";
                                }

                                if ($estimate->is_dismissed == 0) {
                                    $this->setIndicators($estimate);

                                    // Создаем договор
                                    $contracts_client = ContractsClient::create([
                                        'client_id' => $client->id,
                                        'date' => $check->created,
                                        'number' => $lead->case_number,
                                        'amount' => $estimate->total,
                                        'created_at' => $check->created,
                                        'timestamps' => false
                                    ]);
                                }

                                if ($check->progress == 2) {
                                    // Фиксируем платежи
                                    if ($check->cash) {
                                        if ($check->cash > 0) {
                                            $payment = Payment::create([
                                                'contract_id' => $contracts_client->id,
                                                'contract_type' => 'App\ContractsClient',
                                                'document_id' => $estimate->id,
                                                'document_type' => 'App\Estimate',
                                                'payments_type_id' => 1,
                                                'amount' => $check->cash,
                                                'date' => $check->created->format('d.m.Y'),
                                                'currency_id' => 1,
                                                'company_id' => $companyId,
                                                'author_id' => hideGod($authUser),
                                                'created_at' => $check->created,
                                                'timestamps' => false
                                            ]);
                                        }
                                    }

                                    if ($check->cashless) {
                                        if ($check->cashless > 0) {
                                            $payment = Payment::create([
                                                'contract_id' => $contracts_client->id,
                                                'contract_type' => 'App\ContractsClient',
                                                'document_id' => $estimate->id,
                                                'document_type' => 'App\Estimate',
                                                'payments_type_id' => 2,
                                                'amount' => $check->cashless,
                                                'date' => Carbon::parse($check->created)->format('d.m.Y'),
                                                'currency_id' => 1,
                                                'company_id' => $companyId,
                                                'author_id' => hideGod($authUser),
                                                'created_at' => $check->created,
                                                'timestamps' => false
                                            ]);
                                        }
                                    }
                                }
                            }

                        } else {
                            // Дата больше запуска системы и заказ не стол а с сайта - сращиваем
                            $leads = Lead::whereDate('created_at', $check->created)
                                ->where('is_create_parse', false)
                                ->get();
//                            dd($leads);

                            if ($leads) {
                                if($leads->count() == 1) {
                                    $lead = $leads->first();

                                }
                            }
                        }
                    }
                }
            } else {
                echo "У юзера {$oldUser->id} нет заказов<br>";
            }

            $oldUser->is_parse = true;
            $oldUser->save();
        }
//        dd($users->count());

        return 'Гатова';
    }

    /**
     * Удаление локаций с Иркутском для РХ и проставка всем юзерам, у которых нет локаций
     * @return string
     */
    public function userLocation()
    {
        set_time_limit(0);

        $users = User::with([
            'userLeads',
            'location',
            'filial'
        ])
            ->where('site_id', 2)
            ->get();

        foreach($users as $user) {

            if ($user->userLeads->isNotEmpty()) {
                $lead = $user->userLeads->last();

                if (isset($user->location)) {
                    if ($user->location->city_id == 1) {
                        $user->location()->forceDelete();
                        $user->update([
                            'location_id' => $lead->location_id
                        ]);
                    }
                } else {
                    $user->update([
                        'location_id' => $lead->location_id
                    ]);
                }

            } else {
                $location_id = create_location(request(), 1, $user->filial->location->city_id);
                $user->update([
                    'location_id' => $location_id
                ]);
            }
        }

        return "Удалены локации у пользователей с городом Иркутск и проставлены пользователям (тем, у кого не было, или был город Иркутск) локации с последнего лида, у кого нет лидов проставлены локации(город из филиала)";

    }

    /**
     * Парсер оформления смет, создания клиентов
     *
     * @return string
     */
    public function ladClient()
    {
        set_time_limit(0);

        // ГЛАВНЫЙ ЗАПРОС:
        $leads = Lead::with([
            'estimate.goods_items'
        ])
            ->whereHas('estimate', function ($q) {
                $q->whereNull('registered_at');
            })
            ->orderBy('id')
            ->get();

//            dd($leads->count());

        foreach($leads as $lead) {

            $manager = Staffer::find(6);

            // Ищем или создаем клиента
            $client = $this->getClientUser($lead->user_id);


            if (is_null($client->source_id)) {
                $client->update([
                    'source_id' => $lead->source_id
                ]);
            }

            $lead->manager_id = $manager->user_id;

            // Если номер пуст и планируется назначение на сотрудника, а не бота - то генерируем номер!
            if($lead->case_number == NULL){

                // Формируем номера обращения
                $lead_number = getLeadNumbers($manager, $lead);
                $lead->case_number = $lead_number['case'];
                $lead->serial_number = $lead_number['serial'];
            }

            $lead->editor_id = auth()->user()->id;
            $lead->save();

            $estimate = $lead->estimate;

            if (! $estimate->registered_at) {

                logs('documents')->info("========================================== НАЧАЛО РЕГИСТРАЦИИ СМЕТЫ, ID: {$estimate->id} =============================================== ");

                // Обновляем смету
                $amount = 0;
                $discount = 0;
                $total = 0;

                if ($estimate->goods_items->isNotEmpty()) {

                    $amount = $estimate->goods_items->sum('amount');
                    $discount = (($amount * $estimate->discount_percent) / 100);
                    $total = ($amount - $discount);
                }



                $estimate->lead->update([
                    'client_id' =>  $client->id
                ]);

                $contracts_client = ContractsClient::create([
                    'client_id' => $client->id
                ]);

                $estimate->update([
                    'client_id' => $client->id,
                    'registered_at' => $estimate->created_at,
                    'amount' => $amount,
                    'discount' => $discount,
                    'total' => $total,
                ]);

                logs('documents')->info("========================================== КОНЕЦ РЕГИСТРАЦИИ СМЕТЫ, ID: {$estimate->id} =============================================== ");

                if (! $estimate->saled_at) {

                    // Обновляем показатели клиента
                    $this->setIndicators($estimate);

                    $estimate->update([
                        'saled_at' => now(),
                    ]);

                }

            }
        }

        return 'У лидов закрыты сметы и созданы клиенты';
    }

    /**
     * Прописываение всем users поля name
     */
    public function userName()
    {
        $users = User::get();

        foreach ($users as $user) {
            $user->save([
                'name' => 'lol'
            ]);
//            $cur_user = $user;
//            if (isset($cur_user->first_name) || isset($cur_user->second_name)) {
//                $cur_user->name = $cur_user->first_name . ' ' . $cur_user->second_name;
//            } else {
//                $cur_user->name = $cur_user->login;
//            }
//
//            $cur_user->save();
//            dd($cur_user);
        }
        echo "Пользователям проставлено поле name<br><br>";
    }
}
