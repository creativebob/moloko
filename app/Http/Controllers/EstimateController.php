<?php

namespace App\Http\Controllers;

use App\Client;
use App\Company;
use App\ContractsClient;
use App\Http\Controllers\System\Traits\Clientable;
use App\Http\Controllers\System\Traits\Companable;
use App\Http\Controllers\System\Traits\Locationable;
use App\Http\Controllers\System\Traits\Phonable;
use App\Http\Controllers\Traits\Offable;
use App\Http\Controllers\Traits\Photable;
use App\Http\Controllers\Traits\Reservable;
use App\Http\Controllers\Traits\UserControllerTrait;
use App\Http\Requests\System\LeadRequest;
use Illuminate\Support\Facades\Log;
use App\Estimate;
use App\Http\Controllers\Traits\LeadControllerTrait;
use App\Lead;
use App\Stock;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EstimateController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Estimate $estimate)
    {
        $this->middleware('auth');
        $this->estimate = $estimate;
        $this->class = Estimate::class;
        $this->model = 'App\Estimate';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    use UserControllerTrait;
    use LeadControllerTrait;
    use Locationable;
    use Phonable;
    use Photable;
    use Clientable;
    use Companable;
    use Reservable;
    use Offable;



    public function index(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $estimates = Estimate::with([
            'client.clientable.location',
            'goods_items',
            'author',
            'payments',
            'lead'
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            // ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->where('draft', false)
            ->where('is_registered', true)
//        ->whereNotNull('client_id')
//            ->booklistFilter($request)  // Фильтр по спискам
//            ->filter($request, 'client_id')
            ->filter()
            ->orderByDesc('created_at')
            ->paginate(30);
//         dd($estimates);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            // 'client',               // Клиенты
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------


        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('estimates.index', compact('estimates', 'pageInfo'));
    }


    public function search(Request $request, $search)
    {

        $results = Estimate::with('lead')
        ->where('number', $search)
            ->orWhereHas('lead', function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('company_name', 'LIKE', '%' . $search . '%')
                ->orWhereHas('phones', function ($q) use ($search) {
                    $q->where('phone', $search)
                    ->orWhere('crop', $search);
                });
            })->where('is_registered', true)
            ->orderBy('created_at', 'desc')
            ->get();

            // Модифицируем данные 
            $modified = $results->map(function($value, $key) {
                $value['total'] = num_format($value['total'], 0);
                // $value['created_at'] = Carbon::parse($value['created_at']);
                return $value;
            });

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
        })->where('is_registered', true)
        ->orderBy('created_at', 'desc')
        ->get();
        return response()->json($results);

    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }

    public function show(Request $request, $id)
    {
        //
    }

    public function edit(Request $request, $id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy(Request $request, $id)
    {
        //
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

        if ($estimate->is_registered == 0) {

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
                'client_id' =>  $client->id
            ]);

            $contracts_client = ContractsClient::create([
                'client_id' => $client->id,
                'amount' => $estimate->total,
            ]);

            $estimate->update([
                'client_id' => $client->id,
                'is_registered' => true,
                'registered_date' => today(),
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
     * Производство сметы
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function produce(Request $request, $id)
    {

        // ГЛАВНЫЙ ЗАПРОС:
        $estimate = Estimate::with([
            'lead',
            'goods_items' => function ($q) {
                $q->with([
                    'price',
                    'product',
                    'document'
                ]);
            },
        ])
            ->find($id);

        if ($estimate->is_saled == 0) {
            // Подключение политики
//        $this->authorize(getmethod('update'), $lead);

            $lead = $estimate->lead;
            // Отдаем работу по редактировнию лида трейту
            $this->updateLead($request, $lead);

            $estimate->update([
                'stock_id' => $request->stock_id
            ]);

            if ($estimate->goods_items->isNotEmpty()) {
                //            dd('Ща буит');

                $stock_general = Stock::find($request->stock_id);

                $leftover = 1;
                // Если нужна проверка остатка на складах
                if ($leftover) {
                    $result = [];
                    $errors = [];

                    $number = 1;

                    foreach ($estimate->goods_items as $item) {

//                        $entity_document = Entity::where('alias', $item->document->getTable())->first();
//                        $model_document = 'App\\' . $entity_document->model;

//                        if ($item->document->getTable() == 'estimates') {
//                            $model_document_item = $model_document.'sGoodsItem';
//                        } else {
//                            $model_document_item = $model_document.'sItem';
//                        }

                        // Проверяем позицию сметы
                        $stock = $item->stock;
                        $storage = $item->product->stocks->where('stock_id', $stock->id)->where('filial_id', $stock->filial_id)->where('manufacturer_id', $item->product->article->manufacturer_id)->first();
//                          dd($storage);

                        if ($storage) {

//                            $entity_storage = Entity::where('alias', $storage->getTable())->first();
//                            $model_storage = 'App\\' . $entity_storage->model;

                            // проверяем наличие резерва по позиции
                            if (optional($item->reserve)->count > 0) {
                                if ($storage->reserve < $item->reserve->count) {
                                    $dif = $storage->reserve - $item->reserve->count;
                                    $errors['msg'][] = "Для зарезервированной позиции \"{$item->product->article->name}\" не хватает в резерве склада {$dif} для продажи";
                                }
                                $total = $storage->free - ($item->count - $item->reserve->count);
                            } else {
                                $total = $storage->free - $item->count;
                            }

                            if ($total < 0) {
                                $errors['msg'][] = "Для позиции \"{$item->product->article->name}\" не хватает {$total} {$item->product->article->unit->abbreviation} для продажи";
                            } else {
//                                $result[$item->id][] = [
//                                    'entity' => $item->getTable(),
//                                    'id' => $item->id,
//                                    'model' => $model_document_item,
//                                    'stock_id' => $storage->id,
//                                    'stock_model' => $model_storage,
//                                    'price' => $item->price,
//                                    'amount' => $item->amount,
//                                    'count' => $item->count
//                                ];
                            }
                        } else {
                            $errors['msg'][] = "Для позиции {$number} не существует склада для {$item->product->article->name}";
                        }

                        $number++;
                    }
//	        dd($result);

                    if ($errors) {
//                dd($errors);
                        return back()
                            ->withErrors($errors)
                            ->withInput();
                    };
                }

                Log::channel('documents')
                    ->info('========================================== НАЧАЛО ПРОИЗВОДТСВА СМЕТЫ, ID: ' . $estimate->id . ' ==============================================');

                $estimate->goods_items->load('document');

                // Если нужна проверка остатка на складах
//                if ($leftover) {
//
//                } else {
//
//                }


                foreach ($estimate->goods_items as $item) {
                    $this->off($item);
                }

                // ОБновляем смету
                $estimate->load('goods_items');

                if ($estimate->goods_items->isNotEmpty()) {

                    $amount = $estimate->goods_items->sum('amount');
                    $discount = (($amount * $estimate->discount_percent) / 100);
                    $total = ($amount - $discount);

                    $data = [
                        'amount' => $amount,
                        'discount' => $discount,
                        'total' => $total,
                        'is_produced' => true,
                    ];
                }
                $estimate->update($data);

                Log::channel('documents')
                    ->info('Произведена смета c id: ' . $estimate->id);
                Log::channel('documents')
                    ->info('========================================== КОНЕЦ ПРОИЗВОДТСВА СМЕТЫ ==============================================
                
                ');

                // TODO - 06.02.20 - Временная запись клиента и договора
                $client = new Client;
                $client->clientable_id = $estimate->lead->user_id;
                $client->clientable_type = 'App\User';
                $client->company_id = $request->user()->company_id;
                $client->save();

                $estimate->lead->update([
                   'client_id' =>  $client->id
                ]);

                $data = [
                    'client_id' => $client->id,
                    'date' => now()->format('d.m.Y'),
                    'amount' => $total,
                    'debit' => $total,
                ];
                $contracts_client = ContractsClient::create($data);
                $contracts_client->update([
                    'number' => $contracts_client->id
                ]);

                return redirect()
                    ->route('leads.edit', $estimate->lead_id)
                    ->with(['success' => 'Успешно произведено']);

            } else {
                return back()
                    ->withErrors(['msg' => 'Смета пуста'])
                    ->withInput();
//                abort(403, 'Смета пуста');
            }
        }

//        return redirect()->route('leads.index');
    }

    /**
     * Продажа сметы
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saling($id)
    {
        // ГЛАВНЫЙ ЗАПРОС:
        $estimate = Estimate::with([
            'client'
        ])
        ->find($id);

        if ($estimate->is_saled == 0) {

            // Обновляем показатели клиента
            $this->setIndicators($estimate);

            $estimate->update([
                'is_saled' => true,
                'saled_date' => today(),
            ]);

        }

        return response()->json($estimate);
    }

    public function reserving(Request $request, $id)
    {
//        dd($request);

        // ГЛАВНЫЙ ЗАПРОС:
        $estimate = Estimate::with([
//            'lead',
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

        if ($estimate->is_saled == 0) {
            // Подключение политики
//        $this->authorize(getmethod('update'), $lead);

//            $lead = $estimate->lead;
//            // Отдаем работу по редактировнию лида трейту
//            $this->updateLead($request, $lead);

            if ($estimate->goods_items->isNotEmpty()) {
                Log::channel('documents')
                    ->info('========================================== НАЧАЛО РЕЗЕРВИРОВАНИЯ СМЕТЫ, ID: ' . $estimate->id . ' ==============================================');

                $result = [];
                foreach ($estimate->goods_items as $item) {
//                    $item->load('document');
                    $result[] = $this->reserve($item);
                }

                Log::channel('documents')
                    ->info('========================================== КОНЕЦ РЕЗЕРВИРОВАНИЯ СМЕТЫ ==============================================
                
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
     * Снатие резерва со сметы
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

        if ($estimate->is_saled == 0) {
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

                Log::channel('documents')
                    ->info('========================================== НАЧАЛО ОТМЕНЫ РЕЗЕРВИРОВАНИЯ СМЕТЫ, ID: ' . $estimate->id . ' ==============================================');

                $result = [];
                foreach ($estimate->goods_items as $item) {
//                    $item->load('document');
                    $result[] = $this->unreserve($item);
                }

                Log::channel('documents')
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

}
