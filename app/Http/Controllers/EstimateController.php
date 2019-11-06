<?php

namespace App\Http\Controllers;

// Модели
use App\Http\Controllers\Traits\Offable;
use App\Http\Controllers\Traits\UserControllerTrait;
use Illuminate\Support\Facades\Log;
use App\Estimate;
use App\Http\Controllers\Traits\LeadControllerTrait;
use App\Lead;
use App\Stock;
use Illuminate\Http\Request;

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
            'client.clientable',
            'items',
            'author'
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        // ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->where('draft', false)
        ->whereNotNull('client_id')
        ->booklistFilter($request)  // Фильтр по спискам
        ->filter($request, 'client_id')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($estimates);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            // 'client',               // Клиенты
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------


        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('estimates.index', compact('estimates', 'page_info', 'filter'));
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

    public function saling(Request $request, $id)
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
            ->findOrFail($id);

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

            $stock_general = Stock::findOrFail($request->stock_id);
            $estimate->update([
                'stock_id' => $request->stock_id
            ]);

            Log::channel('documents')
                ->info('========================================== НАЧАЛО ПРОДАЖИ СМЕТЫ, ID: ' . $estimate->id . ' ==============================================');

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
                    'is_saled' => true,
                ];
            }
            $estimate->update($data);

            Log::channel('documents')
                ->info('Продана смета c id: ' . $estimate->id);
            Log::channel('documents')
                ->info('========================================== КОНЕЦ ПРОДАЖИ СМЕТЫ ==============================================
                
                ');

            return redirect()->route('leads.index');
        } else {
            abort(403, 'Смета пуста');
        }
    }

    public function ajax_create(Request $request)
    {

        $lead = Lead::findOrFail($request->lead_id);

        // TODO - 24.10.19 - Скидка должна браться из ценовой политики

        $estimate = Estimate::firstOrNew([
            'lead_id' => $lead->id,
            'filial_id' => $lead->filial_id,
            'client_id' => $lead->client_id,
            'stock_id' => $request->stock_id,
            'discount_percent' => 10,
        ]);

        // dd($estimate);

        if (!$estimate->id) {
            $estimate->save();
            return response()->json($estimate->id);
        }
    }

    public function ajax_update(Request $request)
    {

        $result = Estimate::findOrFail($request->estimate_id)
            ->update([
                'stock_id' => $request->stock_id
            ]);
        // dd($result);

        return response()->json($result);
    }

}
