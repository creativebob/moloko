<?php

namespace App\Http\Controllers;

// Модели
use App\Consignment;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\ConsignmentRequest;

// Карбон
use Carbon\Carbon;

class ConsignmentController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Consignment $consignment)
    {
        $this->middleware('auth');
        $this->consignment = $consignment;
        $this->class = Consignment::class;
        $this->model = 'App\Consignment';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $consignments = Consignment::with('author')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        // ->whereNull('draft')
        ->booklistFilter($request)
        ->filter($request, 'supplier_id')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($consignments);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'supplier',             // Поставщики
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('consignments.index', compact('consignments', 'page_info', 'filter'));
    }


    public function create()
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('consignments.create', [
            'consignment' => new $this->class,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }


    public function store(ConsignmentRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $consignment = new Consignment;

        $consignment->supplier_id = $request->supplier_id;

        $consignment->description = $request->description;
        $consignment->number = $request->number;
        $consignment->amount = $request->amount;

        // Дата приема
        $consignment->receipt_date = $request->has('draft') ? null : Carbon::parse($request->receipt_date)->format('Y-m-d');

        $consignment->draft = $request->draft;

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $consignment->company_id = $user->company_id;
        $consignment->author_id = hideGod($user);

        $consignment->save();

        return redirect()->route('consignments.index');
    }

    public function show(Request $request, $id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $consignment = Consignment::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        $this->authorize(getmethod(__FUNCTION__), $consignment);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('consignments.edit', compact('consignment', 'page_info'));
    }


    public function update(ConsignmentRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $consignment = Consignment::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $consignment);

        $consignment->supplier_id = $request->supplier_id;

        $consignment->description = $request->description;
        $consignment->number = $request->number;
        $consignment->amount = $request->amount;

        // Дата приема
        $consignment->receipt_date = $request->has('draft') ? null : Carbon::parse($request->receipt_date)->format('Y-m-d');

        $consignment->draft = $request->draft;

        $consignment->editor_id = hideGod($request->user());
        $consignment->save();

        return redirect()->route('consignments.index');
    }


    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $consignment = Consignment::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $consignment);

        $consignment->editor_id = hideGod($request->user());
        $consignment->save();

        $consignment->delete();

        if ($consignment) {
            return redirect()->route('consignments.index');
        } else {
            abort(403, 'Ошибка при удалении товарной накладной');
        }

    }
}
