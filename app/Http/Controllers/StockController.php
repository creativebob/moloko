<?php

namespace App\Http\Controllers;

// Модели
use App\Stock;
use App\Room;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\System\StockRequest;

class StockController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Stock $stock)
    {
        $this->middleware('auth');
        $this->stock = $stock;
        $this->class = Stock::class;
        $this->model = 'App\Stock';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = true;
    }

    public function index(Request $request)
    {


        // dd($request->session()->get('access')['settings']);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);
        if (($filter_url != null)&&($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entity_alias));
            return Redirect($filter_url);
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -----------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------------

        $stocks = Stock::with([
            'author',
            'company',
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->systemItem($answer)
        // ->booklistFilter($request)
        // ->filter($request, 'author')
        // ->filter($request, 'company')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($stocks);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
            'company',              // Компания
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('system.pages.stocks.index', compact('stocks', 'pageInfo', 'filter'));
    }


    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(__FUNCTION__, $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('rooms', false, 'index');

        // Главный запрос
        $rooms_categories_count = Room::with([
            'article'
        ])
        ->where('archive', false)
        ->whereHas('article', function ($q) {
            $q->where('draft', false);
        })
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->count();

        if($rooms_categories_count == 0){

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!";
            $ajax_error['text'] = "Для начала необходимо создать помещения. А уже потом будем добавлять склады. Ок?";
            $ajax_error['link'] = "/admin/rooms";
            $ajax_error['title_link'] = "Идем в раздел помещений";

            return view('ajax_error', compact('ajax_error'));
        }

        return view('system.pages.stocks.create', [
            'stock' => Stock::make(),
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }


    public function store(StockRequest $request)
    {

        // dd($request);
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $data = $request->input();
        $stock = Stock::create($data);

        if ($stock) {
            return redirect()->route('stocks.index');
        } else {
            abort(403, 'Ошибка записи склада');
        }
    }


    public function show(Request $request, $id)
    {
        //
    }


    public function edit(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $stock = Stock::moderatorLimit($answer)
        ->find($id);
        // dd($stock);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $stock);

        return view('system.pages.stocks.edit', [
            'stock' => $stock,
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }

    public function update(StockRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $stock = Stock::moderatorLimit($answer)
        ->find($id);
        // dd($stock);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $stock);

        $data = $request->input();
        $result = $stock->update($data);

        if ($result) {
            return redirect()->route('stocks.index');
        } else {
            abort(403, 'Ошибка обновления');
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $stock = Stock::moderatorLimit($answer)
        ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $stock);

        $stock->delete();
        // dd($stock);

        if ($stock) {
            return redirect()->route('stocks.index');
        } else {
            abort(403, 'Ошибка при удалении альбома');
        }
    }

    public function count()
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $stocks_count = Stock::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->systemItem($answer)
            ->count();

        return response()->json($stocks_count);
    }
}
