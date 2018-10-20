<?php

namespace App\Http\Controllers;

// Модели
use App\Goods;
use App\GoodsCategory;
use App\GoodsMode;
use App\GoodsProduct;
use App\GoodsArticle;
use App\Album;
use App\AlbumEntity;
use App\Photo;
use App\UnitsCategory;
use App\Unit;
use App\Catalog;
use App\RawsCategory;
use App\EntitySetting;
use App\ArticleValue;

// Политика
use App\Policies\GoodsPolicy;

// Куки
use Illuminate\Support\Facades\Cookie;

// Транслитерация
use Transliterate;


use Illuminate\Http\Request;

class GoodsController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'goods';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize('index', Goods::class);

        // Включение контроля активного фильтра 
        $filter_url = autoFilter($request, $this->entity_name);

        if (($filter_url != null)&&($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entity_name));
            return Redirect($filter_url);
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------------

        $goods = Goods::with('author', 'company', 'goods_article.goods_product.goods_category', 'catalogs.site')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        ->filter($request, 'goods_category_id', 'goods_article.goods_product')
        ->filter($request, 'goods_product_id', 'goods_article')
        ->whereNull('archive')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            'author',               // Автор записи
            'goods_category',       // Категория товара
            'goods_product',     // Группа продукта
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('goods.index', compact('goods', 'page_info', 'filter'));
    }

    public function search($text_fragment)
    {

        // Подключение политики
        $this->authorize('index', Goods::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // --------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------------

        $result_search = Goods::with('author', 'company', 'goods_article.goods_product.goods_category')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->where('name', 'LIKE', '%'.$text_fragment.'%')
        ->whereNull('archive')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        if ($result_search->count()) {

            $entity_name = $this->entity_name;

            return view('includes.search', compact('result_search', 'entity_name'));
        } else {

            return view('includes.search');
        }
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Goods::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_goods_categories = operator_right('goods_categories', false, 'index');

        // Главный запрос
        $goods_categories = GoodsCategory::withCount('goods_products')
        ->with('goods_products')
        ->moderatorLimit($answer_goods_categories)
        ->companiesLimit($answer_goods_categories)
        ->authors($answer_goods_categories)
        ->systemItem($answer_goods_categories) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get();

        if($goods_categories->count() < 1){

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
            $ajax_error['text'] = "Для начала необходимо создать категории товаров. А уже потом будем добавлять товары. Ок?";
            $ajax_error['link'] = "/admin/goods_categories"; // Ссылка на кнопке
            $ajax_error['title_link'] = "Идем в раздел категорий"; // Текст на кнопке

            return view('ajax_error', compact('ajax_error'));
        }

        $goods_products_count = $goods_categories[0]->goods_products_count;
        $parent_id = null;

        if ($request->cookie('conditions') != null) {

            $condition = Cookie::get('conditions');
            if(isset($condition['goods_category'])) {
                $goods_category_id = $condition['goods_category'];

                $goods_category = $goods_categories->find($goods_category_id);
                // dd($goods_category);

                $goods_products_count = $goods_category->goods_products_count;
                $parent_id = $goods_category_id;
                // dd($goods_products_count);
            }
        }

        // Пишем в куку страницу на которой находимся
        $backlink = url()->previous();
        Cookie::queue('backlink', $backlink, 1440);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $goods_categories_list = get_select_tree($goods_categories->keyBy('id')->toArray(), $parent_id, null, null);
        // echo $goods_categories_list;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_units_categories = operator_right('units_categories', false, 'index');

        // Главный запрос
        $units_categories_list = UnitsCategory::moderatorLimit($answer_units_categories)
        ->companiesLimit($answer_units_categories)
        ->authors($answer_units_categories)
        ->systemItem($answer_units_categories) // Фильтр по системным записям
        ->template($answer_units_categories)
        ->orderBy('sort', 'asc')
        ->get()
        ->pluck('name', 'id');
        // dd($units_categories_list);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_units = operator_right('units', false, 'index');

        // Главный запрос
        $units = Unit::where('units_category_id', 6) // Статичная категория с количеством для товаров
        ->orderBy('sort', 'asc')
        ->get();
        // dd($units);

        $unit_abbreviation = $units->where('id', 26)->first()->abbreviation;
        // dd($unit_abbreviation);

        $units_list = $units->pluck('name', 'id');
        // dd($units_list);

        return view('goods.create', compact('goods_categories_list', 'goods_products_count', 'units_categories_list', 'units_list', 'unit_abbreviation'));
    }

    public function store(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Goods::class);

        // dd($request);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        $name = $request->name;
        $goods_category_id = $request->goods_category_id;

        switch ($request->mode) {
            case 'mode-default':

            $goods_product = GoodsProduct::where(['name' => $name, 'goods_category_id' => $goods_category_id])->first();

            if ($goods_product) {
                $goods_product_id = $goods_product->id;
            } else {

                $goods_product = new GoodsProduct;
                $goods_product->name = $name;
                $goods_product->goods_category_id = $goods_category_id;
                $goods_product->unit_id = $request->unit_id;

                if (isset($request->status)) {
                    $goods_product->status = 'set';
                } else {
                    $goods_product->status = 'one';
                }
                
                // Модерация и системная запись
                $goods_product->system_item = $request->system_item;
                $goods_product->display = 1;
                $goods_product->company_id = $company_id;
                $goods_product->author_id = $user_id;
                $goods_product->save();

                if ($goods_product) {
                    $goods_product_id = $goods_product->id;
                } else {
                    abort(403, 'Ошибка записи группы товаров');
                }
            }
            break;
            
            case 'mode-add':
            $goods_product_name = $request->goods_product_name;
            $goods_product = GoodsProduct::where(['name' => $goods_product_name, 'goods_category_id' => $goods_category_id])->first();

            if ($goods_product) {
                $goods_product_id = $goods_product->id;
            } else {

                // Наполняем сущность данными
                $goods_product = new GoodsProduct;

                $goods_product->name = $request->goods_product_name;
                $goods_product->unit_id = $request->unit_id;

                if (isset($request->status)) {
                    $goods_product->status = 'set';
                } else {
                    $goods_product->status = 'one';
                }

                $goods_product->goods_category_id = $goods_category_id;

                // Модерация и системная запись
                $goods_product->system_item = $request->system_item;
                $goods_product->display = 1;
                $goods_product->company_id = $company_id;
                $goods_product->author_id = $user_id;
                $goods_product->save();

                if ($goods_product) {
                    $goods_product_id = $goods_product->id;
                } else {
                    abort(403, 'Ошибка записи группы услуг');
                }
            }
            break;

            case 'mode-select':
            $goods_product = GoodsProduct::findOrFail($request->goods_product_id);

            $service_product_name = $goods_product->name;
            $goods_product_id = $goods_product->id;
            break;
        }

        $goods_article = new GoodsArticle;
        $goods_article->goods_product_id = $goods_product_id;
        $goods_article->company_id = $company_id;
        $goods_article->author_id = $user_id;
        $goods_article->name = $name;
        $goods_article->save();

        if ($goods_article) {

            $cur_goods = new Goods;
            $cur_goods->price = $request->price;
            $cur_goods->company_id = $company_id;
            $cur_goods->author_id = $user_id;
            $cur_goods->draft = 1;
            $cur_goods->goods_article()->associate($goods_article);
            $cur_goods->save();

            if ($cur_goods) {

                // Пишем сессию
                $mass = [
                    'goods_category' => $goods_category_id,
                ];

                Cookie::queue('conditions', $mass, 1440);

                // dd($request->quickly);
                if ($request->quickly == 1) {
                    return redirect('/admin/goods');
                } else {
                    return redirect('/admin/goods/'.$cur_goods->id.'/edit'); 
                }

            } else {
                abort(403, 'Ошибка записи товара');
            }
        } else {
            abort(403, 'Ошибка записи информации услуги');
        } 
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // ГЛАВНЫЙ ЗАПРОС:
        $answer_goods = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $cur_goods = Goods::with(['goods_article.goods_product.goods_category' => function ($query) {
            $query->with(['metrics.property', 'compositions.raws_product.unit'])
            ->withCount('metrics', 'compositions');
        }, 'album.photos', 'company.manufacturers', 'metrics_values', 'raws_compositions_values.raws_product.unit'])
        ->withCount(['metrics_values', 'raws_compositions_values'])
        ->moderatorLimit($answer_goods)
        ->findOrFail($id);
        // dd($cur_goods);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $cur_goods);

        if ($cur_goods->draft == 1) {

            $cur_goods_compositions = [];
            foreach ($cur_goods->raws_compositions_values as $composition) {
                $cur_goods_compositions[] = $composition->id;
            }
        } else {

            $answer_goods_categories = operator_right('goods_categories', false, 'index');

            $goods_category = GoodsCategory::with(['goods_mode', 'metrics.unit', 'metrics.values', 'compositions.raws_product.unit'])
            ->withCount('metrics', 'compositions')
            ->moderatorLimit($answer_goods_categories)
            ->findOrFail($cur_goods->goods_article->goods_product->goods_category_id);

            $cur_goods_compositions = [];
            foreach ($goods_category->compositions as $composition) {
                $cur_goods_compositions[] = $composition->id;
            }
        }

        $manufacturers_list = $cur_goods->company->manufacturers->pluck('name', 'id');
        // dd($manufacturers_list);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_goods_categories = operator_right('goods_categories', false, 'index');
        // dd($answer_goods_categories);

        // Категории
        $goods_categories = GoodsCategory::moderatorLimit($answer_goods_categories)
        ->companiesLimit($answer_goods_categories)
        ->authors($answer_goods_categories)
        ->systemItem($answer_goods_categories) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
        $goods_categories_list = get_select_tree($goods_categories, $cur_goods->goods_article->goods_product->goods_category_id, null, null);
        // dd($goods_categories_list);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_goods_products = operator_right('goods_products', false, 'index');
        // dd($answer_goods_products);

        // Группы товаров
        $goods_products_list = GoodsProduct::where('goods_category_id', $cur_goods->goods_article->goods_product->goods_category_id)
        ->orderBy('sort', 'asc')
        ->get()
        ->pluck('name', 'id');

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_catalogs = operator_right('catalogs', false, 'index');

        $catalogs = Catalog::moderatorLimit($answer_catalogs)
        ->companiesLimit($answer_catalogs)
        ->systemItem($answer_catalogs) // Фильтр по системным записям
        ->whereSite_id(2)
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();
        // dd($catalogs);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $catalogs_tree = get_parents_tree($catalogs);

        // Рекурсивно считываем наш шаблон
        function show_cats($items, $padding, $parents){
            $string = '';
            $padding = $padding;

            foreach($items as $item){
                $string .= tpl_menus($item, $padding, $parents);
            }
            return $string;
        }

        // Функция отрисовки option'ов
        function tpl_menus($item, $padding, $parents) {

            // Выбираем пункт родителя
            $selected = '';
            if (in_array($item['id'], $parents)) {
                $selected = ' selected';
            }

            // отрисовываем option's
            if ($item['category_status'] == 1) {
                $menu = '<option value="'.$item['id'].'" class="first"'.$selected.'>'.$item['name'].'</option>';
            } else {
                $menu = '<option value="'.$item['id'].'"'.$selected.'>'.$padding.' '.$item['name'].'</option>';
            }

            // Добавляем пробелы вложенному элементу
            if (isset($item['children'])) {
                $i = 1;
                for($j = 0; $j < $i; $j++){
                    $padding .= '&nbsp;&nbsp';
                }     
                $i++;

                $menu .= show_cats($item['children'], $padding, $parents);
            }
            return $menu;
        }

        $parents = [];
        foreach ($cur_goods->catalogs as $catalog) {
            $parents[] = $catalog->id;
        }
        // dd($parents);

        // Получаем HTML разметку
        $catalogs_list = show_cats($catalogs_tree, '', $parents);

        $goods_category = $cur_goods->goods_article->goods_product->goods_category;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_goods_modes = operator_right('goods_modes', false, 'index');

        $goods_modes = GoodsMode::with(['goods_categories' => function ($query) use ($answer_goods_categories) {
            $query->with(['goods_products' => function ($query) {
                $query->with(['goods_articles.goods' => function ($query) {
                    $query->whereNull('draft');
                }]);
            }])
            ->withCount('goods_products')
            ->moderatorLimit($answer_goods_categories)
            ->companiesLimit($answer_goods_categories)
            ->authors($answer_goods_categories)
            ->systemItem($answer_goods_categories); // Фильтр по системным записям 
        }])
        ->moderatorLimit($answer_goods_modes)
        ->companiesLimit($answer_goods_modes)
        ->authors($answer_goods_modes)
        ->systemItem($answer_goods_modes) // Фильтр по системным записям
        ->template($answer_goods_modes)
        ->orderBy('sort', 'asc')
        ->get()
        ->toArray();

        $goods_modes_list = [];
        foreach ($goods_modes as $goods_mode) {

            $goods_categories_id = [];
            foreach ($goods_mode['goods_categories'] as $goods_cat) {
                $goods_categories_id[$goods_cat['id']] = $goods_cat;
            }

            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
            $goods_cat_list = get_parents_tree($goods_categories_id, null, null, null);

            $goods_modes_list[] = [
                'name' => $goods_mode['name'],
                'alias' => $goods_mode['alias'],
                'goods_categories' => $goods_cat_list,
            ];
        }
        // dd($goods_modes_list);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_raws_categories = operator_right('raws_categories', false, 'index');

        $answer_raws_products = operator_right('raws_products', false, 'index');

        $answer_raws = operator_right('raws', false, 'index');

        $raws_categories = RawsCategory::with(['raws_products' => function ($query) use ($answer_raws_products, $answer_raws) {
            $query->with(['raws_articles.raws' => function ($query) use ($answer_raws) {
                $query
                    // ->moderatorLimit($answer_raws)
                    // ->companiesLimit($answer_raws)
                    // ->authors($answer_raws)
                    // ->systemItem($answer_raws) // Фильтр по системным записям 
                ->whereNull('draft');
            }])
            ->withCount('raws_articles');
                // ->moderatorLimit($answer_raws_products)
                // ->companiesLimit($answer_raws_products)
                // ->authors($answer_raws_products)
                // ->systemItem($answer_raws_products); // Фильтр по системным записям 
        }])
        ->withCount('raws_products')
        ->moderatorLimit($answer_raws_categories)
        ->companiesLimit($answer_raws_categories)
        ->authors($answer_raws_categories)
        ->systemItem($answer_raws_categories) // Фильтр по системным записям 
        ->get()
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $composition_categories_list = get_parents_tree($raws_categories, null, null, null);
        // dd($composition_categories_list);

        $composition_list = [
            'name' => 'Сырье',
            'alias' => 'raws',
            'composition_categories' => $composition_categories_list,
        ];

        // dd($composition_list);
        // dd($goods_modes_list);

        // dd($product->goods_group->goods_category->type);
        if ($cur_goods->metrics_values_count > 0) {
            $metrics_values = [];
            foreach ($cur_goods->metrics_values as $metric) {
                $metrics_values[$metric->id][] = $metric->pivot->value;
            }
        } else {
            $metrics_values = null;
        }

        $raws_compositions_values = $cur_goods->raws_compositions_values->keyBy('id');
        // dd($raws_compositions_values[2]->pivot->value);
        // // dd($compositions_values->where('product_id', 4));
        // $type = $cur_goods->goods_product->goods_category->type;
        // dd($cur_goods->goods_product->goods_category->compositions);
        // foreach ($cur_goods->goods_product->goods_category->compositions as $composition) {
        //     dd($composition->name);
        // }

        // Получаем настройки по умолчанию
        $settings = config()->get('settings');
        // dd($settings);

        $get_settings = EntitySetting::where(['entity' => $this->entity_name])->first();

        if ($get_settings){

            if ($get_settings->img_small_width != null) {
                $settings['img_small_width'] = $get_settings->img_small_width;
            }

            if ($get_settings->img_small_height != null) {
                $settings['img_small_height'] = $get_settings->img_small_height;
            }

            if ($get_settings->img_medium_width != null) {
                $settings['img_medium_width'] = $get_settings->img_medium_width;
            }

            if ($get_settings->img_medium_height != null) {
                $settings['img_medium_height'] = $get_settings->img_medium_height;
            }

            if ($get_settings->img_large_width != null) {
                $settings['img_large_width'] = $get_settings->img_large_width;
            }

            if ($get_settings->img_large_height != null) {
                $settings['img_large_height'] = $get_settings->img_large_height;  
            }

            if ($get_settings->img_formats != null) {
                $settings['img_formats'] = $get_settings->img_formats;
            }

            if ($get_settings->img_min_width != null) {
                $settings['img_min_width'] = $get_settings->img_min_width;
            }

            if ($get_settings->img_min_height != null) {
                $settings['img_min_height'] = $get_settings->img_min_height;   
            }

            if ($get_settings->img_max_size != null) {
                $settings['img_max_size'] = $get_settings->img_max_size;
            }
        }

        // Получаем настройки по умолчанию
        $settings_album = config()->get('settings');
        // dd($settings_album);

        $get_settings = EntitySetting::where(['entity' => 'albums_categories', 'entity_id' => 1])->first();

        if ($get_settings){

            if ($get_settings->img_small_width != null) {
                $settings_album['img_small_width'] = $get_settings->img_small_width;
            }

            if ($get_settings->img_small_height != null) {
                $settings_album['img_small_height'] = $get_settings->img_small_height;
            }

            if ($get_settings->img_medium_width != null) {
                $settings_album['img_medium_width'] = $get_settings->img_medium_width;
            }

            if ($get_settings->img_medium_height != null) {
                $settings_album['img_medium_height'] = $get_settings->img_medium_height;
            }

            if ($get_settings->img_large_width != null) {
                $settings_album['img_large_width'] = $get_settings->img_large_width;
            }

            if ($get_settings->img_large_height != null) {
                $settings_album['img_large_height'] = $get_settings->img_large_height;  
            }

            if ($get_settings->img_formats != null) {
                $settings_album['img_formats'] = $get_settings->img_formats;
            }

            if ($get_settings->img_min_width != null) {
                $settings_album['img_min_width'] = $get_settings->img_min_width;
            }

            if ($get_settings->img_min_height != null) {
                $settings_album['img_min_height'] = $get_settings->img_min_height;   
            }

            if ($get_settings->img_max_size != null) {
                $settings_album['img_max_size'] = $get_settings->img_max_size;
            }
        }
        // dd($settings_album);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
        // dd($page_info);

        return view('goods.edit', compact('cur_goods', 'page_info', 'goods_categories_list', 'goods_products_list', 'manufacturers_list', 'goods_modes_list', 'cur_goods_compositions', 'metrics_values', 'raws_compositions_values', 'settings', 'settings_album', 'composition_list', 'catalogs_list'));
    }

    public function update(Request $request, $id)
    {

        // dd($request);
        if (isset($request->metrics)) {
            $metrics_count = count($request->metrics);
        } else {
            $metrics_count = 0;
        }
        // dd($metrics_count);

        if (isset($request->compositions)) {
            $compositions_count = count($request->compositions);
        } else {
            $compositions_count = 0;
        }

        // Если снят флаг черновика
        if (empty($request->draft)) {

            // Проверка на наличие артикула
            // Вытаскиваем артикулы продукции с нужным нам числом метрик и составов
            // $goods = cur_goods::with('metrics_values', 'compositions_values')
            // ->where('product_id', $request->product_id)
            // ->where(['metrics_count' => $metrics_count, 'compositions_count' => $compositions_count])
            // ->get();

            $goods_articles = GoodsArticle::with(['goods' => function ($query) {
                $query->with('metrics_values', 'raws_compositions_values');
            }])
            ->where('goods_product_id', $request->goods_product_id)
            ->where(['metrics_count' => $metrics_count, 'compositions_count' => $compositions_count])
            ->get();
            // dd($goods_articles);

            // Создаем массив совпадений
            $coincidence = [];
            // dd($request);

            // Сравниваем метрики
            $metrics_array = [];
            $raws_compositions_array = [];
            foreach ($goods_articles as $goods_article) {
                foreach ($goods_article->goods as $cur_goods) {
                    // dd($cur_goods);
                    foreach ($cur_goods->metrics_values as $metric) {
                        // dd($metric);
                        $metrics_array[$cur_goods->id][$metric->id][] = $metric->pivot->value;
                    }

                    foreach ($cur_goods->raws_compositions_values as $raws_composition) {
                        // dd($raws_composition);
                        $raws_compositions_array[$cur_goods->id][$raws_composition->id] = $raws_composition->pivot->value;
                    }
                }
            }
            // dd($metrics_array);
            // dd($raws_compositions_array);
            $metrics_values = $request->metrics;
            // dd($metrics_values);
            foreach ($metrics_array as $item) {
                if ($metrics_values == $item) {
                    // Если значения метрик совпали, создаюм ключ метрик
                    $coincidence['metric'] = 1;
                }
            }

            $raws_compositions_values = $request->compositions;
            // dd($raws_compositions_values);
            foreach ($raws_compositions_array as $item) {
                if ($raws_compositions_values == $item) {
                    // Если значения метрик совпали, создаюм ключ метрик
                    $coincidence['raws_composition'] = 1;
                }
            }
            
            // dd($coincidence);


            // Проверяем наличие ключей в массиве
            if (array_key_exists('metric', $coincidence) && array_key_exists('raws_composition', $coincidence)) {
                // Если ключи присутствуют, даем ошибку
                return redirect()->back()->withInput()->withErrors('Такой артикул уже существует!');

            // dd($coincidence);
            }
        }

        // Если что то не совпало, пишем новый артикул

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $cur_goods = Goods::with('goods_article')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $cur_goods);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        if ($request->hasFile('photo')) {

            // Вытаскиваем настройки
            // Вытаскиваем базовые настройки сохранения фото
            $settings = config()->get('settings');

            // Начинаем проверку настроек, от компании до альбома
            // Смотрим общие настройки для сущности
            $get_settings = EntitySetting::where(['entity' => $this->entity_name])->first();

            if($get_settings){

                if ($get_settings->img_small_width != null) {
                    $settings['img_small_width'] = $get_settings->img_small_width;
                }

                if ($get_settings->img_small_height != null) {
                    $settings['img_small_height'] = $get_settings->img_small_height;
                }

                if ($get_settings->img_medium_width != null) {
                    $settings['img_medium_width'] = $get_settings->img_medium_width;
                }

                if ($get_settings->img_medium_height != null) {
                    $settings['img_medium_height'] = $get_settings->img_medium_height;
                }

                if ($get_settings->img_large_width != null) {
                    $settings['img_large_width'] = $get_settings->img_large_width;
                }

                if ($get_settings->img_large_height != null) {
                    $settings['img_large_height'] = $get_settings->img_large_height;  
                }

                if ($get_settings->img_formats != null) {
                    $settings['img_formats'] = $get_settings->img_formats;
                }

                if ($get_settings->img_min_width != null) {
                    $settings['img_min_width'] = $get_settings->img_min_width;
                }

                if ($get_settings->img_min_height != null) {
                    $settings['img_min_height'] = $get_settings->img_min_height;   
                }

                if ($get_settings->img_max_size != null) {
                    $settings['img_max_size'] = $get_settings->img_max_size;

                }
            }

            $directory = $company_id.'/media/goods/'.$cur_goods->id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($cur_goods->photo_id) {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, $cur_goods->photo_id, $settings);

            } else {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);

            }
            $photo = $array['photo'];

            $cur_goods->photo_id = $photo->id;
        } 

        // $cur_goods->name = $request->name;

        // $cur_goods->manually = $request->manually;
        // // $cur_goods->external = $request->external;
        // $cur_goods->cost = $request->cost;
        // $cur_goods->price = $request->price;

        // -------------------------------------------------------------------------------------------------
        // ПЕРЕНОС ГРУППЫ ТОВАРА В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ

        // Получаем выбранную категорию со старницы (то, что указал пользователь)
        $goods_category_id = $request->goods_category_id;

        // Смотрим: была ли она изменена
        if($cur_goods->goods_article->goods_product->goods_category_id != $goods_category_id){

            // Была изменена! Переназначаем категорию группе:
            $item = GoodsProduct::where('id', $cur_goods->goods_article->goods_product_id)->update(['goods_category_id' => $goods_category_id]);
        };

        // -------------------------------------------------------------------------------------------------
        // ПЕРЕНОС ТОВАРА В ДРУГУЮ ГРУППУ ПОЛЬЗОВАТЕЛЕМ
        // Важно! Важно проверить, соответствеут ли группа в которую переноситься товар, метрикам самого товара
        // Если не соответствует - дать отказ. Если соответствует - осуществить перенос

        // Тут должен быть код проверки !!! 

        // А, пока изменяем без проверки
        $cur_goods->goods_article->goods_product_id = $request->goods_product_id;

        // $cur_goods->description = $request->description;
        // $cur_goods->manufacturer_id = $request->manufacturer_id;
        // $cur_goods->metrics_count = $metrics_count;
        // $cur_goods->compositions_count = $compositions_count;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false) {
            $cur_goods->moderation = 1;
        }

        if ($request->portion_status == 1) {
            $cur_goods->portion_status = $request->portion_status;
            $cur_goods->portion_name = $request->portion_name;
            $cur_goods->portion_abbreviation = $request->portion_abbreviation;
            $cur_goods->portion_count = $request->portion_count;
        } else {
            $cur_goods->portion_status = null;
        }

        $cur_goods->system_item = $request->system_item;

        $cur_goods->description = $request->description;
        $cur_goods->display = $request->display;
        $cur_goods->draft = $request->draft;

        $cur_goods->manually = $request->manually;
        $cur_goods->cost = $request->cost;
        $cur_goods->price = $request->price;

        $cur_goods->company_id = $company_id;
        $cur_goods->author_id = $user_id;
        $cur_goods->save();

        if ($cur_goods) {

            if ($cur_goods->goods_article->name != $request->name) {
                $goods_article = $cur_goods->goods_article;
                $goods_article->name = $request->name;
                $goods_article->save();
            }

            if (isset($request->catalogs)) {

                $mass = [];
                foreach ($request->catalogs as $catalog) {
                    $mass[$catalog] = ['display' => 1];
                }

                // dd($mass);
                $cur_goods->catalogs()->sync($mass);
            } else {
                $cur_goods->catalogs()->detach();
            }

            if ($cur_goods->draft == 1) {

                //     // dd($metrics_insert);
                //     if (isset($request->compositions)) {
                //         $compositions_insert = [];
                //         foreach ($request->compositions as $composition_id => $value) {
                //             // dd($value['value']);
                //             $compositions_insert[$value['cur_goods']]['entity'] = 'goods';
                //             $compositions_insert[$value['cur_goods']]['value'] = $value['count'];
                //         }

                //         // Пишем состав
                //         $cur_goods->compositions_values()->attach($compositions_insert);
                //     }
            }

            // dd($request->metrics);
            if (isset($request->metrics)) {

                $cur_goods->metrics_values()->detach();

                $metrics_insert = [];

                foreach ($request->metrics as $metric_id => $values) {
                    foreach ($values as $value) {
                        // dd($value);
                        $cur_goods->metrics_values()->attach([
                            $metric_id => [
                                'value' => $value,
                            ]
                        ]);
                    }
                }
                // dd($metrics_insert);

                // Пишем метрики
                // $cur_goods->metrics_values()->attach($metrics_insert);
            } else {
                $cur_goods->metrics_values()->detach();
            }

            // dd($request->compositions);
            if (isset($request->compositions)) {
                $cur_goods->raws_compositions_values()->detach();
                $compositions_insert = [];
                foreach ($request->compositions as $composition_id => $value) {
                    $compositions_insert[$composition_id] = [
                        'value' => $value,
                    ];
                }
                // dd($compositions_insert);
                $cur_goods->raws_compositions_values()->attach($compositions_insert);
            } else {
                $cur_goods->raws_compositions_values()->detach();
            }

            $goods_article = GoodsArticle::where('id', $cur_goods->goods_article_id)->update(['metrics_count' => $metrics_count, 'compositions_count' => $compositions_count]);

            // Если ли есть 
            if ($request->cookie('backlink') != null) {

                $backlink = Cookie::get('backlink');
                return Redirect($backlink);

            }

            return Redirect('/admin/goods');

        } else {
            abort(403, 'Ошибка записи группы товаров');
        }
    }

    public function destroy($id)
    {
        //
    }

    public function archive(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'delete');

        // ГЛАВНЫЙ ЗАПРОС:
        $cur_goods = Goods::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('delete', $cur_goods);

        if ($cur_goods) {

            // Получаем пользователя
            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);

            $cur_goods->editor_id = $user_id;
            $cur_goods->archive = 1;
            $cur_goods->save();

            if ($cur_goods) {
                return Redirect('/admin/goods');
            } else {
                abort(403, 'Ошибка при архивации товара');
            }
        } else {
            abort(403, 'Товар не найден');
        }
    }

        // Сортировка
    public function ajax_sort(Request $request)
    {
        $i = 1;

        foreach ($request->goods as $item) {
            Goods::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
    }

        // Системная запись
    public function ajax_system_item(Request $request)
    {

        if ($request->action == 'lock') {
            $system = 1;
        } else {
            $system = null;
        }

        $item = Goods::where('id', $request->id)->update(['system_item' => $system]);

        if ($item) {

            $result = [
                'error_status' => 0,
            ];  
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении статуса системной записи!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

        // Отображение на сайте
    public function ajax_display(Request $request)
    {

        if ($request->action == 'hide') {
            $display = null;
        } else {
            $display = 1;
        }

        $item = Goods::where('id', $request->id)->update(['display' => $display]);

        if ($item) {

            $result = [
                'error_status' => 0,
            ];  
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении отображения на сайте!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // Отображение на сайте
    public function ajax_sync(Request $request)
    {

        // Описание ошибки
        $ajax_error = [];
        $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
        $ajax_error['text'] = "Для начала необходимо создать категории товаров. А уже потом будем добавлять товары. Ок?";
        $ajax_error['link'] = "/admin/goods_categories"; // Ссылка на кнопке
        $ajax_error['title_link'] = "Идем в раздел категорий"; // Текст на кнопке

        return view('ajax_error', compact('ajax_error'));

    }

    public function get_inputs(Request $request)
    {

        $product = Product::with('metrics.property', 'compositions.unit')->withCount('metrics', 'compositions')->findOrFail($request->product_id);
        return view('products.cur_goods-form', compact('product'));
        // $product = Product::with('metrics.property', 'compositions.unit')->findOrFail(1);
        // dd($product);

    }

    public function add_photo(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod('store'), Photo::class);

        if ($request->hasFile('photo')) {
            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            // $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod('index'));

            // Получаем авторизованного пользователя
            $user = $request->user();

            // Смотрим компанию пользователя
            $company_id = $user->company_id;

            // Скрываем бога
            $user_id = hideGod($user);

           // Иначе переводим заголовок в транслитерацию
            $alias = Transliterate::make($request->name, ['type' => 'url', 'lowercase' => true]);

            $album = Album::where(['company_id' => $company_id, 'name' => $request->name, 'albums_category_id' => 1])->first();

            if ($album) {
                $album_id = $album->id;
            } else {
                $album = new Album;
                $album->company_id = $company_id;
                $album->name = $request->name;
                $album->alias = $alias;
                $album->albums_category_id = 1;
                $album->description = $request->name;
                $album->author_id = $user_id;
                $album->save();

                $album_id = $album->id;
            }

            $cur_goods = Goods::findOrFail($request->id);

            if ($cur_goods->album_id == null) {
                $cur_goods->album_id = $album_id;
                $cur_goods->save();

                if (!$cur_goods) {
                    abort(403, 'Ошибка записи альбома в продукцию');
                }
            }

            // Вытаскиваем настройки
            // Вытаскиваем базовые настройки сохранения фото
            $settings = config()->get('settings');

            // Начинаем проверку настроек, от компании до альбома
            // Смотрим общие настройки для сущности
            $get_settings = EntitySetting::where(['entity' => 'albums_categories', 'entity_id'=> 1])->first();

            if ($get_settings) {

                if ($get_settings->img_small_width != null) {
                    $settings['img_small_width'] = $get_settings->img_small_width;
                }

                if ($get_settings->img_small_height != null) {
                    $settings['img_small_height'] = $get_settings->img_small_height;
                }

                if ($get_settings->img_medium_width != null) {
                    $settings['img_medium_width'] = $get_settings->img_medium_width;
                }

                if ($get_settings->img_medium_height != null) {
                    $settings['img_medium_height'] = $get_settings->img_medium_height;
                }

                if ($get_settings->img_large_width != null) {
                    $settings['img_large_width'] = $get_settings->img_large_width;
                }

                if ($get_settings->img_large_height != null) {
                    $settings['img_large_height'] = $get_settings->img_large_height;  
                }

                if ($get_settings->img_formats != null) {
                    $settings['img_formats'] = $get_settings->img_formats;
                }

                if ($get_settings->img_min_width != null) {
                    $settings['img_min_width'] = $get_settings->img_min_width;
                }

                if ($get_settings->img_min_height != null) {
                    $settings['img_min_height'] = $get_settings->img_min_height;   
                }

                if ($get_settings->img_max_size != null) {
                    $settings['img_max_size'] = $get_settings->img_max_size;
                }
            }

            $directory = $company_id.'/media/albums/'.$album_id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            $array = save_photo($request, $directory,  $alias.'-'.time(), $album_id, null, $settings);

            $photo = $array['photo'];
            $upload_success = $array['upload_success'];

            $media = new AlbumEntity;
            $media->album_id = $album_id;
            $media->entity_id = $photo->id;
            $media->entity = 'photos';
            $media->save();

            // $check_media = AlbumEntity::where(['album_id' => $album_id, 'entity_id' => $request->id, 'entity' => 'product'])->first();

            // if ($check_media == false) {
            //     $media = new AlbumEntity;
            //     $media->album_id = $album_id;
            //     $media->entity_id = $request->id;
            //     $media->entity = 'product';
            //     $media->save();
            // }

            if ($upload_success) {

                // Переадресовываем на index
                // return redirect()->route('/products/'.$product->id.'/edit', ['photo' => $photo, 'upload_success' => $upload_success]);

                return response()->json($upload_success, 200);
            } else {
                return response()->json('error', 400);
            } 

        } else {
            return response()->json('error', 400);
        } 
    }

    public function photos(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod('index'));

        // ГЛАВНЫЙ ЗАПРОС:
        $cur_goods = Goods::with('album.photos')->moderatorLimit($answer)->findOrFail($request->cur_goods_id);
        // dd($product);

        // Подключение политики
        $this->authorize(getmethod('edit'), $cur_goods);

        return view('goods.photos', compact('cur_goods'));

    }


}
