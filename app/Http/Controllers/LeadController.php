<?php

namespace App\Http\Controllers;

use App\Client;
use App\Company;
use App\ContractsClient;
use App\Department;
use App\Discount;
use App\Http\Controllers\Traits\Estimatable;
use App\Models\System\Documents\Estimate;
use App\Models\System\Documents\EstimatesGoodsItem;
use App\Http\Controllers\System\Traits\Leadable;
use App\Http\Controllers\System\Traits\Locationable;
use App\Http\Controllers\System\Traits\Phonable;
use App\Http\Controllers\System\Traits\Timestampable;
use App\Http\Controllers\System\Traits\Userable;
use App\Http\Controllers\Traits\Offable;
use App\Http\Controllers\Traits\Photable;
use App\Models\System\Documents\EstimatesServicesItem;
use App\Models\System\Flows\EventsFlow;
use App\Models\System\Flows\ServicesFlow;
use App\Outlet;
use App\Representative;
use App\Stock;
use App\Subscriber;
use App\Template;
use App\User;
use App\Lead;
use App\LeadType;
use App\Note;
use App\Challenge;
use App\GoodsCategory;
use App\ServicesCategory;
use App\RawsCategory;
use Illuminate\Http\Request;
use App\Http\Requests\System\LeadRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;
use App\Exports\LeadsExport;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{

    protected $entityAlias;
    protected $entityDependence;

    /**
     * LeadController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'leads';
        $this->entityDependence = true;
    }

    use Leadable,
        Userable,
        Offable,
        Photable,
        Phonable,
        Locationable,
        Timestampable,
        Estimatable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
//        dd($request);

        // Включение контроля активного фильтра
//        $filter_url = autoFilter($request, $this->entityAlias);
//        if (($filter_url != null) && ($request->filter != 'active')) {
//            return Redirect($filter_url);
//        };

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Lead::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------

        // Проверяем специфические права
        $lead_all_managers = extra_right('lead-all-managers');

        $leads = Lead::select([
            'id',
            'company_id',
            'filial_id',
            'draft',
            'badget',
            'created_at',
            'case_number',
            'name',
            'lead_type_id',
            'lead_method_id',
            'stage_id',
            'location_id',
            'manager_id',
            'shipment_at'
        ])->with([
            'choice',
            'lead_type',
            'lead_method',
            'stage',
            'location.city',
            'main_phones',
            'estimate' => function ($q) {
                $q->with([
                    'goods_items' => function ($q) {
                        $q->with([
                            // 'goods.article',
                            'reserve'
                        ]);
                    },
                    'services_items' => function ($q) {
                        $q->with([
                            'service' => function ($q) {
                                $q->with([
                                    'process',
                                    'actualFlows'
                                ]);
                            },
                            'flow',
                        ]);
                    },
                    'payments' => function ($q) {
                        $q->with([
                            'method',
                            'sign',
                            'currency'
                        ]);
                    },
                    'agent.company'
                ]);
            },
        ])

            // Если есть право смотреть лидов ВСЕХ менеджеров (true), то получаем еще данные менеджеров
            // ->when($lead_all_managers, function ($q) {
            //     return $q->with(['manager' => function ($query) {
            //         $query->select('id', 'first_name', 'second_name');
            //     }]);
            // })
            // ->withCount(['challenges' => function ($query) {
            //     $query->whereNull('status');
            // }])
            ->manager(auth()->user())
            // ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->filials($answer)
            ->where('draft', false)
            // ->systemItem($answer)
            ->filter()
            // ->booklistFilter($request)
            // ->latest('created_at')
            ->orderByDesc('created_at')
            ->paginate(30);

            // dd($leads);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
//            'city',                 // Город
//            'stage',                // Этап
//            'lead_method',          // Способ обращения
//            'lead_type',            // Тип обращения
//            'manager',              // Менеджер
//            'date_interval',        // Дата обращения
            'booklist',               // Списки пользователя
//            'challenges_active_count' // Активные и не активные задачи
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------


        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('leads.index', compact('leads', 'pageInfo', 'filter'));
    }

    /**
     * Создаем чернового лида со сметой
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        // Подключение политики
        $this->authorize(__FUNCTION__, Lead::class);


        $lead = Lead::create();

        $location = $this->getLocation(1, $lead->filial->location->city_id);
        $lead->update([
            'location_id' => $location->id
        ]);

        // Создаем смету для лида
        $estimate = Estimate::make([
            'filial_id' => $lead->filial_id,
            'is_main' => true,
            'number' => $lead->id,
            'date' => today()->format('d.m.Y'),
            'currency_id' => 1,
        ]);

        $result = $lead->estimate()->save($estimate);

        $lead->load('estimate');

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('discounts', true, 'index');

        $discounts = Discount::where('archive', false)
            ->whereHas('entity', function ($q) {
                $q->where('alias', 'estimates');
            })
            ->where('begined_at', '<=', now())
            ->where(function ($q) {
                $q->where('ended_at', '>=', now())
                    ->orWhereNull('ended_at');
            })
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->get();

        if ($discounts) {
            $estimate = $lead->estimate;
            $estimate->discounts()->attach($discounts->pluck('id'));
        }

        return redirect()->route('leads.edit', $lead->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:

        $lead = Lead::with([
            'location.city',
            'user' => function ($q) {
                $q->with([
                    'location',
                    'main_phones',
                    'client'
                ]);
            },
            'organization' => function ($q) {
                $q->with([
                    'location',
                    'main_phones',
                    'client'
                ]);
            },
            'client' => function ($q) {
                $q->with([
                    'clientable' => function ($q) {
                        $q->with([
                            'location',
                            'main_phones'
                        ]);
                    },
                    'contract'
                ]);
            },
            'main_phones',
            'extra_phones',
            'medium',
            'campaign',
            'source',
            'site',
            'estimate' => function ($q) {
                $q->with([
                    'goods_items' => function ($q) {
                        $q->with([
                            'goods.article',
                            'reserve',
                            'stock:id,name',
                            'price_goods',
                            'currency',
                            'productions_item'
                        ]);
                    },
                    'catalogs_goods',
                    'services_items' => function ($q) {
                        $q->with([
                            'service' => function ($q) {
                                $q->with([
                                    'process',
                                    'actualFlows'
                                ]);
                            },
                            'price_service',
                            'currency',
                            'flow',
                        ]);
                    },
                    'catalogs_services',
                    'payments' => function ($q) {
                        $q->with([
                            'method',
                            'sign',
                            'currency'
                        ]);
                    },
                    'lead.client.contract',
                    'discounts',
                    'agent.company',
                    'labels'
                ]);
            },
            'claims',
            'lead_method',
            'choice' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
            'notes' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'challenges' => function ($query) {
                $query->with('challenge_type')
                    ->whereNull('status')
                    ->orderBy('deadline_date', 'asc');
            },
            'outlet' => function ($q) {
                $q->with([
                    'catalogs_goods',
                    'catalogs_services',
                    'stock',
                    'settings',
                    'payments_methods'
                ]);
            },
            'filial'
        ])
            ->companiesLimit($answer)
            ->filials($answer)
            // ->where('manager_id', '!=', 1)
            // ->authors($answer)
            ->systemItem($answer)
            ->moderatorLimit($answer)
            ->find($id);
//        dd($lead);

        if (empty($lead)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $lead);

//        if ($lead->draft == false && $lead->estimate->registered_at) {
//            // Если есть клиент у лида, сравниваем скидку
//            if ($lead->client) {
//                $this->updateClientDiscount($lead);
//            } else {
//                // Если нет клиента, то ищем его
//                // Проверяем организацию
//                if ($lead->organization) {
//                    $organization = $lead->organization;
//                    $organization->load('client');
//                    if ($organization->client) {
//                        $lead->update([
//                            'client_id' => $organization->client->id
//                        ]);
//                        $lead->load('client');
//                        $this->updateClientDiscount($lead);
//                    }
//                } else if ($lead->company_name) {
//                    // Если есть имя компании, ищем первую компанию, которую представляет пользователь
//                    $user = $lead->user;
//                    $user->load('organizations');
//
//                    if ($user->organizations->isNotEmpty()) {
//                        $organization = $user->organizations->first();
//                        $organization->load('client');
//                        if ($organization->client) {
//                            $lead->update([
//                                'client_id' => $organization->client->id
//                            ]);
//                            $lead->load('client');
//                            $this->updateClientDiscount($lead);
//                        }
//                    }
//                } else if ($lead->user) {
//                    // Смотрим, есть ли клиент у юзера
//                    $user = $lead->user;
//                    $user->load('client');
//                    if ($user->client) {
//                        $lead->update([
//                            'client_id' => $user->client->id
//                        ]);
//                        $lead->load('client');
//                        $this->updateClientDiscount($lead);
//                    }
//
//                }
//            }
//        }
//
        $goods_categories_list = GoodsCategory::whereNull('parent_id')->get()->mapWithKeys(function ($item) {
            return ['goods-' . $item->id => $item->name];
        })->toArray();


        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_sc = operator_right('services_category', false, getmethod('index'));

        $services_categories_list = ServicesCategory::moderatorLimit($answer_sc)
            ->companiesLimit($answer_sc)
            ->authors($answer_sc)
            ->where('is_direction', true)
            ->get()
            ->mapWithKeys(function ($item) {
                return ['service-' . $item->id => $item->name];
            })->toArray();

        $raws_categories_list = RawsCategory::whereNull('parent_id')->get()->mapWithKeys(function ($item) {
            return ['raw-' . $item->id => $item->name];
        })->toArray();

        $choices = [
            'Товары' => $goods_categories_list,
            'Услуги' => $services_categories_list,
            'Сырье' => $raws_categories_list,
        ];


        // TODO - 04.11.20 - Заглушка торговой точкой
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
//        $answerOutlets = operator_right('outlets', true, getmethod('index'));

        $outlet = Outlet::with([
            'catalogs_goods',
            'catalogs_services',
            'stock',
            'settings',
            'payments_methods'
        ])
//            ->moderatorLimit($answerOutlets)
//            ->companiesLimit($answerOutlets)
//            ->authors($answerOutlets)

//            ->where('filial_id', auth()->user()->stafferFilialId)
//            ->first();
            ->find($lead->outlet_id);

        // Настройки компании
        $settings = auth()->user()->company->settings;

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('leads.edit', compact('lead', 'pageInfo', 'choices', 'settings', 'outlet'));
    }

    public function updateClientDiscount($lead)
    {
        $clientDiscount = $lead->client->discount;

        $estimate = $lead->estimate;
        foreach ($estimate->goods_items as $goodsItem) {
            if ($goodsItem->client_discount_percent != $clientDiscount) {
                $goodsItem->update([
                    'client_discount_percent' => $clientDiscount
                ]);
            }
        }
    }

    public function update(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $lead = Lead::with([
            'location.city',
            'user.client',
            'organization.client',
            'client',
            'main_phones',
            'estimate',
        ])
            ->companiesLimit($answer)
            ->filials($answer)
            ->systemItem($answer)
            ->moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $lead);

        $newLead = $request->lead;

        $dataLead = [
            'name' => $newLead['name'],
            'company_name' => $newLead['company_name'],
            'email' => $newLead['email'],
            'user_id' => $newLead['user_id'],
            'organization_id' => $newLead['organization_id'],
            'client_id' => $newLead['client_id'],

            'stage_id' => $newLead['stage_id'],
            'shipment_at' => $newLead['shipment_at'],
        ];

        $location = $this->getLocation(1, $newLead['location']['city_id'], $newLead['location']['address']);
        $dataLead['location_id'] = $location->id;

        // Проверка пользователя
        if (empty($dataLead['user_id'])) {
            $user = User::whereHas('main_phones', function ($q) use ($newLead) {
                $q->where('phone', cleanPhone($newLead['main_phone']));
            })
                ->where('company_id', $lead->company_id)
                ->where('site_id', '!=', 1)
                ->first();

            if ($user) {
                $dataLead['user_id'] = $user->id;
            } else {
                $dataUser = [
                    'name' => $newLead['name'],
                    'email' => $newLead['email'],
                    'location_id' => $dataLead['location_id']
                ];

                $user = User::create($dataUser);

                $this->savePhones($user, $newLead['main_phone']);
                logs('users')->info("Создан пользователь. Id: [{$user->id}]");

                $dataLead['user_id'] = $user->id;
                $dataLead['private_status'] = 0;
            }

            $dataLead['private_status'] = 0;
        }

        // Проверка организации
        if (empty($dataLead['organization_id']) && isset($dataLead['company_name'])) {

            $company = Company::firstOrCreate([
                'name' => $newLead['company_name']
            ], [
                'email' => $newLead['email'],
                'location_id' => $dataLead['location_id']
            ]);

            $this->savePhones($company, $newLead['main_phone']);
            logs('companies')->info("Создана компания. Id: [{$company->id}]");

            $dataLead['organization_id'] = $company->id;
            $dataLead['private_status'] = 1;
        }

        $res = $lead->update($dataLead);
        $this->savePhones($lead, $newLead['main_phone']);

        // Обновляем пункты сметы
        $goodsItems = $lead->estimate->goods_items;
        $newGoodsItems = $request->goods_items;

        $newGoodsItemsIds = [];
        $sort = 1;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('stocks', true, getmethod('index'));

        // TODO - 16.10.20 - Пока что берем первый склад
        $stock = Stock::companiesLimit($answer)
            ->first();
        $stockId = optional($stock)->id;
        return $stockId;

        foreach ($newGoodsItems as $newGoodsItem) {

            $data = [
                'estimate_id' => $newGoodsItem['estimate_id'],
                'price_id' => $newGoodsItem['price_id'],
                'stock_id' => $stockId,

                'goods_id' => $newGoodsItem['goods_id'],
                'currency_id' => $newGoodsItem['currency_id'],
                'sale_mode' => $newGoodsItem['sale_mode'],

                'comment' => $newGoodsItem['comment'],

                'cost_unit' => $newGoodsItem['cost_unit'],
                'price' => $newGoodsItem['price'],
                'points' => $newGoodsItem['points'],
                'count' => $newGoodsItem['count'],

                'cost' => $newGoodsItem['cost'],
                'amount' => $newGoodsItem['amount'],

                'price_discount_id' => $newGoodsItem['price_discount_id'],
                'price_discount_unit' => $newGoodsItem['price_discount_unit'],
                'price_discount' => $newGoodsItem['price_discount'],
                'total_price_discount' => $newGoodsItem['total_price_discount'],

                'catalogs_item_discount_id' => $newGoodsItem['catalogs_item_discount_id'],
                'catalogs_item_discount_unit' => $newGoodsItem['catalogs_item_discount_unit'],
                'catalogs_item_discount' => $newGoodsItem['catalogs_item_discount'],
                'total_catalogs_item_discount' => $newGoodsItem['total_catalogs_item_discount'],

                'estimate_discount_id' => $newGoodsItem['estimate_discount_id'],
                'estimate_discount_unit' => $newGoodsItem['estimate_discount_unit'],
                'estimate_discount' => $newGoodsItem['estimate_discount'],
                'total_estimate_discount' => $newGoodsItem['total_estimate_discount'],

                'client_discount_percent' => $newGoodsItem['client_discount_percent'],
                'client_discount_unit_currency' => $newGoodsItem['client_discount_unit_currency'],
                'client_discount_currency' => $newGoodsItem['client_discount_currency'],
                'total_client_discount' => $newGoodsItem['total_client_discount'],

                'total' => $newGoodsItem['total'],
                'total_points' => $newGoodsItem['total_points'],
                'total_bonuses' => $newGoodsItem['total_bonuses'],

                'computed_discount_percent' => $newGoodsItem['computed_discount_percent'],
                'computed_discount_currency' => $newGoodsItem['computed_discount_currency'],
                'total_computed_discount' => $newGoodsItem['total_computed_discount'],

                'is_manual' => $newGoodsItem['is_manual'],
                'manual_discount_percent' => $newGoodsItem['manual_discount_percent'],
                'manual_discount_currency' => $newGoodsItem['manual_discount_currency'],
                'total_manual_discount' => $newGoodsItem['total_manual_discount'],

                'discount_currency' => $newGoodsItem['discount_currency'],
                'discount_percent' => $newGoodsItem['discount_percent'],

                'margin_currency_unit' => $newGoodsItem['margin_currency_unit'],
                'margin_percent_unit' => $newGoodsItem['margin_percent_unit'],
                'margin_currency' => $newGoodsItem['margin_currency'],
                'margin_percent' => $newGoodsItem['margin_percent'],

                'sort' => $sort,
            ];

            if (isset($newGoodsItem['company_id'])) {
                $goodsItem = $goodsItems->firstWhere('id', $newGoodsItem['id']);
                $goodsItem->update($data);
            } else {
                $goodsItem = EstimatesGoodsItem::create($data);
            }

            $newGoodsItemsIds[] = $goodsItem['id'];
            $sort++;
        }

        $oldGoodsItemsIds = $lead->estimate->goods_items
            ->pluck('id')
            ->toArray();

        $deleteIds = array_diff($oldGoodsItemsIds, $newGoodsItemsIds);
        $res = EstimatesGoodsItem::destroy($deleteIds);

        // Аггрегация сметы
        $this->aggregateEstimate($lead->estimate);
        $lead->load([
            'estimate'
        ]);
        $estimate = $lead->estimate;

        // Регистрация сметы
        if ($request->has('is_registered')) {

            if (empty($lead->client_id)) {
                if (isset($lead->organization_id)) {
                    $client = Client::firstOrCreate([
                        'clientable_id' => $lead->organization_id,
                        'clientable_type' => 'App\Company',
                    ]);

                } else {
                    $client = Client::firstOrCreate([
                        'clientable_id' => $lead->user_id,
                        'clientable_type' => 'App\User',
                    ]);

                }

                $clientId = $client->id;
                $lead->update([
                    'client_id' => $clientId
                ]);
            } else {
                $clientId = $lead->client_id;
            }

            $lead->estimate->update([
                'client_id' => $clientId,
            ]);

            $contracts_client = ContractsClient::create([
                'client_id' => $clientId,
                'amount' => $estimate->total,
            ]);

            $estimate->update([
                'client_id' => $clientId,
                'registered_at' => now(),
            ]);
        } else {
            $lead->estimate->update([
                'client_id' => $lead->client_id
            ]);
        }

        // Проверка на создание представителя
        if ($lead->organization_id) {
            $representative = Representative::where([
                'user_id' => $lead->user_id,
                'organization_id' => $lead->organization_id,
                'company_id' => $lead->company_id,
            ])
                ->first();

            if (empty($representative)) {
                Representative::create([
                    'user_id' => $lead->user_id,
                    'organization_id' => $lead->organization_id,
                ]);
            }
        }

        $lead->load([
            'main_phones',
            'extra_phones',
            'location.city',
            'user' => function ($q) {
                $q->with([
                    'main_phones',
                    'location.city',
                    'client',
                    'organizations' => function ($q) {
                        $q->with([
                            'client',
                        ])
                            ->latest();
                    },
                ]);
            },
            'organization' => function ($q) {
                $q->with([
                    'main_phones',
                    'location',
                    'client',
                    'representatives' => function ($q) {
                        $q->with([
                            'client',
                        ])
                            ->latest();
                    },
                ]);
            },
            'client.contract',
            'estimate' => function ($q) {
                $q->with([
                    'goods_items' => function ($q) {
                        $q->with([
                            'goods.article',
                            'reserve',
                            'stock:id,name',
                            'price_goods',
                            'currency'
                        ]);
                    },
                    'services_items' => function ($q) {
                        $q->with([
                            'product.process',
                        ]);
                    },
                    'lead.client.contract',
                ]);
            },
            'medium',
            'campaign',
            'source',
            'site',
            'claims',
            'lead_method',
            'choice' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
            'notes' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'challenges' => function ($query) {
                $query->with('challenge_type')
                    ->whereNull('status')
                    ->orderBy('deadline_date', 'asc');
            }
        ]);

        $estimate = $lead->estimate;
        $goodsItems = $estimate->goods_items;

        return response()->json([
            'lead' => $lead,
            'estimate' => $estimate,
            'goods_items' => $goodsItems,
        ]);
    }

    /**
     * Временный метод обновления лида
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function axiosUpdate(Request $request, $id)
    {
        // TODO - 21.09.20 - Временный метод для axios обновления лида, уйдет с рефактором контроллера лидов
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $lead = Lead::with([
            'location.city',
            'user' => function ($q) {
                $q->with([
                    'location',
                    'main_phones',
                    'client'
                ]);
            },
            'organization' => function ($q) {
                $q->with([
                    'location',
                    'main_phones',
                    'client'
                ]);
            },
            'client',
            'main_phones',
            'estimate' => function ($q) {
                $q->with([
                    'goods_items' => function ($q) {
                        $q->with([
                            'product.article',
                            'reserve',
                            'stock:id,name',
                            'price_goods',
                            'currency'
                        ]);
                    },
                    'services_items' => function ($q) {
                        $q->with([
                            'product.process',
                            'price_service',
                            'currency'
                        ]);
                    },
                    'payments' => function ($q) {
                        $q->with([
                            'method',
                            'sign',
                            'currency'
                        ]);
                    },
                    'discounts'
                ]);
            },
        ])
            ->companiesLimit($answer)
            ->filials($answer)
            ->systemItem($answer)
            ->moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod('update'), $lead);

        if ($lead->estimate->registered_at) {
            $newLead = $request->lead;
            $dataLead = [
                'stage_id' => $newLead['stage_id'],
                'shipment_at' => $newLead['shipment_at'],
            ];

            $res = $lead->update($dataLead);
        } else {
            $newLead = $request->lead;

            $dataLead = [
                'name' => $newLead['name'],
                'company_name' => $newLead['company_name'] ?? null,
                'email' => $newLead['email'] ?? null,
                'user_id' => $newLead['user_id'] ?? null,
                'organization_id' => $newLead['organization_id'] ?? null,
                'client_id' => $newLead['client_id'] ?? null,

                'stage_id' => $newLead['stage_id'] ?? null,
                'shipment_at' => $newLead['shipment_at'] ?? null,

                'filial_id' => $newLead['filial_id'] ?? null,
                'outlet_id' => $newLead['outlet_id'] ?? null,

                'description' => $newLead['description'] ?? null,
            ];

            $location = $this->getLocation(1, $newLead['location']['city_id'], $newLead['location']['address']);
            $dataLead['location_id'] = $location->id;

            // Проверка пользователя
            if (empty($dataLead['user_id'])) {
                $user = User::whereHas('main_phones', function ($q) use ($newLead) {
                    $q->where('phone', cleanPhone($newLead['main_phone']));
                })
                    ->where('company_id', $lead->company_id)
                    ->where('site_id', '!=', 1)
                    ->first();

                if ($user) {
                    // Обновляем имя пользователя если его нет
                    if (($user->first_name == '' || empty($user->first_name)) && isset($newLead['name'])) {
                        $user->name = $newLead['name'];
                        $user->saveQuietly();
                    }
                    $dataLead['user_id'] = $user->id;
                } else {
                    $dataUser = [
                        'name' => $newLead['name'],
                        'email' => $newLead['email'],
                        'location_id' => $dataLead['location_id']
                    ];

                    $user = User::create($dataUser);

                    $this->savePhones($user, $newLead['main_phone']);
                    logs('users')->info("Создан пользователь. Id: [{$user->id}]");

                    $dataLead['user_id'] = $user->id;
                }
            }

            // Проверка организации
            if (empty($dataLead['organization_id']) && isset($dataLead['company_name'])) {
                $dataCompany = [
                    'name' => $newLead['company_name'],
                    'email' => $newLead['email'],
                    'location_id' => $dataLead['location_id']
                ];

                $company = Company::create($dataCompany);

                $this->savePhones($company, $newLead['main_phone']);
                logs('companies')->info("Создана компания. Id: [{$company->id}]");

                $dataLead['organization_id'] = $company->id;
            }

            $datLead['private_status'] = isset($dataLead['company_name']) ? 1 : 0;

//        return $dataLead;

            $res = $lead->update($dataLead);
            $this->savePhones($lead, $newLead['main_phone']);

            // Обновляем пункты сметы

            // Товары
            $goodsItems = $lead->estimate->goods_items;
            $newGoodsItems = $request->goods_items;
            $oldGoodsItemsIds = $lead->estimate->goods_items->pluck('id')->toArray();

            $newGoodsItemsIds = [];
            $sort = 1;

            foreach ($newGoodsItems as $newGoodsItem) {

                $data = [
                    'estimate_id' => $newGoodsItem['estimate_id'],
                    'price_id' => $newGoodsItem['price_id'],
                    'stock_id' => $newGoodsItem['stock_id'],

                    'goods_id' => $newGoodsItem['goods_id'],
                    'currency_id' => $newGoodsItem['currency_id'],
                    'sale_mode' => $newGoodsItem['sale_mode'],

                    'comment' => $newGoodsItem['comment'],

                    'cost_unit' => $newGoodsItem['cost_unit'],
                    'price' => $newGoodsItem['price'],
                    'points' => $newGoodsItem['points'],
                    'count' => $newGoodsItem['count'],

                    'cost' => $newGoodsItem['cost'],
                    'amount' => $newGoodsItem['amount'],

                    'price_discount_id' => $newGoodsItem['price_discount_id'],
                    'price_discount_unit' => $newGoodsItem['price_discount_unit'],
                    'price_discount' => $newGoodsItem['price_discount'],
                    'total_price_discount' => $newGoodsItem['total_price_discount'],

                    'catalogs_item_discount_id' => $newGoodsItem['catalogs_item_discount_id'],
                    'catalogs_item_discount_unit' => $newGoodsItem['catalogs_item_discount_unit'],
                    'catalogs_item_discount' => $newGoodsItem['catalogs_item_discount'],
                    'total_catalogs_item_discount' => $newGoodsItem['total_catalogs_item_discount'],

                    'estimate_discount_id' => $newGoodsItem['estimate_discount_id'],
                    'estimate_discount_unit' => $newGoodsItem['estimate_discount_unit'],
                    'estimate_discount' => $newGoodsItem['estimate_discount'],
                    'total_estimate_discount' => $newGoodsItem['total_estimate_discount'],

                    'client_discount_percent' => $newGoodsItem['client_discount_percent'],
                    'client_discount_unit_currency' => $newGoodsItem['client_discount_unit_currency'],
                    'client_discount_currency' => $newGoodsItem['client_discount_currency'],
                    'total_client_discount' => $newGoodsItem['total_client_discount'],

                    'total' => $newGoodsItem['total'],
                    'total_points' => $newGoodsItem['total_points'],
                    'total_bonuses' => $newGoodsItem['total_bonuses'],

                    'computed_discount_percent' => $newGoodsItem['computed_discount_percent'],
                    'computed_discount_currency' => $newGoodsItem['computed_discount_currency'],
                    'total_computed_discount' => $newGoodsItem['total_computed_discount'],

                    'is_manual' => $newGoodsItem['is_manual'],
                    'manual_discount_percent' => $newGoodsItem['manual_discount_percent'],
                    'manual_discount_currency' => $newGoodsItem['manual_discount_currency'],
                    'total_manual_discount' => $newGoodsItem['total_manual_discount'],

                    'discount_currency' => $newGoodsItem['discount_currency'],
                    'discount_percent' => $newGoodsItem['discount_percent'],

                    'margin_currency_unit' => $newGoodsItem['margin_currency_unit'],
                    'margin_percent_unit' => $newGoodsItem['margin_percent_unit'],
                    'margin_currency' => $newGoodsItem['margin_currency'],
                    'margin_percent' => $newGoodsItem['margin_percent'],

                    'sort' => $sort,
                ];

                if (isset($newGoodsItem['company_id'])) {
                    $goodsItem = $goodsItems->firstWhere('id', $newGoodsItem['id']);
                    $goodsItem->update($data);
                } else {
                    $goodsItem = EstimatesGoodsItem::create($data);
                }

                $newGoodsItemsIds[] = $goodsItem['id'];
                $sort++;
            }

            $deleteIds = array_diff($oldGoodsItemsIds, $newGoodsItemsIds);
            $res = EstimatesGoodsItem::destroy($deleteIds);

            // Услуги
            $servicesItems = $lead->estimate->services_items;
            $newServicesItems = $request->services_items;
            $oldServicesItemsIds = $lead->estimate->services_items->pluck('id')->toArray();

            $newServicesItemsIds = [];
            $sort = 1;

            foreach ($newServicesItems as $newServicesItem) {

                $data = [
                    'estimate_id' => $newServicesItem['estimate_id'],
                    'price_id' => $newServicesItem['price_id'],

                    'service_id' => $newServicesItem['service_id'],
                    'currency_id' => $newServicesItem['currency_id'],
                    'sale_mode' => $newServicesItem['sale_mode'],

                    'comment' => $newServicesItem['comment'],

                    'cost_unit' => $newServicesItem['cost_unit'],
                    'price' => $newServicesItem['price'],
                    'points' => $newServicesItem['points'],
                    'count' => $newServicesItem['count'],

                    'cost' => $newServicesItem['cost'],
                    'amount' => $newServicesItem['amount'],

                    'price_discount_id' => $newServicesItem['price_discount_id'],
                    'price_discount_unit' => $newServicesItem['price_discount_unit'],
                    'price_discount' => $newServicesItem['price_discount'],
                    'total_price_discount' => $newServicesItem['total_price_discount'],

                    'catalogs_item_discount_id' => $newServicesItem['catalogs_item_discount_id'],
                    'catalogs_item_discount_unit' => $newServicesItem['catalogs_item_discount_unit'],
                    'catalogs_item_discount' => $newServicesItem['catalogs_item_discount'],
                    'total_catalogs_item_discount' => $newServicesItem['total_catalogs_item_discount'],

                    'estimate_discount_id' => $newServicesItem['estimate_discount_id'],
                    'estimate_discount_unit' => $newServicesItem['estimate_discount_unit'],
                    'estimate_discount' => $newServicesItem['estimate_discount'],
                    'total_estimate_discount' => $newServicesItem['total_estimate_discount'],

                    'client_discount_percent' => $newServicesItem['client_discount_percent'],
                    'client_discount_unit_currency' => $newServicesItem['client_discount_unit_currency'],
                    'client_discount_currency' => $newServicesItem['client_discount_currency'],
                    'total_client_discount' => $newServicesItem['total_client_discount'],

                    'total' => $newServicesItem['total'],
                    'total_points' => $newServicesItem['total_points'],
                    'total_bonuses' => $newServicesItem['total_bonuses'],

                    'computed_discount_percent' => $newServicesItem['computed_discount_percent'],
                    'computed_discount_currency' => $newServicesItem['computed_discount_currency'],
                    'total_computed_discount' => $newServicesItem['total_computed_discount'],

                    'is_manual' => $newServicesItem['is_manual'],
                    'manual_discount_percent' => $newServicesItem['manual_discount_percent'],
                    'manual_discount_currency' => $newServicesItem['manual_discount_currency'],
                    'total_manual_discount' => $newServicesItem['total_manual_discount'],

                    'discount_currency' => $newServicesItem['discount_currency'],
                    'discount_percent' => $newServicesItem['discount_percent'],

                    'margin_currency_unit' => $newServicesItem['margin_currency_unit'],
                    'margin_percent_unit' => $newServicesItem['margin_percent_unit'],
                    'margin_currency' => $newServicesItem['margin_currency'],
                    'margin_percent' => $newServicesItem['margin_percent'],

                    'flow_id' => $newServicesItem['flow_id'],

                    'sort' => $sort,
                ];

                if (isset($newServicesItem['company_id'])) {
                    $servicesItem = $servicesItems->firstWhere('id', $newServicesItem['id']);
                    $servicesItem->update($data);
                } else {
                    $servicesItem = EstimatesServicesItem::create($data);
                }

                $newServicesItemsIds[] = $servicesItem['id'];
                $sort++;
            }

            $deleteIds = array_diff($oldServicesItemsIds, $newServicesItemsIds);
            $res = EstimatesServicesItem::destroy($deleteIds);

            // Аггрегация сметы
            $this->aggregateEstimate($lead->estimate);

            $lead->estimate->labels()->sync($request->labels);


            // Проверяем скидки
            if ($request->has('estimate')) {
                $discountsIds = [];
                foreach ($request->estimate['discounts'] as $discount) {
                    $discountsIds[] = $discount['id'];
                }
                $lead->estimate->discounts()->sync($discountsIds);
            }

            $lead->load([
                'estimate'
            ]);
            $estimate = $lead->estimate;

            // Регистрация сметы
            if ($request->has('is_registered')) {

                if (empty($lead->client_id)) {
                    if (isset($lead->organization_id)) {
                        $client = Client::firstOrCreate([
                            'clientable_id' => $lead->organization_id,
                            'clientable_type' => 'App\Company',
                        ]);

                    } else {
                        $client = Client::firstOrCreate([
                            'clientable_id' => $lead->user_id,
                            'clientable_type' => 'App\User',
                        ]);

                    }

                    $clientId = $client->id;
                    $lead->update([
                        'client_id' => $clientId
                    ]);
                } else {
                    $clientId = $lead->client_id;
                }

                $contracts_client = ContractsClient::create([
                    'client_id' => $clientId,
                    'amount' => $estimate->total,
                ]);

                $estimate->load('services_items.service.process');

                if ($estimate->services_items->isNotEmpty()) {
                    $client = $lead->client;

                    foreach($estimate->services_items as $servicesItem) {

                        // Автоинициация
                        if ($servicesItem->service->process->is_auto_initiated) {
                            $data = [
                                'process_id' => $servicesItem->service_id,
                                'filial_id' => $estimate->filial_id,
                                'start_at' => now(),
                                'finish_at' => now()->addSeconds($servicesItem->service->process->length),
                                'capacity_min' => 1,
                                'capacity_max' => 1,
                                'initiator_id' => $servicesItem->id,
                            ];

                            $flow = ServicesFlow::create($data);

                            $servicesItem->update([
                               'flow_id' =>  $flow->id
                            ]);

                            $client->services_flows()->attach($flow->flow_id);
                        } else {
                            // Поток выбран в ручную
                            $client->services_flows()->attach($servicesItem->flow_id);
                        }
                    }
                }

                $estimate->update([
                    'client_id' => $clientId,
                    'registered_at' => now(),
                ]);
            } else {
                $lead->estimate->update([
                    'client_id' => $lead->client_id
                ]);
            }

            // Проверка на создание представителя
            if ($lead->organization_id) {
                $representative = Representative::where([
                    'user_id' => $lead->user_id,
                    'organization_id' => $lead->organization_id,
                    'company_id' => $lead->company_id,
                ])
                    ->first();

                if (empty($representative)) {
                    Representative::create([
                        'user_id' => $lead->user_id,
                        'organization_id' => $lead->organization_id,
                    ]);
                }
            }
        }

        $lead->load([
            'location.city',
            'user.client',
            'organization.client',
            'client' => function ($q) {
                $q->with([
                    'contract',
                    'clientable',
                ]);
            },
            'main_phones',
            'extra_phones',
            'medium',
            'campaign',
            'source',
            'site',
            'claims',
            'estimate' => function ($q) {
                $q->with([
                    'goods_items' => function ($q) {
                        $q->with([
                            'goods.article',
                            'reserve',
                            'stock:id,name',
                            'price_goods',
                            'currency',
                            'productions_item'
                        ]);
                    },
                    'catalogs_goods',
                    'services_items' => function ($q) {
                        $q->with([
                            'service' => function ($q) {
                                $q->with([
                                    'process',
                                    'actualFlows'
                                ]);
                            },
                            'price_service',
                            'currency',
                            'flow',
                        ]);
                    },
                    'catalogs_services',
                    'payments' => function ($q) {
                        $q->with([
                            'method',
                            'sign',
                            'currency'
                        ]);
                    },
                    'discounts',
                    'labels'
                ]);
            },
            'lead_method',
            'choice' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
            'notes' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'challenges' => function ($query) {
                $query->with('challenge_type')
                    ->whereNull('status')
                    ->orderBy('deadline_date', 'asc');
            },
            'outlet' => function ($q) {
                $q->with([
                    'catalogs_goods',
                    'catalogs_services',
                    'stock',
                    'settings',
                    'payments_methods'
                ]);
            }
        ]);

        $estimate = $lead->estimate;
        $goodsItems = $estimate->goods_items;
        $servicesItems = $estimate->services_items;
        $outlet = $lead->outlet;

        return response()->json([
            'lead' => $lead,
            'estimate' => $estimate,
            'goods_items' => $goodsItems,
            'services_items' => $servicesItems,
            'outlet' => $outlet,
        ]);
    }

    public function leads_calls(Request $request)
    {

        Carbon::setLocale('en');
        // dd(Carbon::getLocale());

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entityAlias);
        if (($filter_url != null) && ($request->filter != 'active')) {
            return Redirect($filter_url);
        };

        $user = $request->user();

        // Подключение политики
        $this->authorize(getmethod('index'), Lead::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('index'));

        // --------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------

        // Запрос с выбором лидов по дате задачи == сегодняшней дате или меньше, не получается отсортировать по дате задачи, т.к. задач может быть много на одном лиде
        $leads = Lead::with(
            'location.city',
            'choice',
            'manager',
            'stage',
            'challenges.challenge_type'
        )
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
            // ->authors($answer)
            ->manager($user)
            ->whereNull('draft')
            ->whereHas('challenges', function ($query) {
                $query->whereHas('challenge_type', function ($query) {
                    $query->where('id', 2);
                })->whereNull('status')->whereDate('deadline_date', '<=', Carbon::now()->format('Y-m-d'));
            })
            ->systemItem($answer) // Фильтр по системным записям
            ->filter($request, 'city_id', 'location')
            ->filter($request, 'stage_id')
            ->filter($request, 'manager_id')
            ->dateIntervalFilter($request, 'created_at')
            ->booklistFilter($request)
            // ->orderBy('challenges.deadline_date', 'desc')
            ->orderBy('manager_id', 'asc')
            ->orderBy('created_at', 'desc')
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc')
            ->paginate(30);

        // ---------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ----------------------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------

        $filter_query = Lead::with('location.city', 'manager', 'stage')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
            ->manager($user)
            // ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->get();

        $filter['status'] = null;
        $filter['entityAlias'] = $this->entityAlias;
        $filter['inputs'] = $request->input();

        // Перечень подключаемых фильтров:
        $filter = addFilter($filter, $filter_query, $request, 'Выберите город:', 'city', 'city_id', 'location', 'external-id-one');
        $filter = addFilter($filter, $filter_query, $request, 'Выберите этап:', 'stage', 'stage_id', null, 'internal-id-one');
        $filter = addFilter($filter, $filter_query, $request, 'Менеджер:', 'manager', 'manager_id', null, 'internal-id-one');


        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entityAlias);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('leads.index', compact('leads', 'pageInfo', 'filter', 'user'));
    }

    public function search(Request $request, $search)
    {

        // Подключение политики
        $this->authorize(getmethod('index'), Lead::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('index'));

        $results = Lead::with('main_phones', 'lead_method', 'stage', 'estimate')
            ->where('case_number', $search)
            ->orWhere('name', 'LIKE', '%' . $search . '%')
            ->orWhere('company_name', 'LIKE', '%' . $search . '%')
            ->orWhereHas('phones', function ($q) use ($search) {
                $q->where('phone', $search)
                    ->orWhere('crop', $search);
            })
            ->companiesLimit($answer)
            ->filials($answer)
            ->authors($answer)
            // ->manager($user)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($results);

        // Подключение политики
//        $this->authorize('index', Lead::class);

//        $entityAlias = $this->entityAlias;
//
//        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
//        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));
//
//        $text_fragment = $request->text_fragment;
//        $fragment_phone = NULL;
//        $crop_phone = NULL;
//
//        $len_text = strlen($text_fragment);
//
//        if((strlen($text_fragment) == 11)&&(is_numeric($text_fragment))){
//            $fragment_phone = $text_fragment;
//        }
//
//        if((strlen($text_fragment) == 4)&&(is_numeric($text_fragment))){
//            $crop_phone = $text_fragment;
//        }
//
//        if(strlen($text_fragment) == 17){
//            $fragment_phone = cleanPhone($text_fragment);
//        }
//
//        if(strlen($text_fragment) > 6){
//            $fragment_case_number = $text_fragment;
//        } else {
//            $fragment_case_number = '';
//        }
//
//
//        if($len_text > 3){
//
//            // ------------------------------------------------------------------------------------------------------------
//            // ГЛАВНЫЙ ЗАПРОС
//            // ------------------------------------------------------------------------------------------------------------
//
//            $result_search = Lead::with(
//                'location.city',
//                'choice',
//                'manager',
//                'stage',
//                'challenges.challenge_type',
//                'phones')
//            ->companiesLimit($answer)
//            ->whereNull('draft')
//            ->where(function ($query) use ($fragment_case_number, $text_fragment, $len_text, $fragment_phone, $crop_phone) {
//
//                if($len_text > 5){
//                    $query->where('name', $text_fragment);
//                };
//
//                if(($len_text > 6)||($len_text < 14)){
//                    $query->orWhere('case_number', 'LIKE', $fragment_case_number);
//                };
//
//                if(isset($fragment_phone)){
//                    $query->orWhereHas('phones', function($query) use ($fragment_phone){
//                       $query->where('phone', $fragment_phone);
//                   });
//                };
//
//                if(isset($crop_phone)){
//                    $query->orWhereHas('phones', function($query) use ($crop_phone){
//                       $query->where('crop', $crop_phone);
//                   });
//                };
//
//            })
//            ->orderBy('created_at', 'asc')
//            ->get();
//
//        } else {
//            return '';
//        };
//
//        if($result_search->count()){
//
//            return view('includes.search_lead', compact('result_search', 'entityAlias'));
//        } else {
//
//            return '';
//        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $user = $request->user();

        // ГЛАВНЫЙ ЗАПРОС:
        $lead = Lead::with([
            'estimate' => function ($q) {
                $q->with([
                    'goods_items.reserve'
                ]);
            },
        ])
            ->withCount(['challenges' => function ($query) {
                $query->whereNull('status');
            }])
            ->withCount('claims')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
            ->manager($user)
            // ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $lead);

        // Удаляем комментарии
        $lead->notes()->delete();
        $lead->challenges()->delete();

        // Удаляем пользователя с обновлением
        $lead->destroy($id);

        if ($lead) {
            return redirect('/admin/leads');
        } else {
            abort(403, 'Что-то пошло не так!');
        };
    }

    /**
     * Печать чека
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function print($id)
    {
        // ГЛАВНЫЙ ЗАПРОС:
        $lead = Lead::with([
            'estimate.goods_items.goods.article',
        ])
            ->find($id);
        // dd($lead);

        // TODO - 13.11.20 - Если подключен чек (при печати чека)  то ссылаемся на него, если не подключен, тогда на стандартный системный
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('templates', false, getmethod('index'));

        $checkOrder = Template::companiesLimit($answer)
            ->moderatorLimit($answer)
            ->where('category_id', 2)
            ->where('tag', 'check-order')
            ->first();

        if ($checkOrder) {
            return view($checkOrder->path, compact('lead'));
        } else {
            return view('system.templates.prints.check_order', compact('lead'));
        }
    }

    /**
     * Печать складского стикера
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function print_sticker_stock($id)
    {
        // ГЛАВНЫЙ ЗАПРОС:
        $lead = Lead::with([
            'estimate.goods_items.goods.article',
        ])
            ->find($id);
        // dd($lead);

        // TODO - 13.11.20 - Если подключен чек (при печати чека)  то ссылаемся на него, если не подключен, тогда на стандартный системный
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('templates', false, getmethod('index'));

        $checkOrder = Template::companiesLimit($answer)
            ->moderatorLimit($answer)
            ->where('category_id', 2)
            ->where('tag', 'sticker-stock')
            ->first();

        if ($checkOrder) {
            return view($checkOrder->path, compact('lead'));
        } else {
            return view('system.templates.prints.sticker_stock', compact('lead'));
        }
    }



    // --------------------------------------- Ajax ----------------------------------------------------------

    // Добавление комментария
    public function ajax_add_note(Request $request)
    {

        $lead = Lead::find($request->id);

        if ($lead) {

            // Получаем данные для авторизованного пользователя
            $user = $request->user();

            $note = new Note;
            $note->body = $request->body;
            $note->company_id = $user->company_id;
            $note->author_id = hideGod($user);

            $lead->notes()->save($note);

            return view($request->entity . '.note', compact('note'));
        }
    }

    public function ajax_autofind_phone(Request $request)
    {

        // Подключение политики
        // $this->authorize('index', Lead::class);

        $phone = $request->phone;
        $lead_id = $request->lead_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_lead = operator_right('leads', true, 'index');

        // --------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------------

        $finded_leads = Lead::with(
            'location.city',
            'choice',
            'manager',
            'stage',
            'user',
            'challenges.challenge_type',
            'phones')
            ->companiesLimit($answer_lead)
            // ->authors($answer_lead) // Не фильтруем по авторам
            ->systemItem($answer_lead) // Фильтр по системным записям
            // ->whereNull('archive')
            ->whereNull('draft')
            ->whereHas('phones', function ($query) use ($phone) {
                $query->where('phone', $phone);
            })
            ->where('id', '!=', $lead_id)
            ->orderBy('sort', 'asc')
            ->get();

        // $user = $finded_leads->first()->user;

        if ($finded_leads->count() > 0) {

            return view('leads.autofind', compact('finded_leads'));

            // Фрагмент кода, который мы так долго искали ;)
            // return response()->json([
            //     'params' => любые параметры... ,
            //     'view' => view('leads.autofind', compact('finded_leads'))->render(),
            // ]);

        } else {
            return '';
        }
    }

    // Освобождение лида
    public function ajax_lead_free(Request $request)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $lead = Lead::find($request->id);

        if ($user->gender == 1) {
            $phrase_sex = 'освободил';
        } else {
            $phrase_sex = 'освободила';
        }
        $note = add_note($lead, $user->staff->first()->position->name . ': ' . $user->first_name . ' ' . $user->second_name . ' ' . $phrase_sex . ' лида.');

        $lead->manager_id = 1;
        $lead->save();

        return response()->json($lead);
    }

    // Назначение лида
    public function ajax_appointed_check(Request $request)
    {
        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $user = User::with('staff.position.charges')->find($user->id);

        foreach ($user->staff as $staffer) {
            // $staffer = $user->staff->first();

            $direction = null;

            foreach ($staffer->position->charges as $charge) {
                if ($charge->alias == 'lead-appointment') {
                    $direction = 1;
                    // break;
                }

                if (isset($request->manager_id)) {
                    if (($charge->alias == 'lead-appointment-self') && ($user->id == $request->manager_id)) {
                        $direction = 1;
                        // break;
                    }
                }
            }
        }
        return $direction;
    }

    // Прием лида менеджером
    public function ajax_lead_take(Request $request)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $lead = Lead::find($request->id);

        if ($lead->manager_id == 1) {

            // dd($direction);
            $lead->manager_id = $user->id;

            if ($lead->case_number == NULL) {

                // Формируем номера обращения
                $lead_number = getLeadNumbers($user, $lead);
                $lead->case_number = $lead_number['case'];
                $lead->serial_number = $lead_number['serial'];
            }

            $lead->editor_id = $user->id;
            $lead->save();

            // Ставим задачу
            $challenge = new Challenge;
            $challenge->company_id = $user->company_id;
            $challenge->appointed_id = $user->id;
            $challenge->challenges_type_id = 2;
            $challenge->author_id = $user->id;

            if ($lead->created_at->format('Y-m-d') == today()->format('Y-m-d')) {
                $challenge->description = "Перезвонить через 15 минут!\r\n";
                $challenge->priority_id = 3;
                // Отдаем график работы и время в секундах
                $challenge->deadline_date = getDeadline(null, 60 * 15);
            } else {
                $description = "Актуализировать информацию по лиду,\r\n";
                $description .= "этап - " . $lead->stage->name;
                $challenge->description = $description;
                $challenge->priority_id = 2;
                // Отдаем график работы и время в секундах (предварительно проверяем юзера на бога)
                $challenge->deadline_date = getDeadline(getSchedule($user), 60 * 15);
            }

            // Автоматическая поставнока задачи: временно отключена
            // TODO: Необходимо создать настройку, исходя из которой будет или не будет
            // автоматически ставиться задача

            // $lead->challenges()->save($challenge);
            // $lead->increment('challenges_active_count');

            if ($user->gender == 1) {
                $phrase_sex = 'принял';
            } else {
                $phrase_sex = 'приняла';
            }

            $note = add_note($lead, $user->staff->first()->position->name . ': ' . $user->first_name . ' ' . $user->second_name . ' ' . $phrase_sex . ' лида.');

            $result = [
                'id' => $lead->id,
                'name' => $lead->name ?? 'Имя не указано',
                'case_number' => $lead->case_number,
                'manager' => $lead->manager->first_name . ' ' . $lead->manager->second_name,
                'company_name' => $lead->company_name,
            ];
            return response()->json($result);
        }
    }

    // Назначение лида
    public function ajax_distribute(Request $request)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $lead = Lead::find($request->lead_id);

        $manager = User::find($request->appointed_id);
        $lead->manager_id = $manager->id;

        // Если номер пуст и планируется назначение на сотрудника, а не бота - то генерируем номер!
        if (($lead->case_number == NULL) && ($request->appointed_id != 1)) {

            // Формируем номера обращения
            $lead_number = getLeadNumbers($manager, $lead);
            $lead->case_number = $lead_number['case'];
            $lead->serial_number = $lead_number['serial'];
        }

        $lead->editor_id = $user->id;
        $lead->save();

        if ($request->appointed_id != 1) {

            // Ставим задачу

            $description = "Актуализировать информацию по лиду,\r\n";
            $description .= "этап - " . $lead->stage->name;

            $challenge = new Challenge;
            $challenge->company_id = $user->company_id;
            $challenge->appointed_id = $request->appointed_id;
            $challenge->challenges_type_id = 2;
            $challenge->description = $description;
            $challenge->priority_id = 2;
            $challenge->author_id = $user->id;

            // Отдаем график работы и время в секундах (предварительно проверяем юзера на бога)
            $challenge->deadline_date = getDeadline(getSchedule($manager), 60 * 60);

            $lead->challenges()->save($challenge);
            $lead->increment('challenges_active_count');

        }


        if ($user->gender == 1) {
            $phrase_sex = 'назначил';
        } else {
            $phrase_sex = 'назначила';
        }

        // Пишем комментарий
        $note = add_note($lead, $user->first_name . ' ' . $user->second_name . ' ' . $phrase_sex . ' лида менеджеру ' . $manager->first_name . ' ' . $manager->second_name);

        // Оповещаем менеджера о назначении
        if (isset($manager->telegram_id)) {
            $message = $user->first_name . ' ' . $user->second_name . ' ' . $phrase_sex . ' вам лида: ' . $lead->case_number . "\r\n\r\n";
            $message = lead_info($message, $lead);
            $telegram_destinations[] = $manager;

            send_message($telegram_destinations, $message);

        } else {

            if (isset($user->telegram_id)) {

                // Если у менеджера нет телеграмма, оповещаем руководителя
                $message = 'У ' . $manager->first_name . ' ' . $manager->second_name . " отсутствует Telegram ID, оповестите его другим способом!\r\n\r\n";
                $message = lead_info($message, $lead);

                $telegram_destinations[] = $user;
                send_message($telegram_destinations, $message);
            } else {
                $note = add_note($lead, 'Оповещение никому не выслано, так как ни у кого нет telegram Id.');
            }
        }

        $result = [
            'id' => $lead->id,
            'name' => $lead->name,
            'case_number' => $lead->case_number,
            'manager' => $lead->manager->first_name . ' ' . $lead->manager->second_name,
        ];

        return response()->json($result);
    }

    public function ajax_lead_appointed(Request $request)
    {

        $users = User::with('staff.position')
            ->whereHas('staff', function ($query) {
                $query->whereNotNull('user_id')->whereHas('position', function ($query) {
                    $query->whereHas('charges', function ($query) {
                        $query->whereIn('alias', ['lead-regular', 'lead-service', 'lead-dealer']);
                    });
                });
            })
            ->orWhere('id', 1)
            ->orderBy('second_name')
            ->get();
        // ->pluck('name', 'id');
        // dd($users);

        $users_list = [];
        foreach ($users as $user) {
            if (isset($user->staff->first()->position->name)) {
                $position = $user->staff->first()->position->name;
            } else {
                $position = 'Cyberdyne Systems 101 серии 800';
            }

            $users_list[$user->id] = $user->name . ' (' . $position . ')';
        }

        // dd($users_list);
        $lead_id = $request->id;
        // $lead_id = 1;
        return view('leads.modal-appointed', compact('users_list', 'lead_id'));
    }

    public function ajax_open_change_lead_type(Request $request)
    {
        $lead_type_list = LeadType::pluck('name', 'id');
        $lead_type_id = $request->lead_type_id;
        $lead_id = $request->lead_id;

        return view('leads.modal-change-lead-type', compact('lead_type_list', 'lead_type_id', 'lead_id'));
    }

    public function ajax_change_lead_type(Request $request)
    {
        $user = $request->user();
        $lead_id = $request->lead_id;
        $new_lead_type_id = $request->lead_type_id;

        $lead = Lead::find($lead_id);
        $lead_type_id = $lead->lead_type_id;
        $old_lead_type_name = $lead->lead_type->name;

        $manager_id = $lead->manager_id;
        $manager = User::find($manager_id);


        if ($new_lead_type_id !== $lead_type_id) {

            $lead->lead_type_id = $new_lead_type_id;

            // Получаем старый номер, если он существовал
            if (isset($lead->case_number)) {
                $old_case_number = $lead->case_number;
            };
            if (isset($lead->serial_number)) {
                $old_serial_number = $lead->case_number;
            };

            // Создаем пустой контейнер для нового номера
            $lead_number = [];
            $lead_number['case'] = null;
            $lead_number['serial'] = null;

            $lead_number = getLeadNumbers($manager, $lead);

            $lead->case_number = $lead_number['case'];
            $lead->serial_number = $lead_number['serial'];

            $lead->save();
            $lead = Lead::find($lead_id);
            $new_lead_type_name = $lead->lead_type->name;

            $note = add_note($lead, 'Сотрудник ' . $user->first_name . ' ' . $user->second_name . ' изменил тип обращения c "' . $old_lead_type_name . '" на "' . $new_lead_type_name . '", в связи с чем был изменен номер с ' . $old_case_number . ' на ' . $lead_number['case']);

        }

        $data = [];
        $data['case_number'] = $lead->case_number;
        $data['lead_type_name'] = $lead->lead_type->name;

        return $data;
    }

    public function export()
    {
        return Excel::download(new LeadsExport, 'Номера телефонов.xlsx');
    }

    public function sendEmail(Request $request, $id)
    {
        $lead = Lead::with([
            'user',
            'organization',
        ])
            ->find($id);
//        dd($lead);

        if (empty($lead)) {
            abort(403, __('errors.not_found'));
        }

        if (isset($lead->user)) {
            $user = $lead->user;
            $user->load([
                'arciveSubscriber',
                'client'
            ]);

            $subscriber = $user->arciveSubscriber;

            if ($subscriber) {

            } else {
                $subscriber = Subscriber::firstOrCreate([
                    'email' => $lead->email,
                    'site_id' => $user->site_id
                ]);

                $subscriber->update([
                    'subscriberable_id' => $user->id,
                    'subscriberable_type' => 'App\User',
                    'name' => $user->name,
                    'denied_at' => null,
                    'is_self' => 0,
                    'client_id' => optional($user->client)->id,
                ]);
            }
        }

        dd(__METHOD__, $lead);
    }

    /**
     * Поиск клиента физика по номеру телефона
     *
     * @param $phone
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchUserByPhone($phone)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('users', true, 'index');

        $user = User::with([
            'client.clientable',
            'organizations.client',
            'location',
            'main_phones'
        ])
            ->where('site_id', '!=', 1)
            ->whereHas('main_phones', function ($q) use ($phone) {
                $q->where('phone', $phone);
            })
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->first();

        return response()->json($user);
    }

    /**
     * Поиск клиента юрика по имени
     *
     * @param $name
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchCompaniesByName($name)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('companies', false, 'index');

        $companies = Company::with([
            'client.clientable',
            'representatives' => function ($q) {
                $q->with([
                    'client'
                ])
                    ->latest();
            }
        ])
            ->where('name', 'like', '%' . $name . '%')
            ->whereHas('companies', function ($q) {
                $q->where('id', auth()->user()->company_id);
            })
            ->moderatorLimit($answer)
//            ->companiesLimit($answer)

            ->authors($answer)
            ->systemItem($answer)
            ->get();

        return response()->json($companies);
    }

    /**
     * ПОлучаем историю лида по неомеру телефона
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLeadHistory(Request $request)
    {
        $phone = $request->phone;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('leads', true, getmethod('edit'));

        // История лида
        $leadHistory = Lead::with([
            'location.city',
            'choice',
            'manager',
            'stage',
            'user',
            'challenges.challenge_type',
            'phones',
        ])
            ->companiesLimit($answer)
            ->systemItem($answer)
            ->where('draft', false)
            ->whereHas('phones', function ($query) use ($phone) {
                $query->where('phone', cleanPhone($phone));
            })
            ->where('id', '!=', $request->id)
            ->oldest('sort')
            ->get();

        return response()->json($leadHistory);

    }

    public function getUserFilialsWithOutlets(Request $request)
    {
        $outlets = session("access.user_info.outlets");
        $filials = null;
        if ($outlets) {
            $filialsIds = [];
            foreach ($outlets as $outlet) {
                $filialsIds[] = $outlet->filial_id;
            }

            $filials = Department::select([
                'id',
                'name'
            ])
            ->find($filialsIds);
        }

        $data = [
            'filials' => $filials,
            'outlets' => $outlets
        ];

        return response()->json($data);

    }
}
