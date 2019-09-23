<?php

namespace App\Http\Controllers;

use App\Container;
use App\ContainersCategory;
use App\Http\Requests\ContainerStoreRequest;
use App\Http\Requests\ContainerUpdateRequest;
use App\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Traits\Articlable;

class ContainerController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Container $container)
    {
        $this->middleware('auth');
        $this->container = $container;
        $this->class = Container::class;
        $this->model = 'App\Container';
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
            'price_unit_id',
            'price_unit_category_id',

            'portion_goods_status',
            'portion_goods_name',
            'portion_goods_abbreviation',
            'unit_portion_goods_id',
            'portion_goods_count',

            'author_id',
            'company_id',
            'display',
            'system',
            'unit_for_composition_id'
        ];


        $containers = Container::with([
            'author',
            'company',
            'compositions.goods',
            'article' => function ($q) {
                $q->with([
                    'group',
                    'photo',
                    'unit',
                    'unit_weight',
                    'unit_volume'
                ]);
            },
            'category' => function ($q) {
                $q->select([
                    'id',
                    'name'
                ]);
            },
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->booklistFilter($request)
            ->filter($request, 'author_id')

            ->whereHas('article', function($q) use ($request){
                $q->filter($request, 'articles_group_id');
            })

            ->filter($request, 'category_id')

            ->where('archive', false)
            ->select($columns)
            ->orderBy('moderation', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(30);
        // dd($containers);


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
            'containers_category',  // Категория упаковки
            'articles_group',       // Группа артикула
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('products.articles.common.index.index', [
            'items' => $containers,
            'page_info' => $page_info,
            'class' => $this->class,
            'entity' => $this->entity_alias,
            'category_entity' => 'containers_categories',
            'filter' => $filter,
        ]);
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('containers_categories', false, 'index');

        // Главный запрос
        $containers_categories = ContainersCategory::withCount('manufacturers')
            ->with('manufacturers')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->orderBy('sort', 'asc')
            ->get();

        if($containers_categories->count() == 0){

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!";
            $ajax_error['text'] = "Для начала необходимо создать категории сырья. А уже потом будем добавлять сырье. Ок?";
            $ajax_error['link'] = "/admin/containers_categories";
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
            'title' => 'Добавление упаковки',
            'entity' => $this->entity_alias,
            'category_entity' => 'containers_categories',
            'units_category_default' => 6,
            'unit_default' => 32,
        ]);
    }

    public function store(ContainerStoreRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        Log::channel('operations')
            ->info('========================================== НАЧИНАЕМ ЗАПИСЬ УПАКОВКИ ==============================================');

        $containers_category = ContainersCategory::findOrFail($request->category_id);
        // dd($goods_category->load('groups'));
        $article = $this->storeArticle($request, $containers_category);

        if ($article) {

            $data = $request->input();
            $data['article_id'] = $article->id;
            $data['price_unit_category_id'] = $data['units_category_id'];
            $data['price_unit_id'] = $data['unit_id'];

            $container = (new Container())->create($data);

            if ($container) {

                // Пишем куки состояния
                // $mass = [
                //     'goods_category' => $goods_category_id,
                // ];
                // Cookie::queue('conditions_goods_category', $goods_category_id, 1440);

                Log::channel('operations')
                    ->info('Записали сырье c id: ' . $container->id);
                Log::channel('operations')
                    ->info('Автор: ' . $container->author->name . ' id: ' . $container->author_id .  ', компания: ' . $container->company->name . ', id: ' .$container->company_id);
                Log::channel('operations')
                    ->info('========================================== КОНЕЦ ЗАПИСИ УПАКОВКИ ==============================================

                    ');

                // dd($request->quickly);
                if ($request->quickly == 1) {
                    return redirect()->route('containers.index');
                } else {
                    return redirect()->route('containers.edit', ['id' => $container->id]);
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
        $container = Container::moderatorLimit($answer)
            ->findOrFail($id);
        // dd($container);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $container);

        $container->load([
            'article' => function ($q) {
                $q->with([
                    'unit'
                ]);
            }
        ]);
        $article = $container->article;
        // dd($article);

        // Получаем настройки по умолчанию
//        $dropzone = getSettings($this->entity_alias);
//        $dropzone['id'] = $article->id;
//        $dropzone['entity'] = $article->getTable();
//        dd($dropzone);

        // Получаем настройки по умолчанию
        $settings = getSettings($this->entity_alias);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);
        // dd($page_info);

        return view('products.articles.common.edit.edit', [
            'title' => 'Редактировать упаковку',
            'item' => $container,
            'article' => $article,
            'page_info' => $page_info,
            'settings' => $settings,
//            'dropzone' => json_encode($dropzone),
            'entity' => $this->entity_alias,
            'category_entity' => 'containers_categories',
            'categories_select_name' => 'containers_category_id',
            'container' => $container,
        ]);
    }

    public function update(ContainerUpdateRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $container = Container::moderatorLimit($answer)
            ->findOrFail($id);
        // dd($container);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $container);

        $article = $container->article;
        // dd($article);

        $result = $this->updateArticle($request, $container);
        // Если результат не массив с ошибками, значит все прошло удачно

        if (!is_array($result)) {

            // ПЕРЕНОС ГРУППЫ ТОВАРА В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ
            $this->changeCategory($request, $container);

            $container->unit_for_composition_id = $request->unit_for_composition_id;

            $container->portion_goods_status = $request->portion_goods_status ?? 0;
            $container->portion_goods_abbreviation = $request->portion_goods_abbreviation;
            // $container->portion_goods_name = $request->portion_goods_name;
            $container->unit_portion_goods_id = $request->unit_portion_goods_id;
            $container->portion_goods_count = $request->portion_goods_count;

            $container->price_unit_id = $request->price_unit_id;
            $container->price_unit_category_id = $request->price_unit_category_id;

            $container->serial = $request->serial;
            $container->display = $request->display;
            $container->system = $request->system;

            $container->save();


            // Если ли есть
            if ($request->cookie('backlink') != null) {
                $backlink = Cookie::get('backlink');
                return Redirect($backlink);
            }

            return redirect()->route('containers.index');
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
        $container = Container::with([
            'compositions.goods',
        ])
            ->moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod('destroy'), $container);

        if ($container) {

            $container->archive = true;

            // Скрываем бога
            $container->editor_id = hideGod($request->user());
            $container->save();

            if ($container) {
                return redirect()->route('containers.index');
            } else {
                abort(403, 'Ошибка при архивации сырья');
            }
        } else {
            abort(403, 'Сырьё не найдено');
        }
    }

    public function replicate(Request $request, $id)
    {
        $container = Container::findOrFail($id);

        $container->load('article');
        $article = $container->article;
        $new_article = $this->replicateArticle($request, $container);

        $new_container = $container->replicate();
        $new_container->article_id = $new_article->id;
        $new_container->save();

        $container->load('metrics');
        if ($container->metrics->isNotEmpty()) {
            $metrics_insert = [];
            foreach ($container->metrics as $metric) {
                $metrics_insert[$metric->id]['value'] = $metric->pivot->value;
            }
            $res = $new_container->metrics()->attach($metrics_insert);
        }

        if($article->kit) {
            $article->load('containers');
            if ($article->containers->isNotEmpty()) {
                $containers_insert = [];
                foreach ($article->containers as $container) {
                    $containers_insert[$container->id]['value'] = $container->pivot->value;
                }
                $res = $new_article->raws()->attach($containers_insert);
            }
        }

        return redirect()->route('containers.index');
    }

    // --------------------------------------------- Ajax -------------------------------------------------

    public function ajax_get_container(Request $request)
    {
        $container = Container::with([
            'article.group.unit',
            'category'
        ])
            ->find($request->id);

        return view('products.articles.goods.containers.container_input', compact('container'));
    }

    // Добавляем состав
    public function ajax_get_category_container(Request $request)
    {

        $container = Container::with([
            'article.group.unit',
            'category'
        ])
            ->findOrFail($request->id);

        return view('products.articles_categories.goods_categories.containers.container_tr', compact('container'));
    }
}
