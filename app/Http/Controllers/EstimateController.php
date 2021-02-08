<?php

namespace App\Http\Controllers;

use App\Agent;
use App\Company;
use App\ContractsClient;
use App\Entity;
use App\Http\Controllers\System\Traits\Cancelable;
use App\Http\Controllers\Traits\Receiptable;
use App\Models\System\Documents\Production;
use App\Models\System\Documents\ProductionsItem;
use App\Notification;
use App\Notifications\System\Telegram;
use App\Http\Controllers\System\Traits\Clientable;
use App\Http\Controllers\System\Traits\Companable;
use App\Http\Controllers\System\Traits\Locationable;
use App\Http\Controllers\System\Traits\Phonable;
use App\Http\Controllers\Traits\Estimatable;
use App\Http\Controllers\Traits\Offable;
use App\Http\Controllers\Traits\Photable;
use App\Http\Controllers\Traits\Reservable;
use App\Http\Controllers\Traits\UserControllerTrait;
use App\Http\Requests\System\LeadRequest;
use App\Models\System\Documents\Estimate;
use App\Http\Controllers\Traits\LeadControllerTrait;
use App\Lead;
use Illuminate\Http\Request;

class EstimateController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * EstimateController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'estimates';
        $this->entityDependence = true;
    }

    use UserControllerTrait,
        LeadControllerTrait,
        Estimatable,
        Locationable,
        Phonable,
        Photable,
        Clientable,
        Companable,
        Reservable,
        Offable,
        Receiptable,
        Estimatable,
        Cancelable;

    public function index(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Estimate::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        // dd($answer);


        $estimates = Estimate::with([
            'agent',
            'client.clientable.location',
            'goods_items',
            'author',
            'payments',
            'lead'
        ])
            // ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->filials($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->where('draft', false)
            ->whereNotNull('registered_at')
            ->filter()
            ->orderByDesc('created_at')
            ->paginate(30);


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
            // 'client',               // Клиенты
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------


        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('estimates.index', compact('estimates', 'pageInfo'));
    }

    public function search(Request $request, $search)
    {

        $this->authorize(getmethod('index'), Estimate::class);
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('index'));

        $results = Estimate::with('lead')
            ->where('number', $search)
            ->orWhereHas('lead', function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('company_name', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('phones', function ($q) use ($search) {
                        $q->where('phone', $search)
                            ->orWhere('crop', $search);
                    });
            })
            ->whereNotNull('registered_at')
            ->companiesLimit($answer)
            ->filials($answer)
            ->authors($answer)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($results);

    }

    // Экспериментальный метод
    // Проверить на больших объемах данных - быстрее ли этот запрос чем запрос без LIKE
    public function search_crop_phone(Request $request, $search)
    {

        // TODO Попробовать сделать поиск через обращенеи phone(crop)->leads->estimate

        $results = Estimate::with('lead')
            ->where('number', $search)
            ->orWhereHas('lead', function ($q) use ($search) {
                $q->whereHas('phones', function ($q) use ($search) {
                    $q->where('crop', $search);
                });
            })
            ->whereNotNull('registered_at')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($results);

    }

    /**
     * Регистрация сметы
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registering(LeadRequest $request, $id)
    {
        // ГЛАВНЫЙ ЗАПРОС:
        $estimate = Estimate::with([
            'lead',
            'goods_items.goods'
        ])
            ->find($id);

        if ($estimate->registered_at) {

            // Отдаем работу по редактировнию лида трейту
            $this->updateLead($request, $estimate->lead);

            foreach ($estimate->goods_items as $goodsItem) {
                if ($goodsItem->goods->archive == 1) {
                    return back()
                        ->withErrors(['msg' => 'Смета содержит архивные позиции, оформление невозможно!']);
                }
            }

            if (isset($request->company_name)) {
                $company = $this->checkCompanyByPhone(cleanPhone($request->main_phone));


                if ($company) {

                } else {
                    $data = $request->input();
                    $location = $this->getLocation();
                    $data['location_id'] = $location->id;

                    $data['name'] = $request->company_name;
//        dd($data);

                    $company = Company::create($data);
                    $this->savePhones($company);

                    dd($company);
                }
            }


            logs('documents')->info("========================================== НАЧАЛО РЕГИСТРАЦИИ СМЕТЫ, ID: {$estimate->id} =============================================== ");

            // Обновляем смету
//            $amount = 0;
//            $discount = 0;
//            $total = 0;
//
//            if ($estimate->goods_items->isNotEmpty()) {
//
//                $amount = $estimate->goods_items->sum('amount');
//                $discount = (($amount * $estimate->discount_percent) / 100);
//                $total = ($amount - $discount);
//            }

            // Пишем склады при оформлении
            $settings = getSettings();
            if ($settings) {

                $estimate->load([
                    'goods_items'
                ]);

                if ($estimate->goods_items->isNotEmpty()) {

                }
            }

            if (isset($request->company_name)) {
                $client = $this->getClientCompany($company->id);
            } else {
                // Ищем или создаем клиента
                $client = $this->getClientUser($estimate->lead->user_id);
            }


            if (is_null($client->source_id)) {
                $client->update([
                    'source_id' => $estimate->lead->source_id
                ]);
            }

            $estimate->lead->update([
                'client_id' => $client->id
            ]);

            $contracts_client = ContractsClient::create([
                'client_id' => $client->id,
                'amount' => $estimate->total,
            ]);

            $estimate->update([
                'client_id' => $client->id,
                'registered_at' => now(),
            ]);

            logs('documents')->info("========================================== КОНЕЦ РЕГИСТРАЦИИ СМЕТЫ, ID: {$estimate->id} =============================================== ");

            return redirect()
                ->route('leads.edit', $estimate->lead_id)
                ->with(['success' => 'Успешно оформлено']);

        } else {
            return back()
                ->withErrors(['msg' => 'Смета уже оформлена'])
                ->withInput();
        }
    }

    /**
     * Отмена регистраии сметы
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unregistering($id)
    {
        // ГЛАВНЫЙ ЗАПРОС:
        $estimate = Estimate::with([
            'goods_items'
        ])
            ->find($id);

        if ($estimate->registered_at && $estimate->payments->isEmpty()) {
            foreach ($estimate->goods_items as $goodsItem) {
                if (isset($goodsItem->reserve)) {
                    $result = $this->cancelReserve($goodsItem);
                }

                if ($goodsItem->share_percent > 0) {
                    $goodsItem->update([
                        'share_percent' => 0,

                        'agent_id' => null,
                        'agency_scheme_id' => null,
                    ]);
                }
            }

            $estimate->update([
                'registered_at' => null,

                'agent_id' => null,
                'agency_scheme_id' => null,
            ]);

            // Аггрегируем значеняи сметы
            $this->aggregateEstimate($estimate);
        }

        $estimate = Estimate::with([
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
            'payments',
            'lead.client.contract',
            'discounts'
        ])
            ->find($id);

        return response()->json($estimate);
    }

    public function reserving($id)
    {

        $estimate = Estimate::with([
            'goods_items' => function ($q) {
                $q->with([
                    'price',
                    'product.article',
                    'document',
                    'reserve.history'
                ]);
            },
        ])
            ->find($id);
//        dd($estimate);

        if (!$estimate->conducted_at) {

            if ($estimate->goods_items->isNotEmpty()) {
                logs('documents')
                    ->info('========================================== НАЧАЛО РЕЗЕРВИРОВАНИЯ СМЕТЫ, ID: ' . $estimate->id . ' ==============================================');

                $result = [];
                foreach ($estimate->goods_items as $item) {
                    $result[] = $this->reserve($item);
                }

                logs('documents')
                    ->info('========================================== КОНЕЦ РЕЗЕРВИРОВАНИЯ СМЕТЫ ==============================================

                ');

                $estimate->load([
                    'goods_items' => function ($q) {
                        $q->with([
                            'product.article',
                            'stock:id,name',
                            'price_goods',
                        ]);
                    }
                ]);
                return response()->json([
                    'items' => $estimate->goods_items,
                    'msg' => $result
                ]);
            } else {
                abort(403, 'Смета пуста');
            }
        }
    }

    /**
     * Снятие резерва со сметы
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreserving(Request $request, $id)
    {
//        dd($request);

        // ГЛАВНЫЙ ЗАПРОС:
        $estimate = Estimate::with([
            'goods_items' => function ($q) {
                $q->with([
                    'product.article',
                    'document',
                    'reserve.history'
                ]);
            },
        ])
            ->find($id);
//        dd($estimate);

        if (!$estimate->conducted_at) {
            // Подключение политики
//        $this->authorize(getmethod('update'), $lead);

//            $lead = $estimate->lead;
//            // Отдаем работу по редактировнию лида трейту
////            $this->updateLead($request, $lead);
//
//            $estimate->update([
//                'stock_id' => $request->stock_id
//            ]);

            if ($estimate->goods_items->isNotEmpty()) {
//                            dd('Ща буит');

                logs('documents')
                    ->info('========================================== НАЧАЛО ОТМЕНЫ РЕЗЕРВИРОВАНИЯ СМЕТЫ, ID: ' . $estimate->id . ' ==============================================');

                $result = [];
                foreach ($estimate->goods_items as $item) {
//                    $item->load('document');
                    $result[] = $this->cancelReserve($item);
                }

                logs('documents')
                    ->info('========================================== КОНЕЦ ОТМЕНЫ РЕЗЕРВИРОВАНИЯ СМЕТЫ ==============================================

                ');

                $estimate->load([
                    'goods_items' => function ($q) {
                        $q->with([
                            'product.article',
                            'reserve',
                            'stock:id,name'
                        ]);
                    }
                ]);
                return response()->json([
                    'items' => $estimate->goods_items,
                    'msg' => $result
                ]);

            } else {
                abort(403, 'Смета пуста');
            }
        }


    }

    /**
     * Производство сметы
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function producing($id)
    {

        // ГЛАВНЫЙ ЗАПРОС:
        $estimate = Estimate::with([
            'goods_items' => function ($q) {
                $q->with([
                    'price',
                    'product' => function ($q) {
                        $q->with([
                            'article' => function ($q) {
                                $q->with([
                                    'goods',
                                    'raws',
                                ]);
                            },
                        ]);
                    },
                    'document',
                ])
                    ->whereHas('product', function ($q) {
                        $q->where('is_produced', true);
                    });
            },

            'lead.outlet'
        ])
            ->find($id);

        if (!$estimate->conducted_at && !$estimate->produced_at) {
            if ($estimate->goods_items->isNotEmpty()) {

                set_time_limit(0);

                logs('documents')
                    ->info('========================================== НАЧАЛО ПРОИЗВОДТСВА СМЕТЫ, ID: ' . $estimate->id . ' ==============================================');

                $production = Production::create([
                    'stock_id' => $estimate->lead->outlet->stock_id,
                    'estimate_id' => $estimate->id
                ]);

                logs('documents')
                    ->info("Создан наряд на производство. Id: {$production->id}");

                $productionItemsInsert = [];

                $entityId = Entity::where('alias', 'goods')
                    ->value('id');
                foreach ($estimate->goods_items as $goodsItem) {

                    if ($goodsItem->product->is_produced) {
                        $productionItemsInsert[] = ProductionsItem::make([
                            'cmv_type' => 'App\Goods',
                            'cmv_id' => $goodsItem->goods_id,

                            'manufacturer_id' => $goodsItem->product->article->manufacturer_id,
                            'estimates_goods_item_id' => $goodsItem->id,
                            'stock_id' => $production->stock_id,
                            'entity_id' => $entityId,

                            'count' => $goodsItem->count,
                        ]);
                    }
                }

                $production->items()->saveMany($productionItemsInsert);
                logs('documents')
                    ->info("Созданы пункты наряда на производство (производимые)");

                $production->load('items');
                foreach ($production->items as $item) {
                    // Без проверки остатка
                    $res = $this->production($item);
                    $cost = $res['cost'];
                    $isWrong = $res['is_wrong'];
                    $amount = $cost * $item->count;

                    $item->update([
                        'cost' => $cost,
                        'amount' => $amount,
                    ]);

                    // Приходование
                    $this->receipt($item, $isWrong);
                }

                $production->update([
                    'conducted_at' => now(),
                ]);
                logs('documents')
                    ->info('Произведен наряд на производство c id: ' . $production->id);

                $estimate->update([
                    'produced_at' => now()
                ]);
                logs('documents')
                    ->info('Произведена смета c id: ' . $estimate->id);

                logs('documents')
                    ->info('========================================== КОНЕЦ ПРОИЗВОДТСВА СМЕТЫ ==============================================

                ');
            }
        }

        $estimate->load([
            'catalogs_goods',
            'catalogs_services',
            'discounts',
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
        ]);

        return response()->json([
            'success' => true,
            'estimate' => $estimate
        ]);
    }

    /**
     * Продажа сметы
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function conducting($id)
    {
        // ГЛАВНЫЙ ЗАПРОС:
        $estimate = Estimate::with([
            'client',
            'goods_items' => function ($q) {
                $q->with([
                    'document',
                    'cmv' => function ($q) {
                        $q->with([
                            'article',
                            'stocks',
                        ]);
                    },
                ]);
            },
        ])
            ->find($id);

        if (empty($estimate->conducted_at)) {

            $user = auth()->user();
            $useStock = $user->company->settings->firstWhere('alias', 'sale-from-stock');

            $outlet = $user->staff->first()->filial->outlet;
            if ($outlet) {
                $checkFree = $user->staff->first()->filial->outlets->first()->settings->firstWhere('alias', 'stock-check-free');
            } else {
                $checkFree = null;
            }

            // Если нужна проверка остатка на складах
            if ($useStock && $checkFree) {
                $errors = [];

                $number = 1;

                foreach ($estimate->goods_items as $item) {

                    // Проверяем позицию сметы
                    $stock = $item->stock;
                    $storage = $item->product->stocks
                        ->where('stock_id', $stock->id)
                        ->where('filial_id', $stock->filial_id)
                        ->where('manufacturer_id', $item->product->article->manufacturer_id)
                        ->first();
//                          dd($storage);

                    if ($storage) {

                        // проверяем наличие резерва по позиции
                        if (optional($item->reserve)->count > 0) {
                            if ($storage->reserve < $item->reserve->count) {
                                $dif = $storage->reserve - $item->reserve->count;
                                $errors[]['msg'] = "Для зарезервированной позиции \"{$item->product->article->name}\" не хватает в резерве склада {$dif} для продажи";
                            }
                            $total = $storage->free - ($item->count - $item->reserve->count);
                        } else {
                            $total = $storage->free - $item->count;
                        }

                        if ($total < 0) {
                            $errors[]['msg'] = "Для позиции \"{$item->product->article->name}\" не хватает {$total} {$item->product->article->unit->abbreviation} для продажи";
                        }
                    } else {
                        $errors[]['msg'] = "Для позиции {$number} не существует склада для {$item->product->article->name}";
                    }

                    $number++;
                }
//	        dd($result);

                if ($errors) {
//                dd($errors);
                    return response()->json([
                        'success' => false,
                        'errors' => $errors
                    ]);
                };
            }

            logs('documents')
                ->info('========================================== НАЧАЛО ПРОДАЖИ СМЕТЫ, ID: ' . $estimate->id . ' ==============================================
                ');

            if ($estimate->goods_items->isNotEmpty() && $useStock) {
                foreach ($estimate->goods_items as $item) {
                    $this->off($item);
                }
            }

            // Аггрегируем значеняи сметы
            $this->aggregateEstimate($estimate);

            $estimate->update([
                'conducted_at' => now(),
            ]);

            // Обновляем показатели клиента
            $this->setClientIndicators($estimate);

            logs('documents')
                ->info('Продана смета c id: ' . $estimate->id);
            logs('documents')
                ->info('========================================== КОНЕЦ ПРОДАЖИ СМЕТЫ ==============================================

                ');

//            return redirect()
//                ->route('leads.edit', $estimate->lead_id)
//                ->with(['success' => 'Успешно проведено']);
        }

        $estimate->load([
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
            'payments',
            'discounts',
            'labels'
        ]);

//        return redirect()->route('leads.index');
        return response()->json([
            'success' => true,
            'estimate' => $estimate
        ]);
    }

    /**
     * Списание сметы
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function dismissing(Request $request, $id)
    {

        // ГЛАВНЫЙ ЗАПРОС:
        $estimate = Estimate::with([
            'production',
            'catalogs_goods',
            'catalogs_services',
            'discounts'
        ])
            ->find($id);

        logs('documents')
            ->info('========================================== НАЧАЛО СПИСАНИЯ СМЕТЫ, ID: ' . $estimate->id . ' ==============================================');

        // Если есть платежи
        if ($estimate->payments->isNotEmpty()) {

        }

        // Если было производство по смете и списание без убытка
        if ($request->loss == false && $estimate->production) {

            $production = $estimate->production;

            if (isset($production->conducted_at)) {
                $production->load([
                    'items' => function ($q) {
                        $q->with([
                            'cmv' => function ($q) {
                                $q->with([
                                    'article',
                                    'cost'
                                ]);
                            },
                            'entity',
                            'receipt.storage',
                            'offs' => function ($q) {
                                $q->with([
                                    'cmv' => function ($q) {
                                        $q->with([
                                            'cost',
                                            'stocks',
                                            'article'
                                        ]);
                                    },
                                    'storage'
                                ]);
                            },
                            'document'
                        ]);
                    },
                    'receipts' => function ($q) {
                        $q->with([
                            'storage'
                        ]);
                    }
                ]);

//                if ($production->items->isNotEmpty()) {
//
//                    foreach ($production->receipts as $receipt) {
//                        $storage = $receipt->storage;
//
//                        if ($storage->free < $receipt->count) {
//                            return back()
//                                ->withErrors(['msg' => 'Наряд содержит позиции, в которых на остатках нет нужного количества для возврата!']);
//                        }
//                    }
//                }

                logs('documents')
                    ->info('========================================== ОТМЕНА НАРЯДА ПРОИЗВОДСТВА ==============================================');

                foreach ($production->items as $item) {
                    logs('documents')
                        ->info('=== ПЕРЕБИРАЕМ ПУНКТ ' . $item->getTable() . ' ' . $item->id . ' ===
                        ');
                    $this->cancelOffs($item);

                    $this->cancelReceipt($item);

                }

                $production->update([
                    'conducted_at' => null
                ]);

                logs('documents')
                    ->info('Отменен наряд c id: ' . $production->id);
                logs('documents')
                    ->info('========================================== КОНЕЦ ОТМЕНЫ НАРЯДА ПРОИЗВОДСТВА ==============================================

				');
            }
        };

        $estimate->update([
            'is_dismissed' => true,
            'cancel_ground_id' => $request->estimates_cancel_ground_id
        ]);

        logs('documents')
            ->info('Списана смета c id: ' . $estimate->id);
        logs('documents')
            ->info('========================================== КОНЕЦ СПИСАНИЯ СМЕТЫ ==============================================

                ');

        return response()->json([
            'success' => true,
            'estimate' => $estimate
        ]);

    }

    public function ajax_create(Request $request)
    {

        $lead = Lead::find($request->lead_id);

        // TODO - 24.10.19 - Скидка должна браться из ценовой политики

        $estimate = Estimate::firstOrNew([
            'lead_id' => $lead->id,
            'filial_id' => $lead->filial_id,
            'client_id' => $lead->client_id,
            'stock_id' => $request->stock_id,
            'discount_percent' => 0,
        ]);

        // dd($estimate);

        if (!$estimate->id) {
            $estimate->save();
            return response()->json($estimate->id);
        }
    }

    public function ajax_update(Request $request)
    {

        $result = Estimate::find($request->estimate_id)
            ->update([
                'stock_id' => $request->stock_id
            ]);
        // dd($result);

        return response()->json($result);
    }

    /**
     * Устанавливаем агента на смету, перерасчиываем позиции и агрегируем смету
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setAgent(Request $request)
    {

        $catalogGoodsId = $request->catalog_goods_id;
        $agent = Agent::with([
            'schemes' => function ($q) use ($catalogGoodsId) {
                $q->where('catalog_id', $catalogGoodsId);
            }
        ])
            ->whereHas('schemes', function ($q) use ($catalogGoodsId) {
                $q->where('catalog_id', $catalogGoodsId);
            })
            ->find($request->agent_id);

        $agencyScheme = $agent->schemes->first();

        $estimate = Estimate::with([
            'goods_items',
            'services_items'
        ])
            ->find($request->estimate_id);

        foreach ($estimate->goods_items as $item) {
            $item->update([
                'agent_id' => $agent->id,
                'agency_scheme_id' => $agencyScheme->id,
                'share_percent' => $agencyScheme->percent_default,
            ]);
        }

        foreach ($estimate->services_items as $item) {
            $item->update([
                'agent_id' => $agent->id,
                'agency_scheme_id' => $agencyScheme->id,
                'share_percent' => $agencyScheme->percent_default,
            ]);
        }

        $this->aggregateEstimate($estimate);

        $estimate->update([
            'agent_id' => $agent->id,
            'agency_scheme_id' => $agencyScheme->id,
        ]);
        // dd($result);

        $note = "Передан агенту: " . $agent->company->name . "\r\n";
        $note .= "Вознаграждение агента: " . num_format($estimate->share_currency, 0) . "\r\n";
        $note .= "Вознаграждения принципала: " . num_format($estimate->principal_currency, 0) . "\r\n";

        $estimate = Estimate::with([
            'goods_items' => function ($q) {
                $q->with([
                    'goods.article',
                    'reserve',
                    'stock:id,name',
                    'price_goods',
                    'currency'
                ]);
            },
            'discounts',
            'agent.company',
            'lead'
        ])
            ->find($request->estimate_id);

        $notificationId = Notification::where('name', 'Прием заказа от партнера')
            ->value('id');

        if ($notificationId) {

            $lead = $estimate->lead;

            // Формируем сообщение
            $message = "Заказ от партнера №{$estimate->number}\r\n";
            $message .= "Город: {$lead->location->city->name}\r\n";
            $message .= "Имя клиента: {$lead->name}\r\n";
            $message .= "Тел: " . decorPhone($lead->main_phone->phone) . "\r\n";
            if ($lead->description) {
                $message .= "Примечание: {$lead->description}\r\n";
            };

            if ($estimate->goods_items->isNotEmpty()) {
                $estimate->goods_items->load([
                    'goods.article'
                ]);

                $message .= "\r\nСостав заказа:\r\n";
                $num = 1;
                foreach ($estimate->goods_items as $item) {
                    $message .= $num . ' - ' . $item->goods->article->name . ": " . num_format($item->count, 0) .
                        ' ' . $item->goods->article->unit->abbreviation . " (" . num_format($item->total, 0) . " руб.) \r\n";
                    $num++;
                }
                $message .= "\r\n";
            }

            $message .= "Кол-во товаров: " . num_format($estimate->goods_items->sum('count'), 0) . "\r\n";
            $message .= "Сумма заказа: " . num_format($estimate->amount, 0) . ' руб.' . "\r\n";

            if ($estimate->discount_currency > 0) {
                $message .= "Сумма со скидкой: " . num_format($estimate->total, 0) . ' руб.' . "\r\n";
                $message .= "Скидка: " . num_format($estimate->discount_currency, 0) . ' руб.' . "\r\n";
            }
            $message .= "\r\n";

            if ($lead->shipment_at) {
                $message .= "Доставить к " . $lead->shipment_at->format('H:i');
                $message .= "\r\n";
            }
            Telegram::send($notificationId, $message, $agent->agent_id);
        }

        $lead->notes()->create([
            'company_id' => $lead->company_id,
            'body' => $note,
            'author_id' => 1,
        ]);

        return response()->json([
            'success' => true,
            'estimate' => $estimate
        ]);
    }
}
