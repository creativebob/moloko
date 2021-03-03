<?php

namespace App\Http\Controllers;

// Модели
use App\Tool;
use App\Article;
use App\ToolsCategory;
use App\Manufacturer;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\System\ToolRequest;
use App\Http\Requests\System\ArticleStoreRequest;

// Куки
use Illuminate\Support\Facades\Cookie;

// Трейты
use App\Http\Controllers\Traits\Articlable;

use Illuminate\Support\Facades\Log;

class ToolController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Tool $tool)
    {
        $this->middleware('auth');
        $this->tool = $tool;
        $this->class = Tool::class;
        $this->model = 'App\Tool';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    use Articlable;

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

        $tools = Tool::with([
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
//        ->select($columns)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($tools);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
            // 'tools_category',    // Категория услуги
            // 'tools_product',     // Группа услуги
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);
        // dd($filter);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('products.articles.common.index.index', [
            'items' => $tools,
            'pageInfo' => $pageInfo,
            'class' => $this->class,
            'entity' => $this->entity_alias,
            'category_entity' => 'tools_categories',
            'filter' => $filter,
        ]);
    }

    public function archives(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod('index'), $this->class);

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

        $tools = Tool::with([
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
            ->where('archive', true)
//        ->select($columns)
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc')
            ->paginate(30);
        // dd($tools);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
            // 'tools_category',    // Категория услуги
            // 'tools_product',     // Группа услуги
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);
        // dd($filter);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('products.articles.common.index.index', [
            'items' => $tools,
            'pageInfo' => $pageInfo,
            'class' => $this->class,
            'entity' => $this->entity_alias,
            'category_entity' => 'tools_categories',
            'filter' => $filter,
        ]);
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('tools_categories', false, 'index');

        // Главный запрос
        $tools_categories = ToolsCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->orderBy('sort', 'asc')
        ->get();

        if($tools_categories->count() == 0){

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!";
            $ajax_error['text'] = "Для начала необходимо создать категории оборудования. А уже потом будем добавлять оборудование. Ок?";
            $ajax_error['link'] = "/admin/tools_categories";
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
            'title' => 'Добавление оборудования',
            'entity' => $this->entity_alias,
            'category_entity' => 'tools_categories',
            'units_category_default' => 6,
            'unit_default' => 32,
        ]);
    }

    public function store(ArticleStoreRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        Log::channel('operations')
        ->info('========================================== НАЧИНАЕМ ЗАПИСЬ ИНСТРУМЕНТА ==============================================');

        $tools_category = ToolsCategory::find($request->category_id);
        // dd($goods_category->load('groups'));
        $article = $this->storeArticle($request, $tools_category);

//        Выводим артикул из черновика для оборудования
        $article->draft = false;
        $article->save();

        if ($article) {

            $data = $request->input();
            $data['article_id'] = $article->id;
            $tool = Tool::create($data);

            if ($tool) {

                // Пишем куки состояния
                // $mass = [
                //     'goods_category' => $goods_category_id,
                // ];
                // Cookie::queue('conditions_goods_category', $goods_category_id, 1440);

                Log::channel('operations')
                ->info('Записали оборудование c id: ' . $tool->id);
                Log::channel('operations')
                ->info('Автор: ' . $tool->author->name . ' id: ' . $tool->author_id .  ', компания: ' . $tool->company->name . ', id: ' .$tool->company_id);
                Log::channel('operations')
                ->info('========================================== КОНЕЦ ЗАПИСИ ИНСТРУМЕНТА ==============================================

                    ');

                // dd($request->quickly);
                if ($request->quickly == 1) {
                    return redirect()->route('tools.index');
                } else {
                    return redirect()->route('tools.edit', $tool->id);
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
        $tool = Tool::moderatorLimit($answer)
        ->find($id);
        // dd($tool);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $tool);

        $tool->load([
            'article' => function ($q) {
                $q->with([
                    'unit',
                    'parts' => function ($q) {
                        $q->with([
                            'tool' => function ($q) {
                                $q->with([
                                    'category',
                                    'unit_for_composition',
                                    'unit_portion',
                                    'costs',
                                    'article.unit',
                                ]) ;
                            },
                        ]);
                    }
                ]);
            }
        ]);
        $article = $tool->article;
        // dd($article);

        // Получаем настройки по умолчанию
        $settings = getPhotoSettings($this->entity_alias);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);
        // dd($pageInfo);

        return view('products.articles.common.edit.edit', [
            'title' => 'Редактировать инструмент',
            'item' => $tool,
            'article' => $article,
            'pageInfo' => $pageInfo,
            'settings' => $settings,
            'entity' => $this->entity_alias,
            'category_entity' => 'tools_categories',
            'categories_select_name' => 'tools_category_id',
        ]);
    }

    public function update(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $tool = Tool::moderatorLimit($answer)
        ->find($id);
        // dd($tool);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $tool);

        $article = $tool->article;
        // dd($article);

        $result = $this->updateArticle($request, $tool);
        // Если результат не массив с ошибками, значит все прошло удачно
        if (!is_array($result)) {

            // ПЕРЕНОС ГРУППЫ ТОВАРА В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ
            $this->changeCategory($request, $tool);

            $data = $request->input();
            $tool->update($data);

            // Если ли есть
            if ($request->cookie('backlink') != null) {
                $backlink = Cookie::get('backlink');
                return Redirect($backlink);
            }

            if ($tool->archive) {
                return redirect()->route('tools.archives');
            } else {
                return redirect()->route('tools.index');
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
        $tool = Tool::with([
            'compositions.goods',
        ])
        ->moderatorLimit($answer)
        ->find($id);

        // Подключение политики
        $this->authorize(getmethod('destroy'), $tool);

        if ($tool) {

            $tool->archive = true;

            // Скрываем бога
            $tool->editor_id = hideGod($request->user());
            $tool->save();

            if ($tool) {
                return redirect()->route('tools.index');
            } else {
                abort(403, 'Ошибка при архивации');
            }
        } else {
            abort(403, 'Запись не найдена');
        }
    }
}
