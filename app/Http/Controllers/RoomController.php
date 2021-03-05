<?php

namespace App\Http\Controllers;

use App\Http\Requests\System\RoomStoreRequest;
use App\Http\Requests\System\RoomUpdateRequest;
use App\Room;
use App\RoomsCategory;
use App\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Traits\Articlable;

class RoomController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Room $room)
    {
        $this->middleware('auth');
        $this->room = $room;
        $this->class = Room::class;
        $this->model = 'App\Room';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    use Articlable;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);
        if (($filter_url != null)&&($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entity_alias));
            return Redirect($filter_url);
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------

        $columns = [
            'id',
            'article_id',
            'category_id',

            'author_id',
            'company_id',
            'display',
            'system'
        ];

        $rooms = Room::with([
            'author',
            'company',
            'article' => function ($q) {
                $q->with([
                    'group',
                    'photo'
                ]);
            },
            'category'
//            => function ($q) {
//                $q->select([
//                    'id',
//                    'name'
//                ]);
//            }
            ,
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->booklistFilter($request)
            ->filter()

//        ->filter($request, 'author_id')
        // ->filter($request, 'rooms_category_id', 'article.product')
        // ->filter($request, 'rooms_product_id', 'article')

        ->where('archive', false)
//        ->select($columns)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($rooms);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
            // 'rooms_category',    // Категория услуги
            // 'rooms_product',     // Группа услуги
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);


        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('products.articles.common.index.index', [
            'items' => $rooms,
            'pageInfo' => $pageInfo,
            'class' => $this->class,
            'entity' => $this->entity_alias,
            'category_entity' => 'rooms_categories',
            'filter' => $filter,
        ]);
    }

    public function archives(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod('index'), $this->class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);
        if (($filter_url != null)&&($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entity_alias));
            return Redirect($filter_url);
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------

        $columns = [
            'id',
            'article_id',
            'category_id',

            'author_id',
            'company_id',
            'display',
            'system'
        ];

        $rooms = Room::with([
            'author',
            'company',
            'article' => function ($q) {
                $q->with([
                    'group',
                    'photo'
                ]);
            },
            'category'
//            => function ($q) {
//                $q->select([
//                    'id',
//                    'name'
//                ]);
//            }
            ,
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->booklistFilter($request)
            ->filter()

//            ->filter($request, 'author_id')
            // ->filter($request, 'rooms_category_id', 'article.product')
            // ->filter($request, 'rooms_product_id', 'article')

            ->where('archive', true)
//        ->select($columns)
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc')
            ->paginate(30);
        // dd($rooms);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
            // 'rooms_category',    // Категория услуги
            // 'rooms_product',     // Группа услуги
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);


        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('products.articles.common.index.index', [
            'items' => $rooms,
            'pageInfo' => $pageInfo,
            'class' => $this->class,
            'entity' => $this->entity_alias,
            'category_entity' => 'rooms_categories',
            'filter' => $filter,
        ]);
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('rooms_categories', false, 'index');

        // Главный запрос
        $rooms_categories = RoomsCategory::withCount('manufacturers')
        ->with('manufacturers')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->orderBy('sort', 'asc')
        ->get();

        if($rooms_categories->count() == 0){

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!";
            $ajax_error['text'] = "Для начала необходимо создать категории помещений. А уже потом будем добавлять помещения. Ок?";
            $ajax_error['link'] = "/admin/rooms_categories";
            $ajax_error['title_link'] = "Идем в раздел категорий";

            return view('ajax_error', compact('ajax_error'));
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('manufacturers', false, 'index');

        $manufacturers_count = Manufacturer::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->systemItem($answer)
        ->count();

        // Если нет производителей
        if ($manufacturers_count == 0){

            // Описание ошибки
            // $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
            $ajax_error['text'] = "Для начала необходимо добавить производителей. А уже потом будем добавлять сырьё. Ок?";
            $ajax_error['link'] = "/admin/manufacturers/create"; // Ссылка на кнопке
            $ajax_error['title_link'] = "Идем в раздел производителей"; // Текст на кнопке

            return view('ajax_error', compact('ajax_error'));
        }

        return view('products.articles.common.create.create', [
            'item' => new $this->class,
            'title' => 'Добавление помещения',
            'entity' => $this->entity_alias,
            'category_entity' => 'rooms_categories',
            'units_category_default' => 6,
            'unit_default' => 32,
        ]);
    }

    public function store(RoomStoreRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        Log::channel('operations')
        ->info('========================================== НАЧИНАЕМ ЗАПИСЬ ПОМЕЩЕНИЯ ==============================================');

        $rooms_category = RoomsCategory::find($request->category_id);
        // dd($goods_category->load('groups'));
        $article = $this->storeArticle($request, $rooms_category);

        if ($article) {

            $data = $request->input();
            $data['article_id'] = $article->id;
            $data['price_unit_category_id'] = $data['units_category_id'];
            $data['price_unit_id'] = $data['unit_id'];

            $room = (new Room())->create($data);

            if ($room) {

                // Пишем куки состояния
                // $mass = [
                //     'goods_category' => $goods_category_id,
                // ];
                // Cookie::queue('conditions_goods_category', $goods_category_id, 1440);

                Log::channel('operations')
                ->info('Записали помещение с id: ' . $room->id);
                Log::channel('operations')
                ->info('Автор: ' . $room->author->name . ' id: ' . $room->author_id .  ', компания: ' . $room->company->name . ', id: ' .$room->company_id);
                Log::channel('operations')
                ->info('========================================== КОНЕЦ ЗАПИСИ ТОВАРА ==============================================

                    ');

                // dd($request->quickly);
                if ($request->quickly == 1) {
                    return redirect()->route('rooms.index');
                } else {
                    return redirect()->route('rooms.edit', $room->id);
                }
            } else {
                abort(403, 'Ошибка записи сырья');
            }
        } else {
            abort(403, 'Ошибка записи информации сырья');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Главный запрос
        $room = Room::moderatorLimit($answer)
        ->find($id);
        // dd($room);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $room);

        $room->load([
            'article' => function ($q) {
                $q->with([
                    'unit'
                ]);
            },
            'location'
        ]);
        $article = $room->article;
        // dd($article);

        // Получаем настройки по умолчанию
        $settings = getPhotoSettings($this->entity_alias);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);
        // dd($pageInfo);

        return view('products.articles.common.edit.edit', [
            'title' => 'Редактировать помещение',
            'item' => $room,
            'article' => $article,
            'pageInfo' => $pageInfo,
            'settings' => $settings,
            'entity' => $this->entity_alias,
            'category_entity' => 'rooms_categories',
            'categories_select_name' => 'rooms_category_id',
        ]);
    }

    public function update(RoomUpdateRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $room = Room::with('article')
        ->moderatorLimit($answer)
        ->find($id);
        // dd($room);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $room);

        $article = $room->article;
        // dd($article);

        $result = $this->updateArticle($request, $room);
        // Если результат не массив с ошибками, значит все прошло удачно
        if (!is_array($result)) {

            // ПЕРЕНОС ГРУППЫ ТОВАРА В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ
            $this->changeCategory($request, $room);

            // Метрики
            if ($request->has('metrics')) {
                // dd($request);

                $metrics_insert = [];
                foreach ($request->metrics as $metric_id => $value) {
                    if (is_array($value)) {
                        $metrics_insert[$metric_id]['value'] = implode(',', $value);
                    } else {
//                        if (!is_null($value)) {
                        $metrics_insert[$metric_id]['value'] = $value;
//                        }
                    }
                }
                $room->metrics()->syncWithoutDetaching($metrics_insert);
            }

            $room->area = $request->area;
            // dd($request);
            $location_id = create_location($request);
            // dd($location_id);
            $room->location_id = $location_id;

            $room->serial = $request->serial;
            $room->save();


            // Если ли есть
            if ($request->cookie('backlink') != null) {
                $backlink = Cookie::get('backlink');
                return Redirect($backlink);
            }

            if ($room->archive) {
                return redirect()->route('rooms.archives');
            } else {
                return redirect()->route('rooms.index');
            }
        } else {
            return back()
            ->withErrors($result)
            ->withInput();
        }
    }

    public function destroy($id)
    {
        //
    }

    public function archive(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, 'delete');

        // ГЛАВНЫЙ ЗАПРОС:
        $room = Room::moderatorLimit($answer)
        ->find($id);

        // Подключение политики
        $this->authorize(getmethod('destroy'), $room);

        if ($room) {

            $room->archive = true;

            // Скрываем бога
            $room->editor_id = hideGod($request->user());
            $room->save();

            if ($room) {
                return redirect()->route('rooms.index');
            } else {
                abort(403, 'Ошибка при архивации помещения');
            }
        } else {
            abort(403, 'Помещение не найдено');
        }
    }
}
