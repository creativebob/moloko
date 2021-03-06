<?php

namespace App\Http\Controllers\System;

use App\Action;
use App\ActionEntity;
use App\Article;
use App\CatalogsGoods;
use App\CatalogsGoodsItem;
use App\Client;
use App\Dispatch;
use App\EntitiesType;
use App\EstimatesCancelGround;
use App\Http\Controllers\Controller;
use App\Models\System\Documents\ConsignmentsItem;
use App\Models\System\Documents\Estimate;
use App\Goods;
use App\Http\Controllers\System\Traits\Clientable;
use App\Http\Controllers\Traits\UserControllerTrait;
use App\Models\System\Documents\Consignment;
use App\Models\System\Documents\EstimatesGoodsItem;
use App\Models\System\Documents\Production;
use App\Models\System\Documents\ProductionsItem;
use App\Models\System\Stocks\GoodsStock;
use App\OldLead;
use App\OldLocation;
use App\Lead;
use App\Choice;
use App\Challenge;
use App\Claim;
use App\Menu;
use App\Company;
use App\Department;
use App\Payment;
use App\PaymentsSign;
use App\PaymentsType;
use App\Position;
use App\PricesGoods;
use App\Process;
use App\Reserve;
use App\Right;
use App\Role;
use App\Seo;
use App\Subscriber;
use App\User;
use App\Phone;
use App\Page;
use App\Entity;
use App\EntityPage;
use App\Location;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParserController extends Controller
{

    /**
     * ParserController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    use Clientable;
    use UserControllerTrait;

    public function test()
    {
        dd(__METHOD__);
    }

    public function deleteEgiWithoutPrices()
    {
        $res = EstimatesGoodsItem::doesntHave('price')
            ->delete();

        return $res;
    }

    /**
     * Создаем записи сео для сеозависимых сущностей
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function setSeos()
    {
        $entitiesAliases = [
            'catalogs_services_items',
            'catalogs_goods_items',
            'pages',
            'articles',
            'processes',
        ];

        $entities = Entity::whereIn('alias', $entitiesAliases)
            ->get();

        foreach ($entities as $entity) {
            if ($entity->alias == 'articles' || $entity->alias == 'processes') {
                $items = $entity->model::whereNotNull('seo_description')
                    ->get();
            }

            if ($entity->alias == 'catalogs_services_items' || $entity->alias == 'catalogs_goods_items') {
                $items = $entity->model::whereNotNull('seo_description')
                    ->orWhereNotNull('title')
                    ->get();
            }

            if ($entity->alias == 'pages') {
                $items = $entity->model::whereNotNull('description')
                    ->orWhereNotNull('title')
                    ->get();
            }

            foreach($items as $item) {
                if ($entity->alias == 'catalogs_services_items' || $entity->alias == 'catalogs_goods_items') {
                    $dataSeo = [
                        'title' => $item->title,
                        'keywords' => $item->keywords,
                        'description' => $item->seo_description,
                        'h1' => $item->header,
                    ];
                }

                if ($entity->alias == 'articles' || $entity->alias == 'processes') {
                    $dataSeo = [
                        'content' => $item->content,
                        'keywords' => $item->keywords,
                        'description' => $item->seo_description,
                    ];
                }

                if ($entity->alias == 'pages') {
                    $dataSeo = [
                        'title' => $item->title,
                        'keywords' => $item->keywords,
                        'description' => $item->description,
                        'h1' => $item->header,
                        'content' => $item->content,
                    ];
                }

                $seo = Seo::firstOrCreate($dataSeo);
                $item->update([
                    'seo_id' => $seo->id
                ]);
            }

            echo "Создано СЕО для записей сущности: {$entity->name}<br><br>";
        }
        return __('msg.ok');
    }

    /**
     * Проставляем тип сущностям
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function setEntitiesTypes()
    {
        $entitiesTypes = EntitiesType::get();

        $cmv = [
            'goods',
            'raws',
            'containers',
            'attachments',
            'tools',
            'impacts',
            'rooms',
        ];

        Entity::whereIn('alias', $cmv)
            ->update([
                'entities_type_id' => $entitiesTypes->firstWhere('alias', 'cmv')->id
            ]);

        echo "Проставлен тип: ТМЦ<br><br>";

        $processes = [
            'services',
            'workflows',
        ];

        Entity::whereIn('alias', $processes)
            ->update([
                'entities_type_id' => $entitiesTypes->firstWhere('alias', 'process')->id
            ]);

        echo "Проставлен тип: Процесс<br><br>";

        $site = [
            'catalogs_goods',
            'catalogs_services',
            'rubricators',
            'navigations',
            'users',
            'news',
        ];

        Entity::whereIn('alias', $site)
            ->update([
                'entities_type_id' => $entitiesTypes->firstWhere('alias', 'site')->id
            ]);

        echo "Проставлен тип: Сайт<br><br>";

        return __('msg.ok');
    }

    /**
     * Проставляем слаг артикулам
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function setArticlesSlug()
    {
        $articles = Article::whereNull('slug')
            ->get();

        foreach($articles as $article) {
            $article->update([
                'slug' => \Str::slug($article->name),
            ]);
        }

        return __('msg.ok');
    }

    /**
     * Проставляем слаг процессам
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function setProcessesSlug()
    {
        $processes = Process::whereNull('slug')
            ->get();

        foreach($processes as $process) {
            $process->update([
                'slug' => \Str::slug($process->name),
            ]);
        }

        return __('msg.ok');
    }

    /**
     * Добавление основании списания для РХ
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function setRhCancelGrounds()
    {
        EstimatesCancelGround::create([
            'name' => 'Отказ клиента'
        ]);

        EstimatesCancelGround::create([
            'name' => 'Ошибка менеджера'
        ]);

        return __('msg.ok');
    }

    public function updateLeadsOutletId()
    {
        $filials = Department::with([
            'outlets'
        ])
        ->get();
//        dd($groupedLeads);

        foreach ($filials as $filial) {
            if ($filial->outlets->isNotEmpty()) {
                $outletId = $filial->outlets->firstWhere('is_main', true) ? $filial->outlets->firstWhere('is_main', true)->id : $filial->outlets->first()->id;

                Lead::where('filial_id', $filial->id)
                    ->whereNull('outlet_id')
                    ->update([
                        'outlet_id' => $outletId
                    ]);
            }
        }

        return __('msg.ok');
    }

    /**
     * Обновление клиентского филиала на первый
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function updateClientsFilial()
    {
        Client::whereNull('filial_id')
            ->update([
            'filial_id' => 1
        ]);

        return __('msg.ok');
    }

    /**
     * Обновлени типа платежам
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function updatePaymentsSign()
    {
        $paymentsSignId = PaymentsSign::where('alias', 'sell')
            ->value('id');
        $payments = Payment::whereNull('payments_sign_id')
            ->update([
                'payments_sign_id' => $paymentsSignId
            ]);
        return __('msg.ok');
    }

    /**
     * Связь смет со скидками
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function setDiscountsForEstimates()
    {

        $estimates = Estimate::with([
            'goods_items'
        ])
            ->whereHas('goods_items', function ($q) {
                $q->whereNotNull('estimate_discount_id');
            })
            ->get();

        foreach ($estimates as $estimate) {
            $item = $estimate->goods_items->first();

            if ($item->estimate_discount_id) {
                $estimate->discounts()->attach($item->estimate_discount_id);
            }
        }

        return __('msg.ok');
    }

    /**
     * Перерезервирование (если в системе нет продаж)
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function reReserving()
    {

        $storages = GoodsStock::get();
        $groupedReserves = Reserve::get()
        ->groupBy('storage_id');
//        dd(isset($groupedReserves[155]));

        foreach ($storages as $storage) {

            if (isset($groupedReserves[$storage->id])) {

                $count = 0;
                foreach ($groupedReserves[$storage->id] as $reserve) {
                    $count += $reserve->count;
                }

                $storage->update([
                    'free' => $storage->count - $count,
                    'reserve' => $count,
                ]);

            }
        }
        return __('msg.ok');
    }

    /**
     * Проставление хранилища в reserves
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function setStorageForReserves()
    {

        $reserves = Reserve::with([
            'cmv.stocks',
        ])
            ->get();

        foreach ($reserves as $reserve) {

            $storageModel = Entity::where('alias', 'goods_stocks')->value('model');
//            dd($storageModel);

            $storageId = $reserve->cmv->stocks
                ->where('stock_id', $reserve->stock_id)
                ->first()
                ->id;
//            dd($storageId);

            $reserve->update([
                'storage_id' => $storageId,
                'storage_type' => $storageModel,
            ]);
        }
        return __('msg.ok');
    }

    /**
     * Добавление сущностей пунктов документов
     *
     * @return array|string|null
     */
    public function setDocumentsItemsEntities()
    {
        Entity::firstOrCreate([
            'name' => 'Товары сметы',
            'alias' => 'estimates_goods_items',
            'model' => 'App\Models\System\Documents\EstimatesGoodsItem',
            'rights' => false,
            'system' => true,
            'author_id' => 1,
            'ancestor_id' => Entity::whereAlias('estimates')->value('id'),
            'view_path' => 'estimates',
        ]);

        Entity::firstOrCreate([
            'name' => 'Услуги сметы',
            'alias' => 'estimates_services_items',
            'model' => 'App\Models\System\Documents\EstimatesServicesItem',
            'rights' => false,
            'system' => true,
            'author_id' => 1,
            'ancestor_id' => Entity::whereAlias('estimates')->value('id'),
            'view_path' => 'estimates',
        ]);

        Entity::firstOrCreate([
            'name' => 'Пункты накладной',
            'alias' => 'consignments_items',
            'model' => 'App\Models\System\Documents\ConsignmentsItem',
            'rights' => false,
            'system' => true,
            'author_id' => 1,
            'ancestor_id' => Entity::whereAlias('consignments')->value('id'),
            'view_path' => 'consignments',
        ]);

        Entity::firstOrCreate([
            'name' => 'Пункты наряда',
            'alias' => 'productions_items',
            'model' => 'App\Models\System\Documents\ProductionsItem',
            'rights' => false,
            'system' => true,
            'author_id' => 1,
            'ancestor_id' => Entity::whereAlias('productions')->value('id'),
            'view_path' => 'productions',
        ]);

        return __('msg.ok');
    }

    /**
     * Проставление хранилища в receipts и offs
     *
     * @return string
     */
    public function setStorageForProductionsItems()
    {

        $items = ProductionsItem::with([
            'cmv.stocks',
            'entity',
            'receipt' => function ($q) {
                $q->where('storage_id', 0)
                    ->orWhereNull('storage_id');
            },
            'offs' => function ($q) {
                $q->with([
                    'cmv' => function ($q) {
                        $q->with([
                            'stocks',
                            'article'
                        ]);
                    }
                ])
                    ->where('storage_id', 0)
                    ->orWhereNull('storage_id');
            },
            'production'
        ])
            ->whereHas('production', function ($q) {
                $q->whereNotNull('conducted_at')
                    ->whereDate('conducted_at', '>', '2020-08-01');
            })
            ->get();

        foreach ($items as $item) {

            $item->update([
                'stock_id' => $item->production->stock_id
            ]);

            $storageType = Entity::where('alias', $item->entity->alias . '_stocks')
                ->value('model');
//            dd($storageType);

            $storageId = $item->cmv->stocks
                ->where('stock_id', $item->stock_id)
                ->where('manufacturer_id', $item->manufacturer_id)
                ->first()
                ->id;
//            dd($storageId);

            $item->receipt()->update([
                'storage_id' => $storageId,
                'storage_type' => $storageType,
            ]);

            foreach ($item->offs as $off) {
//                dd($off);

                $storageType = Entity::where('alias', $off->cmv->getTable() . '_stocks')
                    ->value('model');
//            dd($storageType);

                $storageId = $off->cmv->stocks
                    ->where('stock_id', $item->stock_id)
                    ->where('manufacturer_id', $off->cmv->article->manufacturer_id)
                    ->first()
                    ->id;
//            dd($storageId);

                $off->update([
                    'storage_id' => $storageId,
                    'storage_type' => $storageType,
                ]);
            }

        }
        return "Гатова!";
    }

    /**
     * Проставление хранилища в receipts
     *
     * @return string
     */
    public function setStorageForConsignmentsItems()
    {

        $items = ConsignmentsItem::with([
            'cmv.stocks',
            'entity',
            'receipt' => function ($q) {
                $q->where('storage_id', 0)
                    ->orWhereNull('storage_id');
            },
            'consignment'
        ])
            ->whereHas('consignment', function ($q) {
                $q->whereNotNull('conducted_at')
                    ->whereDate('conducted_at', '>', '2020-08-01');
            })
            ->get();
//        dd($items);

        foreach ($items as $item) {
//            dd($item);

            $item->update([
                'stock_id' => $item->consignment->stock_id
            ]);

            $storageType = Entity::where('alias', $item->entity->alias . '_stocks')
                ->value('model');
//            dd($storageType);

            $storageId = $item->cmv->stocks
                ->where('stock_id', $item->stock_id)
                ->where('manufacturer_id', $item->manufacturer_id)
                ->first()
                ->id;
//            dd($storageId);

            $item->receipt()->update([
                'storage_id' => $storageId,
                'storage_type' => $storageType,
            ]);

        }
        return "Гатова!";
    }

    /**
     * Создание подписчиков из пользователей (не сотрудников)
     *
     * @return string
     */
    public function createSubscribersFromUsers()
    {
        $users = User::with([
            'client'
//            'notifications'
        ])
            ->where('site_id', '!=', 1)
            ->whereNotNull('email')
            ->get();

        foreach ($users as $user) {

//            $allow = $user->notifications->firstWhere('id', 4);

            $validator = Validator::make([
                'email' => $user->email
            ], [
                'email' => 'email',
            ]);


            $subscriber = Subscriber::firstOrCreate([
                'email' => $user->email,
                'site_id' => $user->site_id
            ]);

            $subscriber->update([
                'subscriberable_id' => $user->id,
                'subscriberable_type' => 'App\User',
                'client_id' => optional($user->client)->id,
                'name' => $user->name,
                'is_valid' => $validator->fails() ? false : true,
                'denied_at' => null,
//                'denied_at' => isset($allow) ? null : $user->updated_at,
                'is_self' => 1
            ]);
        }

        return "Гатова";
    }

    /**
     * Обновление даты отправки в dispatches
     *
     * @return string
     */
    public function setSendedAt()
    {
        $dispatches = Dispatch::whereNull('sended_at')
            ->get();

        foreach ($dispatches as $dispatch) {
            $dispatch->update([
                'sended_at' => $dispatch->created_at
            ]);
        }
        return "Гатова";
    }

    /**
     * Парсер таблицы платежей
     *
     * @return string
     */
    public function updatePayments()
    {
        $payments = Payment::get();

        foreach ($payments as $payment) {
            $data = [
                'registered_at' => $payment->created_at,
                'payments_method_id' => 4,
            ];
            if ($payment->document_type == 'App\Estimate') {
                $data['document_type'] = 'App\Models\System\Documents\Estimate';
            }

            if ($payment->payments_type_id == 1) {
                $data['cash'] = $payment->amount;
                $data['electronically'] = 0;
                $data['total'] = $payment->amount;
                $data['type'] = 'cash';

                $data['cash_taken'] = $payment->amount;
                $data['cash_change'] = 0;
            } else {
                $data['cash'] = 0;
                $data['electronically'] = $payment->amount;
                $data['total'] = $payment->amount;
                $data['type'] = 'electronically';
            }

            $payment->update($data);
        }
        return "Гатова";
    }

    /**
     * Обновление моделей для сущностей производства и складов
     *
     * @return string
     */
    public function updateProductionsEntitiesModel()
    {
        $entities = Entity::get();
        foreach ($entities as $entity) {
            if ($entity->model == "Production" || $entity->model == "Consignment" || $entity->model == "Estimate") {
                $entity->update([
                    'model' => "App\Models\System\Documents\\{$entity->model}"
                ]);
            } else if ($entity->model == "GoodsStock" || $entity->model == "RawsStock" || $entity->model == "ContainersStock" || $entity->model == "AttachmentsStock" || $entity->model == "ToolsStock") {
                $entity->update([
                    'model' => "App\Models\System\Stocks\\{$entity->model}"
                ]);
            } else {
                $entity->update([
                    'model' => "App\\{$entity->model}"
                ]);
            }
        }

        return "Модели сущностей обновлены";
    }

    /**
     * Обновление смет
     *
     * @return string
     */
    public function setRegisteredAt()
    {
        $estimates = Estimate::get();
        foreach ($estimates as $estimate) {
            $data = [];
            if ($estimate->is_registered == 1) {
                $data['registered_at'] = $estimate->updated_at;
            }
            if ($estimate->is_saled == 1) {
                $data['conducted_at'] = $estimate->updated_at;
            }
            $estimate->update($data);
        }

        return "Сметам проставлено registered_at и conducted_at";
    }

    /**
     * Обновление накладных
     *
     * @return string
     */
    public function setReceiptedAt()
    {
        $consignments = Consignment::where('is_posted', 1)
            ->get();
        foreach ($consignments as $consignment) {
            $consignment->update([
                'conducted_at' => $consignment->updated_at
            ]);
        }

        return "Накладным проставлено conducted_at";
    }

    /**
     * Обновление нарядов
     *
     * @return string
     */
    public function setProducedAt()
    {
        $productions = Production::where('is_produced', 1)
            ->get();
        foreach ($productions as $production) {
            $production->update([
                'conducted_at' => $production->updated_at
            ]);
        }

        return "Нарядам проставлено conducted_at";
    }

    /**
     * Запуск команды на перерегистрацию документов
     */
    public function startRegisteringDocumentsCommand()
    {
        \Artisan::call("documents:registering");
    }

    /**
     * Установка организаций для компании
     *
     * @return string
     */
    public function setOrganizations()
    {
        $company = auth()->user()->company;

        $organizations = Company::where('id', '!=', $company->id)
            ->get();

        $data = [];
        foreach ($organizations as $organization) {
            $data[$organization->id] = [
                'company_id' => $company->id,
            ];
        }

        $company->organizations()->attach($data);

        return "Проставлены связи компании с организациями";
    }

    public function setMorphsAliases()
    {
        \DB::table('phone_entities')
            ->where('phone_entity_type', 'App\User')
            ->update(['phone_entity_type' => 'User']);
        \DB::table('phone_entities')
            ->where('phone_entity_type', 'App\Company')
            ->update(['phone_entity_type' => 'Company']);
        \DB::table('phone_entities')
            ->where('phone_entity_type', 'App\Department')
            ->update(['phone_entity_type' => 'Department']);
        \DB::table('phone_entities')
            ->where('phone_entity_type', 'App\Lead')
            ->update(['phone_entity_type' => 'Lead']);

        $clients = Client::get();
        foreach ($clients as $client) {
            switch ($client->phone_entity_type) {
                case ('App\User'):
                    $client->phone_entity_type = 'User';
                    break;
                case ('App\Company'):
                    $client->phone_entity_type = 'Company';
                    break;
            }
            $client->save();
        }

        return "Проставлены morphs алиасы телефонам и клиентам";
    }

    /**
     * Для добавления мне прав на ВК (т.к. у Маши отклдчены права на роли)
     *
     * @return string
     */
    public function addRole()
    {
        $user = User::where('login', 'developer_vk')
            ->first();

        $user->roles()->attach(2, [
            'department_id' => 1,
            'role_id' => 1,
            'position_id' => 1,
            'user_id' => $user->id,
        ]);

        return "developer_vk добавлены права";
    }

    /**
     * Проставление total прайсам товаров
     *
     * @return string
     */
    public function parserPricesGoodsTotal()
    {
        $pricesGoods = PricesGoods::get();
        foreach ($pricesGoods as $priceGoods) {
            $total = $priceGoods->price - $priceGoods->discount_currency;
            $priceGoods->update([
                'total' => $total
            ]);
        }
        return "Прайсам товаров проставлен total";

    }

    /**
     * Архивирование товаров
     *
     * @return string
     */
    public function parserArchiveGoods()
    {
        $goods = Goods::with([
            'prices'
        ])
            ->get();

        foreach ($goods as $curGood) {
            if ($curGood->prices->isNotEmpty()) {
                foreach ($curGood->prices as $price) {
                    $result = $price->update([
                        'archive' => true,
                    ]);
                }
            }

            $curGood->update([
                'archive' => true,
            ]);
        }

        return "Все товары найденные в системе и их прайсы перемещены в архив";
    }

    /**
     * Парсер имен для пользователей, id страницы для должностей
     *
     * @return string
     */
    public function parser_130420()
    {
        $users = User::whereNull('name')
            ->get();

        foreach ($users as $user) {
            $cur_user = $user;
            $cur_user->name = $cur_user->first_name . ' ' . $cur_user->second_name;
//            dd($user);
            $cur_user->save();
        }

        echo "Пользователям проставлено поле name<br><br>";

        $page_id = Page::where('alias', 'dashboard')->value('id');
        $positions = Position::where('system', false)->update([
            'page_id' => $page_id
        ]);

        echo "Должностям проставлен dashboard<br><br>";

        $roles = Role::whereNull('company_id')
            ->where('id', '!=', 1)
            ->update([
                'company_id' => 1
            ]);
        echo "Ролям проставлена компания<br><br>";

        return "Парсинг закончен";

    }

    /**
     * Парсер метрик товаров для РХ
     *
     * @return string
     */
    public function parserRhGoodsMetrics()
    {
        $metrics = \DB::table('entity_metric_value')
            ->where('entity_type', 'App\Goods')
            ->get();

        $insert = [];
        foreach ($metrics as $metric) {
            $insert[] = [
                'goods_id' => $metric->entity_id,
                'metric_id' => $metric->metric_id,
                'value' => $metric->value,
            ];
        }

        \DB::table('goods_metric')
            ->insert($insert);

        return 'Гатова';
    }

    /**
     * Парсер каталогов для РХ
     */
    public function parserRollhouseCatalogs()
    {
        $old_catalog_goods = CatalogsGoods::first();
        $catalog_goods = $old_catalog_goods->replicate();
        $catalog_goods->save();

        $old_catalogs_goods_items = CatalogsGoodsItem::whereNull('parent_id')
            ->get();

        foreach ($old_catalogs_goods_items as $old_item) {
            $item = $old_item->replicate();
            $item->catalogs_goods_id = $catalog_goods->id;
            $item->save();

            $old_item->load('prices');

            if ($old_item->prices) {
                foreach ($old_item->prices as $old_price) {
                    if ($old_price->filial_id == 2) {
                        $old_price->catalogs_goods_item_id = $item->id;
                        $old_price->catalogs_goods_id = $catalog_goods->id;
                        $old_price->save();
                    }
                }
            }

            $old_item->load('childs');

            if ($old_item->childs) {
                foreach ($old_item->childs as $old_child_1) {
                    $child_item_1 = $old_child_1->replicate();
                    $child_item_1->parent_id = $item->id;
                    $child_item_1->category_id = $item->id;
                    $child_item_1->catalogs_goods_id = $catalog_goods->id;
                    $child_item_1->save();

                    $old_child_1->load('prices');

                    if ($old_child_1->prices) {
                        foreach ($old_child_1->prices as $old_price) {
                            if ($old_price->filial_id == 2) {
                                $old_price->catalogs_goods_item_id = $child_item_1->id;
                                $old_price->catalogs_goods_id = $catalog_goods->id;
                                $old_price->save();
                            }
                        }
                    }

                    $old_child_1->load('childs');

                    if ($old_child_1->childs) {
                        foreach ($old_child_1->childs as $old_child_2) {
                            $child_item_2 = $old_child_2->replicate();
                            $child_item_2->parent_id = $child_item_1->id;
                            $child_item_2->category_id = $item->id;
                            $child_item_2->catalogs_goods_id = $catalog_goods->id;
                            $child_item_2->save();

                            $old_child_2->load('prices');

                            if ($old_child_2->prices) {
                                foreach ($old_child_2->prices as $old_price) {
                                    if ($old_price->filial_id == 2) {
                                        $old_price->catalogs_goods_item_id = $child_item_2->id;
                                        $old_price->catalogs_goods_id = $catalog_goods->id;
                                        $old_price->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        echo 'Гатова';

    }

    /**
     * Парсер для обновления
     */
    public function parser()
    {

        Page::insert([
            [
                'name' => 'Разделы портфолио',
                'site_id' => 1,
                'title' => 'Разделы портфолио',
                'description' => 'Разделы портфолио',
                'alias' => 'portfolios_items',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Категории выполненных работ',
                'site_id' => 1,
                'title' => 'Категории выполненных работ',
                'description' => 'Категории выполненных работ',
                'alias' => 'outcomes_categories',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Выполненные работы',
                'site_id' => 1,
                'title' => 'Выполненные работы',
                'description' => 'Выполненные работы',
                'alias' => 'outcomes',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
        ]);

        echo 'Созданы страницы<br>';

//        Menu::insert([
//            //  Вложения
//            //  Инструменты
//            [
//                'name' => 'Инструменты',
//                'icon' => 'icon-tool',
//                'alias' => null,
//                'tag' => 'tools',
//                'parent_id' => null,
//                'page_id' => null,
//                'navigation_id' => 1,
//                'company_id' => null,
//                'system' => true,
//                'author_id' => 1,
//                'display' => true,
//                'sort' => 14,
//            ],
//
//            //  Помещения
//            [
//                'name' => 'Помещения',
//                'icon' => 'icon-room',
//                'alias' => null,
//                'tag' => 'rooms',
//                'parent_id' => null,
//                'page_id' => null,
//                'navigation_id' => 1,
//                'company_id' => null,
//                'system' => true,
//                'author_id' => 1,
//                'display' => true,
//                'sort' => 15,
//            ],
//
//        ]);
//
//        echo "Добавлены 2 главные категори меню<br>";


        $pages = Page::get();
        $menus = Menu::get();

//        $menu = Menu::where([
//            'navigation_id' => 1,
//            'tag' => 'tools'
//        ])->first();
//        $menu->update([
//            'parent_id' => $menus->where('icon', 'icon-tool')->first()->id,
//        ]);
//
//        $menu = Menu::where([
//            'navigation_id' => 1,
//            'tag' => 'tools_categories'
//        ])->first();
//        $menu->update([
//            'parent_id' => $menus->where('icon', 'icon-tool')->first()->id,
//        ]);

//        echo "Перенесены инструменты<br>";

        Menu::insert([
            [
                'name' => 'Категории выполненных работы',
                'icon' => null,
                'alias' => 'admin/outcomes_categories',
                'tag' => 'outcomes_categories',
                'parent_id' => $menus->where('tag', 'productions')->first()->id,
                'page_id' => $pages->where('alias', 'outcomes_categories')->first()->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 4,
            ],
            [
                'name' => 'Выполненные работы',
                'icon' => null,
                'alias' => 'admin/outcomes',
                'tag' => 'outcomes',
                'parent_id' => $menus->where('tag', 'productions')->first()->id,
                'page_id' => $pages->where('alias', 'outcomes')->first()->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 5,
            ],
        ]);

        echo "Пункты меню<br>";

//        $menu = Menu::where([
//            'navigation_id' => 1,
//            'tag' => 'rooms'
//        ])->first();
//        $menu->update([
//            'parent_id' => $menus->where('icon', 'icon-room')->first()->id,
//        ]);
//
//        $menu = Menu::where([
//            'navigation_id' => 1,
//            'tag' => 'rooms_categories'
//        ])->first();
//        $menu->update([
//            'parent_id' => $menus->where('icon', 'icon-room')->first()->id,
//        ]);
//
//        $menu = Menu::where([
//            'navigation_id' => 1,
//            'tag' => 'stocks'
//        ])->first();
//        $menu->update([
//            'parent_id' => $menus->where('icon', 'icon-room')->first()->id,
//        ]);
//
//        echo "Перенесены комнаты и склады<br>";


        Entity::insert([
            [
                'name' => 'Категории выполненных работ',
                'alias' => 'outcomes_categories',
                'model' => 'OutcomesCategory',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'metric' => 0,
                'view_path' => 'system.pages.outcomes_categories',
                'page_id' => $pages->firstWhere('alias', 'outcomes_categories')->id,
            ],
        ]);


        Entity::insert([
            [
                'name' => 'Разделы портфолио',
                'alias' => 'portfolios_items',
                'model' => 'PortfoliosItem',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('portfolios')->first(['id'])->id,
                'view_path' => 'system.pages.portfolios_items',
                'page_id' => $pages->firstWhere('alias', 'portfolios_items')->id,
            ],
            [
                'name' => 'Выполненные работы',
                'alias' => 'outcomes',
                'model' => 'Outcome',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('outcomes_categories')->first(['id'])->id,
                'view_path' => 'system.pages.outcomes',
                'page_id' => $pages->firstWhere('alias', 'outcomes')->id,
            ],

        ]);

        echo 'Созданы сущности<br>';


        // Наваливание прав

        // Добавленным
        $entities = Entity::whereIn('alias', [
            'portfolios_items',
            'outcomes_categories',
            'outcomes',
        ])
            ->get();
        // Всем
//        $entities = Entity::get();

        foreach ($entities as $entity) {
            // Генерируем права
            $actions = Action::get();
            $mass = [];

            foreach ($actions as $action) {
                $mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];
            };
            DB::table('action_entity')->insert($mass);

            $actionentities = ActionEntity::where('entity_id', $entity->id)->get();
            $mass = [];

            foreach ($actionentities as $actionentity) {

                $mass[] = ['name' => "Разрешение на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'allow', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-allow'];

                $mass[] = ['name' => "Запрет на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'deny', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-deny'];
            };

            DB::table('rights')->insert($mass);

            $actionentities = $actionentities->pluck('id')->toArray();

            // Получаем все существующие разрешения (allow)
            $rights = Right::whereIn('object_entity', $actionentities)->where('directive', 'allow')->get();

            $mass = [];
            // Генерируем права на полный доступ
            foreach ($rights as $right) {
                $mass[] = ['right_id' => $right->id, 'role_id' => 1, 'system' => 1];
            };

            DB::table('right_role')->insert($mass);

            $mass = null;
            $mass = [];
            foreach ($rights as $right) {
                $mass[] = ['right_id' => $right->id, 'role_id' => 2, 'system' => 1];
            };

            DB::table('right_role')->insert($mass);
        }

        echo "Навалены права<br>";
    }

    public function update_menus(Request $request)
    {

        $delete = Menu::where('navigation_id', 1)->delete();
        $menus = Menu::where('navigation_id', 2)->update(['navigation_id' => 1]);

        dd('Все готово! Вы молодец.');

    }

    public function entity_page(Request $request)
    {

        $entities = Entity::get(['id', 'alias']);
        $pages = Page::where('site_id', 1)->get(['id', 'alias']);

        $count = 0;
        foreach ($entities as $entity) {
            foreach ($pages as $page) {
                if ($entity->alias == $page->alias) {

                    $entity_page = EntityPage::firstOrCreate(['entity_id' => $entity->id, 'page_id' => $page->id]);

                    $delete = EntityPage::where('id', '!=', $entity_page->id)->where('entity_id', $entity->id)->delete();

                    $count++;
                }
            }
        }


        dd('Гатова, всего: ' . $count);

    }

    public function locations(Request $request)
    {

        $old_leads = OldLead::where('address_company', '')->update(['address_company' => null]);

        $locations = Location::where('address', '')->update(['address' => null]);

        $users = User::with('location')->get();

        foreach ($users as $user) {

            $location_old = $user->location;
            // dd($location_old);

            $location = Location::firstOrCreate(['address' => $location_old->address, 'city_id' => $location_old->city_id, 'country_id' => $location_old->country_id, 'author_id' => $location_old->author_id]);
            // dd($location);

            if ($location->id != $user->location_id) {
                $user->location()->forceDelete();

                $user->location_id = $location->id;
                $user->save();
            }
        }


        $leads = Lead::with('location')->whereNotNull('old_lead_id')->get();
        // dd($leads);

        foreach ($leads as $lead) {

            $old_lead = OldLead::with('city')->find($lead->old_lead_id);

            // dd($lead->location);
            if (isset($lead->location->address)) {
                $address = ($lead->location->address != $old_lead->address_company) ? $lead->location->address : $old_lead->address_company;
            } else {
                $address = $old_lead->address_company;
            }

            $location = Location::firstOrCreate(['address' => $address, 'city_id' => $old_lead->city->new_city_id], ['country_id' => 1, 'author_id' => 1]);

            if ($location->id != $lead->location_id) {
                $lead->location()->forceDelete();

                $lead->location_id = $location->id;
                $lead->save();
            }
        }

        $locations = Location::whereNull('country_id')->update(['country_id' => 1]);

        dd('Гатова');

    }

    public function geoposition_locations(Request $request)
    {
        $locations = Location::with('city')->whereNull('answer_count')->get();

        $count = 0;

        foreach ($locations as $location) {

            // Формируем запрос в Яндекс Карты
            $request_params = [
                'geocode' => $location->city->name . ', ' . $location->address,
                'format' => 'json',
            ];
            // Преобразуем его в GET строку
            $params = http_build_query($request_params);
            // dd($get_params);
            // Отправляем
            $result = (file_get_contents('https://geocode-maps.yandex.ru/1.x/?' . $params));
            // dd($get_params);

            $res = json_decode($result);
            if (count($res->response->GeoObjectCollection->featureMember) == 1) {

                $string = $res->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos;
                $coords = explode(' ', $string);
                $update_location = Location::whereId($location->id)->update(['longitude' => $coords[0], 'latitude' => $coords[1], 'parse_count' => 1, 'answer_count' => 1]);
            } else {
                $update_location = Location::whereId($location->id)->update(['answer_count' => count($res->response->GeoObjectCollection->featureMember)]);
            }

            $count++;
            echo 'Есть - ' . $count . "\r\n";
        }

        dd('Гатова - ' . $count);

    }

    public function geoposition_locations_parse(Request $request)
    {

        $locations = OldLocation::get();

        foreach ($locations as $location) {

            $update = Location::where('id', $location->id)->update(['latitude' => $location->latitude, 'longitude' => $location->longitude, 'parse_count' => $location->parse_count, 'answer_count' => $location->answer_count]);
        }

        dd('Гатова');

    }

    public function challenges_active_count()
    {


        $leads = Lead::with('challenges_active')->get();

        foreach ($leads as $lead) {

            if ($lead->challenges_active->count() > 0) {
                $leads = Lead::where('id', $lead->id)
                    ->update(['challenges_active_count' => $lead->challenges_active->count()]);
            } else {
                $leads = Lead::where('id', $lead->id)
                    ->update(['challenges_active_count' => 0]);
            };
        }

        dd('Готово!!!');

    }

    public function choice_parser()
    {

        $choices = Choice::get();
        foreach ($choices as $choice) {


            if (isset($choice->lead_id)) {
                $leads = Lead::where('id', $choice->lead_id)
                    ->update(['choice_id' => $choice->choices_id, 'choice_type' => $choice->choices_type]);
            }

        }


        dd('Готово!!!');
    }

    public function index(Request $request)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // $cities = City::get('id', 'name');

        set_time_limit(0);

        $old_leads = OldLead::with(['comments.author', 'claims.author', 'task', 'stage', 'manager', 'city', 'service', 'challenges' => function ($query) {
            $query->with('author', 'appointed', 'finisher', 'stage', 'task');
        }])->whereNull('parse_status')->where('phone_contact', '!=', '')->get();

        // dd($old)

        // OldLead::with(['comments.author', 'claims.author', 'task', 'stage', 'manager', 'city', 'service', 'challenges' => function ($query) {
        //     $query->with('author', 'appointed', 'finisher', 'stage', 'task');
        // }])->whereNull('parse_status')->where('phone_contact', '!=', '')->chunk(100, function ($leads) {

        $mass = [];

        foreach ($old_leads as $old_lead) {

            $mass['get_lead'] = $old_lead->id;

            // Пишем локацию
            $location = new Location;
            $location->country_id = 1;
            $location->address = $old_lead->address_company;

            if ($old_lead->id_city == 0) {
                $location->city_id = 2;
            } else {
                $location->city_id = $old_lead->city->new_city_id;
            }

            $location->author_id = 1;
            $location->save();

            if ($location) {
                $location_id = $location->id;

                $mass['location'] = $location;
            } else {
                dd('Ошибка записи адреса для лида: ' . $old_lead->name_contact);
            }

            // Определяем тип лида
            $lead_type_id = null;
            if ($old_lead->status_site == 1) {
                $lead_type_id = 2;
            } else {
                $lead_type_id = 1;
            }

            // Начинаем писать лида
            $lead = new Lead;
            $lead->company_id = 1;
            $lead->filial_id = 1;
            $lead->name = $old_lead->name_contact;
            $lead->description = null;
            $lead->badget = $old_lead->badget;
            $lead->case_number = $old_lead->num_order;
            $lead->serial_number = $old_lead->serial_order;
            $lead->phone = cleanPhone($old_lead->phone_contact);
            $lead->email = $old_lead->email_contact;
            $lead->location_id = $location_id;

            // Определяем тип лида
            $lead_type_id = null;
            if ($old_lead->status_site == 1) {
                $lead->lead_type_id = 2;
                $lead->site_id = 2;
            } else {
                $lead->lead_type_id = 1;
            }

            $lead->manager_id = $old_lead->manager->new_user_id;
            $lead->stage_id = $old_lead->stage->new_stage_id;
            $lead->old_lead_id = $old_lead->id;
            $lead->display = 1;
            $lead->author_id = $old_lead->manager->new_user_id;
            $lead->created_at = $old_lead->date_order;
            $lead->save();

            $mass['save_lead'] = $lead->id;

            if ($lead) {
                $lead_id = $lead->id;

                $mass['lead'] = $lead;
            } else {
                dd('Ошибка записи лида: ' . $old_lead->name_contact);
            }

            // dd($old_lead);
            // Пишем комменты
            $lead_comments = [];

            if ($old_lead->comment != '') {
                $lead_comments[] = [
                    'body' => $old_lead->comment,
                    'company_id' => 1,
                    'author_id' => $old_lead->manager->new_user_id,
                    'created_at' => $old_lead->date_order,
                ];
            }

            if ($old_lead->comment2 != '') {
                $lead_comments[] = [
                    'body' => $old_lead->comment2,
                    'author_id' => $old_lead->manager->new_user_id,
                    'created_at' => $old_lead->date_order,
                ];
            }

            if (count($old_lead->comments) > 0) {

                foreach ($old_lead->comments as $comment) {
                    if ($comment->body_note != '') {
                        $lead_comments[] = [
                            'body' => $comment->body_note,
                            'company_id' => 1,
                            'author_id' => $old_lead->manager->new_user_id,
                            'created_at' => $comment->date_note . ' ' . $comment->time_note . ':00',
                        ];
                    }

                }
            }

            if ($old_lead->fact_pay != 0) {
                $fact_pay = num_format($old_lead->fact_pay, 0);
                $lead_comments[] = [
                    'body' => 'Фактически оплачено: ' . $fact_pay,
                    'company_id' => 1,
                    'author_id' => $old_lead->manager->new_user_id,
                    'created_at' => Carbon::now(),
                ];
            }

            // dd($lead_comments);

            $lead->notes()->createMany($lead_comments);

            $mass['comments'] = $lead_comments;

            // Пишем задачи
            if (count($old_lead->challenges) > 0) {
                $lead_challenges = [];

                foreach ($old_lead->challenges as $challenge) {

                    $status = null;
                    if ($challenge->status_challenge == 2) {
                        $status = 1;
                    }

                    if ($challenge->id_model_challenge == 2) {
                        $challenge_type_id = 2;
                    } elseif ($challenge->id_model_challenge == 4) {
                        $challenge_type_id = 3;
                    } else {
                        $challenge_type_id = 1;
                    }

                    if (isset($challenge->finisher->new_user_id)) {
                        $finisher_id = $challenge->finisher->new_user_id;
                        # code...
                    } else {
                        $finisher_id = null;
                    }


                    if ($challenge->deadline_challenge == '0000-00-00 00:00:00') {
                        $deadline_challenge = null;
                    } else {
                        $deadline_challenge = $challenge->deadline_challenge;
                    };

                    if ($challenge->date_completed == '0000-00-00 00:00:00') {
                        $date_completed = null;
                    } else {
                        $date_completed = $challenge->date_completed;
                    };

                    $lead_challenges[] = [
                        'company_id' => 1,
                        'description' => $challenge->comment_challenge,
                        'appointed_id' => $challenge->appointed->new_user_id,
                        'finisher_id' => $finisher_id,
                        'author_id' => $challenge->author->new_user_id,
                        'deadline_date' => $deadline_challenge,
                        'status' => $status,
                        'completed_date' => $date_completed,
                        'challenges_type_id' => $challenge_type_id,
                        'created_at' => $challenge->date_challenge,
                    ];
                }

                // dd($lead_challenges);
                $lead->challenges()->createMany($lead_challenges);

                $mass['lead_challenges'] = $lead_challenges;
            }

            // Пишем рекламации
            if (count($old_lead->claims) > 0) {
                $lead_claims = [];

                foreach ($old_lead->claims as $claim) {

                    $lead_claims[] = [
                        'company_id' => 1,
                        'body' => $claim->body_claim,
                        // 'lead_id' => $lead_id,
                        'old_claim_id' => $claim->id,
                        'author_id' => $claim->author->new_user_id,
                        'created_at' => $claim->date_claim,

                    ];

                }
                // dd($lead_claims);
                $lead->claims()->createMany($lead_claims);

                $mass['lead_claims'] = $lead_claims;
            }

            if (isset($old_lead->service->choise_type)) {

                $choice = new Choice;
                $choice->lead_id = $lead_id;
                $choice->choices_id = $old_lead->service->choise_id;
                $choice->choices_type = $old_lead->service->choise_type;
                $choice->save();

                if ($choice == false) {
                    dd('Ошибка записи предпочтения лида.');
                }

                $mass['choice'] = $choice;
            }

            $old_lead->parse_status = 1;
            $old_lead->save();

            if ($old_lead) {

                $mass['old_lead'] = $old_lead;

                $mass['parse_lead'] = $old_lead->parse_status;
            } else {
                dd('Ошибка проставления отметки старому лиду: ' . $old_lead->id);

            }


            echo 'Текущий лид: ' . $mass['get_lead'] . ', записан в новую базу: ' . $mass['save_lead'] . ', отметка: ' . $mass['parse_lead'] . "\r\n";

        }
        // echo '500 записей, id: '.$lead->id.', ';
        // });

        return redirect('/admin/leads');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function dublicator(Request $request)
    {

        set_time_limit(0);
        $leads = Lead::with('location', 'notes', 'challenges', 'claims')->whereNotNull('old_lead_id')->get(['id', 'old_lead_id']);
        // dd($leads);
        $mass = [];
        $count = 0;
        foreach ($leads as $lead) {

            $dubl_lead = Lead::where('old_lead_id', $lead->old_lead_id)->where('id', '!=', $lead->id)->first();

            if ($dubl_lead) {
                // dd($dubl_lead);

                $dubl_lead->location()->forceDelete();
                $dubl_lead->notes()->forceDelete();
                $dubl_lead->challenges()->forceDelete();
                $dubl_lead->claims()->forceDelete();
                $dubl_lead->choices_goods_categories()->forceDelete();
                $dubl_lead->choices_services_categories()->forceDelete();
                $dubl_lead->choices_raws_categories()->forceDelete();

                $dubl_lead->forceDelete();

                if ($dubl_lead) {
                    // $mass[$dubl_lead->id][] = $dubl_lead->case_number;
                    // $mass[$dubl_lead->id][] = $dubl_lead->old_lead_id;
                    $count++;
                }


            }

            if ($count == 200) {
                // break;
                return redirect('/admin/cities');
            }

        }


        return redirect('/admin/users');
        // $mass['count'] = $count;
        // dd($mass);


    }

    public function adder(Request $request)
    {
        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // $cities = City::get('id', 'name');

        set_time_limit(0);

        $old_leads = OldLead::with(['comments.author', 'claims.author', 'task', 'stage', 'manager', 'city', 'service', 'challenges' => function ($query) {
            $query->with('author', 'appointed', 'finisher', 'stage', 'task');
        }])->whereNull('parse_status')->where('phone_contact', '!=', '')->get();

        // dd($old)

        // OldLead::with(['comments.author', 'claims.author', 'task', 'stage', 'manager', 'city', 'service', 'challenges' => function ($query) {
        //     $query->with('author', 'appointed', 'finisher', 'stage', 'task');
        // }])->whereNull('parse_status')->where('phone_contact', '!=', '')->chunk(100, function ($leads) {

        $mass = [];

        foreach ($old_leads as $old_lead) {

            $new_lead = Lead::where('old_lead_id', $old_lead->id)->first();

            if ($new_lead) {

                $old_lead->parse_status = 1;
                $old_lead->save();

            } else {

                $mass['get_lead'] = $old_lead->id;

                // Пишем локацию
                $location = new Location;
                $location->country_id = 1;
                $location->address = $old_lead->address_company;

                if ($old_lead->id_city == 0) {
                    $location->city_id = 2;
                } else {
                    $location->city_id = $old_lead->city->new_city_id;
                }

                $location->author_id = 1;
                $location->save();

                if ($location) {
                    $location_id = $location->id;

                    $mass['location'] = $location;
                } else {
                    dd('Ошибка записи адреса для лида: ' . $old_lead->name_contact);
                }

                // Определяем тип лида
                $lead_type_id = null;
                if ($old_lead->status_site == 1) {
                    $lead_type_id = 2;
                } else {
                    $lead_type_id = 1;
                }

                // Начинаем писать лида
                $lead = new Lead;
                $lead->company_id = 1;
                $lead->filial_id = 1;
                $lead->name = $old_lead->name_contact;
                $lead->description = null;
                $lead->badget = $old_lead->badget;
                $lead->case_number = $old_lead->num_order;
                $lead->serial_number = $old_lead->serial_order;
                $lead->phone = cleanPhone($old_lead->phone_contact);
                $lead->email = $old_lead->email_contact;
                $lead->location_id = $location_id;

                // Определяем тип лида
                $lead_type_id = null;
                if ($old_lead->status_site == 1) {
                    $lead->lead_type_id = 2;
                    $lead->site_id = 2;
                } else {
                    $lead->lead_type_id = 1;
                }

                $lead->manager_id = $old_lead->manager->new_user_id;
                $lead->stage_id = $old_lead->stage->new_stage_id;
                $lead->old_lead_id = $old_lead->id;
                $lead->display = 1;
                $lead->author_id = $old_lead->manager->new_user_id;
                $lead->created_at = $old_lead->date_order;
                $lead->save();

                $mass['save_lead'] = $lead->id;

                if ($lead) {
                    $lead_id = $lead->id;

                    $mass['lead'] = $lead;
                } else {
                    dd('Ошибка записи лида: ' . $old_lead->name_contact);
                }

                // dd($old_lead);
                // Пишем комменты
                $lead_comments = [];

                if ($old_lead->comment != '') {
                    $lead_comments[] = [
                        'body' => $old_lead->comment,
                        'company_id' => 1,
                        'author_id' => $old_lead->manager->new_user_id,
                        'created_at' => $old_lead->date_order,
                    ];
                }

                if ($old_lead->comment2 != '') {
                    $lead_comments[] = [
                        'body' => $old_lead->comment2,
                        'author_id' => $old_lead->manager->new_user_id,
                        'created_at' => $old_lead->date_order,
                    ];
                }

                if (count($old_lead->comments) > 0) {

                    foreach ($old_lead->comments as $comment) {
                        if ($comment->body_note != '') {
                            $lead_comments[] = [
                                'body' => $comment->body_note,
                                'company_id' => 1,
                                'author_id' => $old_lead->manager->new_user_id,
                                'created_at' => $comment->date_note . ' ' . $comment->time_note . ':00',
                            ];
                        }

                    }
                }

                if ($old_lead->fact_pay != 0) {
                    $fact_pay = num_format($old_lead->fact_pay, 0);
                    $lead_comments[] = [
                        'body' => 'Фактически оплачено: ' . $fact_pay,
                        'company_id' => 1,
                        'author_id' => $old_lead->manager->new_user_id,
                        'created_at' => Carbon::now(),
                    ];
                }

                // dd($lead_comments);

                $lead->notes()->createMany($lead_comments);

                $mass['comments'] = $lead_comments;

                // Пишем задачи
                if (count($old_lead->challenges) > 0) {
                    $lead_challenges = [];

                    foreach ($old_lead->challenges as $challenge) {

                        $status = null;
                        if ($challenge->status_challenge == 2) {
                            $status = 1;
                        }

                        if ($challenge->id_model_challenge == 2) {
                            $challenge_type_id = 2;
                        } elseif ($challenge->id_model_challenge == 4) {
                            $challenge_type_id = 3;
                        } else {
                            $challenge_type_id = 1;
                        }

                        if (isset($challenge->finisher->new_user_id)) {
                            $finisher_id = $challenge->finisher->new_user_id;
                            # code...
                        } else {
                            $finisher_id = null;
                        }


                        if ($challenge->deadline_challenge == '0000-00-00 00:00:00') {
                            $deadline_challenge = null;
                        } else {
                            $deadline_challenge = $challenge->deadline_challenge;
                        };

                        if ($challenge->date_completed == '0000-00-00 00:00:00') {
                            $date_completed = null;
                        } else {
                            $date_completed = $challenge->date_completed;
                        };

                        $lead_challenges[] = [
                            'company_id' => 1,
                            'description' => $challenge->comment_challenge,
                            'appointed_id' => $challenge->appointed->new_user_id,
                            'finisher_id' => $finisher_id,
                            'author_id' => $challenge->author->new_user_id,
                            'deadline_date' => $deadline_challenge,
                            'status' => $status,
                            'completed_date' => $date_completed,
                            'challenges_type_id' => $challenge_type_id,
                            'created_at' => $challenge->date_challenge,
                        ];
                    }

                    // dd($lead_challenges);
                    $lead->challenges()->createMany($lead_challenges);

                    $mass['lead_challenges'] = $lead_challenges;
                }

                // Пишем рекламации
                if (count($old_lead->claims) > 0) {
                    $lead_claims = [];

                    foreach ($old_lead->claims as $claim) {

                        $lead_claims[] = [
                            'company_id' => 1,
                            'body' => $claim->body_claim,
                            // 'lead_id' => $lead_id,
                            'old_claim_id' => $claim->id,
                            'author_id' => $claim->author->new_user_id,
                            'created_at' => $claim->date_claim,

                        ];

                    }
                    // dd($lead_claims);
                    $lead->claims()->createMany($lead_claims);

                    $mass['lead_claims'] = $lead_claims;
                }

                if (isset($old_lead->service->choise_type)) {

                    $choice = new Choice;
                    $choice->lead_id = $lead_id;
                    $choice->choices_id = $old_lead->service->choise_id;
                    $choice->choices_type = $old_lead->service->choise_type;
                    $choice->save();

                    if ($choice == false) {
                        dd('Ошибка записи предпочтения лида.');
                    }

                    $mass['choice'] = $choice;
                }

                $old_lead->parse_status = 1;
                $old_lead->save();

                if ($old_lead) {

                    $mass['old_lead'] = $old_lead;

                    $mass['parse_lead'] = $old_lead->parse_status;
                } else {
                    dd('Ошибка проставления отметки старому лиду: ' . $old_lead->id);

                }


                echo 'Текущий лид: ' . $mass['get_lead'] . ', записан в новую базу: ' . $mass['save_lead'] . ', отметка: ' . $mass['parse_lead'] . "\r\n";
            }
        }
        // echo '500 записей, id: '.$lead->id.', ';
        // });

        return redirect('/admin/users');


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function leads_check_bases(Request $request)
    {

        $a = Lead::whereYear('created_at', 2017)->count();
        $b = Lead::whereYear('created_at', 2018)->count();
        $b1 = Lead::whereYear('created_at', 2016)->count();

        $c = OldLead::whereYear('date_order', 2017)->count();
        $d = OldLead::whereYear('date_order', 2018)->count();
        $d1 = OldLead::whereYear('date_order', 2016)->count();

        $mass = [
            'Новая' => [
                '2016' => $b1,
                '2017' => $a,
                '2018' => $b
            ],
            'Старая' => [
                '2016' => $d1,
                '2017' => $c,
                '2018' => $d
            ],
        ];
        dd($mass);

    }

    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function andrey(Request $request)
    {
        $leads = Lead::where('manager_id', 5)->update(['manager_id' => 7]);
        $challenges = Challenge::where('appointed_id', 5)->update(['appointed_id' => 7]);

        $challenges = Challenge::where('author_id', 9)->update(['author_id' => 7]);
        dd('Готово!');
    }

    public function lead_type(Request $request)
    {
        $leads = Lead::whereNull('lead_type_id')->update(['lead_type_id' => 1]);
        dd('Заебца!');
    }

    public function old_claims(Request $request)
    {

        $claims = Claim::get();
        foreach ($claims as $claim) {
            $claim->serial_number = $claim->old_claim_id;
            $claim->manager_id = 9;
            $claim->status = 1;
            $claim->save();
        }
        dd('Удача, ебана!');
    }

    public function phone_parser(Request $request)
    {

        $companies = Company::get();
        foreach ($companies as $comapny) {
            if (isset($company->phone)) {
                // Пишем или ищем новый и создаем связь
                $phone = Phone::firstOrCreate(
                    ['phone' => $company->phone,
                    ], [
                    'crop' => substr($company->phone, -4),
                ]);
                $company->phones()->attach($phone->id, ['main' => 1]);
            }
            if (isset($company->extra_phone)) {
                // Пишем или ищем новый и создаем связь
                $phone = Phone::firstOrCreate([
                    'phone' => $company->extra_phone
                ], [
                    'crop' => substr($company->extra_phone, -4),
                ]);
                $company->phones()->attach($phone->id);
            }
        }

        $users = User::get();
        foreach ($users as $user) {
            if (isset($user->phone)) {
                // Пишем или ищем новый и создаем связь
                $phone = Phone::firstOrCreate([
                    'phone' => $user->phone
                ], [
                    'crop' => substr($user->phone, -4),
                ]);
                $user->phones()->attach($phone->id, ['main' => 1]);
            }
            if (isset($company->extra_phone)) {
                // Пишем или ищем новый и создаем связь
                $phone = Phone::firstOrCreate([
                    'phone' => $user->extra_phone
                ], [
                    'crop' => substr($user->extra_phone, -4),
                ]);
                $user->phones()->attach($phone->id);
            }
        }

        $departments = Department::get();
        foreach ($departments as $department) {
            if (isset($department->phone)) {
                // Пишем или ищем новый и создаем связь
                $phone = Phone::firstOrCreate([
                    'phone' => $department->phone
                ], [
                    'crop' => substr($department->phone, -4),
                ]);
                $department->phones()->attach($phone->id, ['main' => 1]);
            }
        }

        $leads = Lead::get();
        foreach ($leads as $lead) {
            if (isset($lead->phone)) {
                // Пишем или ищем новый и создаем связь
                $phone = Phone::firstOrCreate([
                    'phone' => $lead->phone
                ], [
                    'crop' => substr($lead->phone, -4),
                ]);
                $lead->phones()->attach($phone->id, ['main' => 1]);
            }
        }

        dd('Норм');


    }

    public function sort_catalog_parser()
    {

        $items = PricesGoods::all();
        $items_filial_1 = $items->where('filial_id', 1);
        $items_filial_2 = $items->where('filial_id', 2);

        foreach ($items_filial_1 as $item) {
            $new_item = $items_filial_2->where('goods_id', $item->goods_id)->first();
            if ($new_item) {
                $new_item->sort = $item->sort;
                $new_item->save();
            }
        }

        dd('Готово!');
    }

}
