<?php

namespace App\Http\Controllers;

// Модели
use App\Equipment;
use App\Article;
use App\EquipmentsCategory;
use App\Manufacturer;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\EquipmentRequest;
use App\Http\Requests\ArticleStoreRequest;

// Куки
use Illuminate\Support\Facades\Cookie;

// Трейты
use App\Http\Controllers\Traits\Articles\ArticleTrait;

use Illuminate\Support\Facades\Log;

class EquipmentController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Equipment $equipment)
    {
        $this->middleware('auth');
        $this->equipment = $equipment;
        $this->class = Equipment::class;
        $this->model = 'App\Equipment';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    use ArticleTrait;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Включение контроля активного фильтра
        // $filter_url = autoFilter($request, $this->entity_alias);
        // if (($filter_url != null)&&($request->filter != 'active')) {
        //     Cookie::queue(Cookie::forget('filter_' . $this->entity_alias));
        //     return Redirect($filter_url);
        // }

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

        $equipments = Equipment::with([
            'author',
            'company',
            'article' => function ($q) {
                $q->with([
                    'group',
                    'photo'
                ]);
            },
            'category:id,name',
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        ->where('archive', false)
        ->select($columns)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($equipments);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
            // 'equipments_category',    // Категория услуги
            // 'equipments_product',     // Группа услуги
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);
        // dd($filter);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('products.articles.common.index.index', [
            'items' => $equipments,
            'page_info' => $page_info,
            'class' => $this->class,
            'entity' => $this->entity_alias,
            'category_entity' => 'equipments_categories',
            'filter' => $filter,
        ]);
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('equipments_categories', false, 'index');

        // Главный запрос
        $equipments_categories = EquipmentsCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->orderBy('sort', 'asc')
        ->get();

        if($equipments_categories->count() == 0){

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!";
            $ajax_error['text'] = "Для начала необходимо создать категории оборудования. А уже потом будем добавлять оборудование. Ок?";
            $ajax_error['link'] = "/admin/equipments_categories";
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

        return view('products.articles.equipments.create', [
            'item' => new $this->class,
            'title' => 'Добавление оборудования',
            'entity' => $this->entity_alias,
            'category_entity' => 'equipments_categories',
        ]);
    }

    public function store(ArticleStoreRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        Log::channel('operations')
        ->info('========================================== НАЧИНАЕМ ЗАПИСЬ ОБОРУДОВАНИЯ ==============================================');

        $equipments_category = EquipmentsCategory::findOrFail($request->category_id);
        // dd($goods_category->load('groups'));
        $article = $this->storeArticle($request, $equipments_category);

//        Выводим артикул из черновика для оборудования
        $article->draft = false;
        $article->save();

        if ($article) {

            $data = $request->input();
            $data['article_id'] = $article->id;
            $equipment = (new Equipment())->create($data);

            if ($equipment) {

                // Пишем куки состояния
                // $mass = [
                //     'goods_category' => $goods_category_id,
                // ];
                // Cookie::queue('conditions_goods_category', $goods_category_id, 1440);

                Log::channel('operations')
                ->info('Записали оборудование c id: ' . $equipment->id);
                Log::channel('operations')
                ->info('Автор: ' . $equipment->author->name . ' id: ' . $equipment->author_id .  ', компания: ' . $equipment->company->name . ', id: ' .$equipment->company_id);
                Log::channel('operations')
                ->info('========================================== КОНЕЦ ЗАПИСИ ОБОРУДОВАНИЯ ==============================================

                    ');

                // dd($request->quickly);
                if ($request->quickly == 1) {
                    return redirect()->route('equipments.index');
                } else {
                    return redirect()->route('equipments.edit', ['id' => $equipment->id]);
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
        $equipment = Equipment::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($equipment);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $equipment);

        $article = $equipment->article;
        // dd($article);

        // Получаем настройки по умолчанию
        $settings = getSettings($this->entity_alias);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);
        // dd($page_info);

        return view('products.articles.common.edit.edit', [
            'title' => 'Редактировать оборудование',
            'item' => $equipment,
            'article' => $article,
            'page_info' => $page_info,
            'settings' => $settings,
            'entity' => $this->entity_alias,
            'category_entity' => 'equipments_categories',
            'categories_select_name' => 'equipments_category_id',
        ]);
    }

    public function update(ArticleStoreRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $equipment = Equipment::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($equipment);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $equipment);

        $article = $equipment->article;
        // dd($article);

        $result = $this->updateArticle($request, $equipment);
        // Если результат не массив с ошибками, значит все прошло удачно
        if (!is_array($result)) {

            // ПЕРЕНОС ГРУППЫ ТОВАРА В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ
            $this->changeCategory($request, $equipment);

            $equipment->serial = $request->serial;
            $equipment->display = $request->display;
            $equipment->system = $request->system;
            $equipment->save();


            // Если ли есть
            if ($request->cookie('backlink') != null) {
                $backlink = Cookie::get('backlink');
                return Redirect($backlink);
            }

            return redirect()->route('equipments.index');
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
        $equipment = Equipment::with([
            'compositions.goods',
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod('destroy'), $equipment);

        if ($equipment) {

            $equipment->archive = true;

            // Скрываем бога
            $equipment->editor_id = hideGod($request->user());
            $equipment->save();

            if ($equipment) {
                return redirect()->route('equipments.index');
            } else {
                abort(403, 'Ошибка при архивации');
            }
        } else {
            abort(403, 'Запись не найдена');
        }
    }
}
