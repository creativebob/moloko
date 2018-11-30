<?php

namespace App\Http\Controllers;

// Модели
// use App\Article;
use App\Raw;
use App\RawsCategory;
use App\RawsMode;
use App\RawsProduct;
use App\RawsArticle;
use App\Album;
use App\AlbumEntity;
use App\Photo;
use App\UnitsCategory;
use App\Catalog;

use App\EntitySetting;

use App\ArticleValue;

// Политика
use App\Policies\RawsPolicy;

// Куки
use Illuminate\Support\Facades\Cookie;

// Транслитерация
use Transliterate;


use Illuminate\Http\Request;

class RawController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Raw $raw)
    {
        $this->middleware('auth');
        $this->raw = $raw;


        // dd($raw);
        $this->class = Raw::class;
        $this->model = 'App\Raw';
        $this->entity_table = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Raw::class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_table);
        if (($filter_url != null)&&($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entity_table));
            return Redirect($filter_url);
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_table, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------

        $raws = Raw::with('author', 'company', 'raws_article.raws_product.raws_category', 'catalogs.site')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        ->filter($request, 'raws_category_id', 'raws_article.raws_product')
        ->filter($request, 'raws_product_id', 'raws_article')
        ->whereNull('archive')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_table, $request, [
            'author',               // Автор записи
            'raws_category',    // Категория услуги
            'raws_product',     // Группа услуги
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);


        // Инфо о странице
        $page_info = pageInfo($this->entity_table);

        return view('raws.index', compact('raws', 'page_info', 'filter'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Raw::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_raws_categories = operator_right('raws_categories', false, 'index');

        // Главный запрос
        $raws_categories = RawsCategory::withCount('raws_products')
        ->with('raws_products')
        ->moderatorLimit($answer_raws_categories)
        ->companiesLimit($answer_raws_categories)
        ->authors($answer_raws_categories)
        ->systemItem($answer_raws_categories) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get();

        if($raws_categories->count() < 1){

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
            $ajax_error['text'] = "Для начала необходимо создать категории сырья. А уже потом будем добавлять сырье. Ок?";
            $ajax_error['link'] = "/admin/raws_categories"; // Ссылка на кнопке
            $ajax_error['title_link'] = "Идем в раздел категорий"; // Текст на кнопке

            return view('ajax_error', compact('ajax_error'));
        }

        $raws_products_count = $raws_categories[0]->raws_products_count;
        $parent_id = null;

        if ($request->cookie('conditions') != null) {

            $condition = Cookie::get('conditions');
            if(isset($condition['raws_category'])) {
                $raws_category_id = $condition['raws_category'];
                $raws_category = $raws_categories->find($raws_category_id);
                // dd($raws_category);
                $raws_products_count = $raws_category->raws_products_count;
                $parent_id = $raws_category_id;
                // dd($raws_products_count);
            }
        }

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $raws_categories_list = get_select_tree($raws_categories->keyBy('id')->toArray(), $parent_id, null, null);
        // echo $raws_categories_list;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_units_categories = operator_right('units_categories', false, 'index');

        // Главный запрос
        $units_categories_list = UnitsCategory::with(['units' => function ($query) {
            $query->pluck('name', 'id');
        }])
        ->moderatorLimit($answer_units_categories)
        ->companiesLimit($answer_units_categories)
        ->authors($answer_units_categories)
        ->systemItem($answer_units_categories) // Фильтр по системным записям
        ->template($answer_units_categories)
        ->orderBy('sort', 'asc')
        ->get()
        ->pluck('name', 'id');

        return view('raws.create', compact('raws_categories_list', 'raws_products_count', 'units_categories_list'));
    }

    public function store(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Raw::class);
        // dd($request);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        $name = $request->name;
        $raws_category_id = $request->raws_category_id;

        switch ($request->mode) {
            case 'mode-default':
            $raws_product = RawsProduct::where(['name' => $name, 'raws_category_id' => $raws_category_id])->first();

            if ($raws_product) {
                $raws_product_id = $raws_product->id;
            } else {
                $raws_product = new RawsProduct;
                $raws_product->name = $name;
                $raws_product->raws_category_id = $raws_category_id;
                $raws_product->unit_id = $request->unit_id;

                // Модерация и системная запись
                $raws_product->system_item = $request->system_item;
                $raws_product->display = 1;
                $raws_product->company_id = $company_id;
                $raws_product->author_id = $user_id;
                $raws_product->save();

                if ($raws_product) {
                    $raws_product_id = $raws_product->id;
                } else {
                    abort(403, 'Ошибка записи группы товаров');
                }
            }
            break;

            case 'mode-add':
            $raws_product_name = $request->raws_product_name;
            $raws_product = RawsProduct::where(['name' => $raws_product_name, 'raws_category_id' => $raws_category_id])->first();

            if ($raws_product) {
                $raws_product_id = $raws_product->id;
            } else {

                // Наполняем сущность данными
                $raws_product = new RawsProduct;
                $raws_product->name = $request->raws_product_name;
                $raws_product->unit_id = $request->unit_id;
                $raws_product->raws_category_id = $raws_category_id;

                // Модерация и системная запись
                $raws_product->system_item = $request->system_item;
                $raws_product->display = 1;
                $raws_product->company_id = $company_id;
                $raws_product->author_id = $user_id;
                $raws_product->save();

                if ($raws_product) {
                    $raws_product_id = $raws_product->id;
                } else {
                    abort(403, 'Ошибка записи группы услуг');
                }
            }
            break;

            case 'mode-select':
            $raws_product = RawsProduct::findOrFail($request->raws_product_id);
            $raw_product_name = $raws_product->name;
            $raws_product_id = $raws_product->id;
            break;
        }

        $raws_article = new RawsArticle;
        $raws_article->raws_product_id = $raws_product_id;
        $raws_article->company_id = $company_id;
        $raws_article->author_id = $user_id;
        $raws_article->name = $name;
        $raws_article->save();

        if ($raws_article) {

            $raw = new Raw;
            $raw->cost = $request->cost;
            $raw->company_id = $company_id;
            $raw->author_id = $user_id;
            $raw->draft = 1;
            $raw->raws_article()->associate($raws_article);
            $raw->save();

            if ($raw) {

                // Пишем сессию
                // $mass = [
                //     'raws_category' => $raws_category_id,
                // ];

                // Cookie::queue('conditions', $mass, 1440);

                if ($request->quickly == 1) {
                    return redirect('/admin/raws');
                } else {
                    return redirect('/admin/raws/'.$raw->id.'/edit');
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

        // ГЛАВНЫЙ ЗАПРОС:
        $answer_raws = operator_right($this->entity_table, $this->entity_dependence, getmethod(__FUNCTION__));

        $raw = Raw::with(['raws_article.raws_product.raws_category' => function ($query) {
            $query->with(['metrics.property', 'metrics.unit'])
            ->withCount('metrics');
        }, 'album.photos', 'company.manufacturers', 'photo', 'catalogs'])
        ->moderatorLimit($answer_raws)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raw);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $manufacturers_list = $raw->company->manufacturers->pluck('name', 'id');
        // dd($manufacturers_list);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_raws_categories = operator_right('raws_categories', false, 'index');
        // dd($answer_raws_categories);

        // Категории
        $raws_categories = RawsCategory::moderatorLimit($answer_raws_categories)
        ->companiesLimit($answer_raws_categories)
        ->authors($answer_raws_categories)
        ->systemItem($answer_raws_categories) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get(['id','name','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
        $raws_categories_list = get_select_tree($raws_categories, $raw->raws_article->raws_product->raws_category_id, null, null);
        // dd($raws_categories_list);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_raws_products = operator_right('raws_products', false, 'index');
        // dd($answer_raws_products);

        // Группы товаров
        $raws_products_list = RawsProduct::where('raws_category_id', $raw->raws_article->raws_product->raws_category_id)
        ->orderBy('sort', 'asc')
        ->get()
        ->pluck('name', 'id');

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_catalogs = operator_right('catalogs', false, 'index');

        $catalogs = Catalog::moderatorLimit($answer_catalogs)
        ->companiesLimit($answer_catalogs)
        ->systemItem($answer_catalogs) // Фильтр по системным записям
        ->whereSite_id(2)
        // ->orderBy('sort', 'asc')
        ->get(['id','name','parent_id'])
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

            // Отрисовываем option's
            if ($item['parent_id'] == null) {
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
        foreach ($raw->catalogs as $catalog) {
            $parents[] = $catalog->id;
        }
        // dd($parents);

        // Получаем HTML разметку
        $catalogs_list = show_cats($catalogs_tree, '', $parents);

        $raws_category = $raw->raws_article->raws_product->raws_category;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_raws_modes = operator_right('raws_modes', false, 'index');

        $raws_modes = RawsMode::with(['raws_categories' => function ($query) use ($answer_raws_categories) {
            $query->with(['raws_products' => function ($query) {
                $query->with(['raws_articles.raws' => function ($query) {
                    $query->whereNull('draft');
                }]);
            }])
            ->withCount('raws_products')
            ->moderatorLimit($answer_raws_categories)
            ->companiesLimit($answer_raws_categories)
            ->authors($answer_raws_categories)
            ->systemItem($answer_raws_categories); // Фильтр по системным записям
        }])
        ->moderatorLimit($answer_raws_modes)
        ->companiesLimit($answer_raws_modes)
        ->authors($answer_raws_modes)
        ->systemItem($answer_raws_modes) // Фильтр по системным записям
        ->template($answer_raws_modes)
        ->orderBy('sort', 'asc')
        ->get()
        ->toArray();

        $raws_modes_list = [];
        foreach ($raws_modes as $raws_mode) {

            $raws_categories_id = [];
            foreach ($raws_mode['raws_categories'] as $raws_cat) {
                $raws_categories_id[$raws_cat['id']] = $raws_cat;
            }

            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
            $raws_cat_list = get_parents_tree($raws_categories_id, null, null, null);

            $raws_modes_list[] = [
                'name' => $raws_mode['name'],
                'alias' => $raws_mode['alias'],
                'raws_categories' => $raws_cat_list,
            ];
        }

        if ($raw->metrics_values_count > 0) {
            $metrics_values = [];
            foreach ($raw->metrics_values as $metric) {
                $metrics_values[$metric->id][] = $metric->pivot->value;
            }
        } else {
            $metrics_values = null;
        }

        // Получаем настройки по умолчанию
        $settings = config()->get('settings');
        // dd($settings);

        $get_settings = EntitySetting::where(['entity' => $this->entity_table])->first();

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

        // Получаем настройки по умолчанию
        $settings_album = config()->get('settings');
        // dd($settings_album);

        $get_settings = EntitySetting::where(['entity' => 'albums_categories', 'entity_id' => 1])->first();

        if($get_settings){

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

        // Инфо о странице
        $page_info = pageInfo('raws');

        return view('raws.edit', compact('raw', 'page_info', 'raws_categories_list', 'raws_products_list', 'manufacturers_list', 'raws_modes_list', 'raws_category_compositions', 'metrics_values', 'compositions_values', 'settings', 'settings_album', 'catalogs_list'));
    }

    public function update(Request $request, Raw $raw)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_table, $this->entity_dependence, getmethod(__FUNCTION__));

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raw);

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
            $get_settings = EntitySetting::where(['entity' => $this->entity_table])->first();

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

            $directory = $company_id.'/media/raws/'.$raw->id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id компании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($raw->photo_id) {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, $raw->photo_id, $settings);

            } else {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);
            }

            $photo = $array['photo'];
            $raw->photo_id = $photo->id;
        }

        // -------------------------------------------------------------------------------------------------
        // ПЕРЕНОС ГРУППЫ УСЛУГИ В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ

        // dd($request->raws_category_id);
        // Получаем выбранную категорию со старницы (то, что указал пользователь)
        $raws_category_id = $request->raws_category_id;

        // Смотрим: была ли она изменена
        if($raw->raws_article->raws_product->raws_category_id != $raws_category_id){

            // Была изменена! Переназначаем категорию группе:
            $item = RawsProduct::where('id', $raw->raws_article->raws_product_id)->update(['raws_category_id' => $raws_category_id]);
        };

        // -------------------------------------------------------------------------------------------------
        // ПЕРЕНОС СЫРЬЯ В ДРУГУЮ ГРУППУ ПОЛЬЗОВАТЕЛЕМ
        // Важно! Важно проверить, соответствеут ли группа в которую переноситься товар, метрикам самого товара
        // Если не соответствует - дать отказ. Если соответствует - осуществить перенос

        // Тут должен быть код проверки !!!

        // А, пока изменяем без проверки
        $raw->raws_article->raws_product_id = $request->raws_product_id;

        // Наполняем сущность данными
        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false) {
            $raw->moderation = 1;
        }

        // Системная запись
        $raw->system_item = $request->system_item;
        $raw->description = $request->description;
        $raw->display = $request->display;
        $raw->draft = $request->draft;
        $raw->manually = $request->manually;
        $raw->cost = $request->cost;
        $raw->price = $request->price;
        $raw->company_id = $company_id;
        $raw->author_id = $user_id;
        $raw->save();

        if ($raw) {

            if ($raw->raws_article->name != $request->name) {
                $raws_article = $raw->raws_article;
                $raws_article->name = $request->name;
                $raws_article->save();
            }

            if (isset($request->catalogs)) {

                $mass = [];
                foreach ($request->catalogs as $catalog) {
                    $mass[$catalog] = ['display' => 1];
                }

                // dd($mass);
                $raw->catalogs()->sync($mass);
            } else {
                $raw->catalogs()->detach();
            }

            // dd($request->metrics);
            if (isset($request->metrics)) {

                $raw->metrics_values()->detach();

                $metrics_insert = [];

                foreach ($request->metrics as $metric_id => $values) {
                    foreach ($values as $value) {
                            // dd($value);
                        $raw->metrics_values()->attach([
                            $metric_id => [
                                'entity' => 'metrics',
                                'value' => $value,
                            ],
                        ]);
                    }
                }
            }

            // echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return Redirect('/admin/raws');

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
        $answer = operator_right($this->entity_table, $this->entity_dependence, 'delete');

        // ГЛАВНЫЙ ЗАПРОС:
        $raw = Raw::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raw);

        if ($raw) {

            // Получаем пользователя
            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);

            $raw->editor_id = $user_id;
            $raw->archive = 1;
            $raw->save();

            if ($raw) {
                return Redirect('/admin/raws');
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

        foreach ($request->raws as $item) {
            Raw::where('id', $item)->update(['sort' => $i]);
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

        $item = Raw::where('id', $request->id)->update(['system_item' => $system]);

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

        $item = Raw::where('id', $request->id)->update(['display' => $display]);

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

    public function get_inputs(Request $request)
    {

        $product = Product::with('metrics.property', 'compositions.unit')->withCount('metrics', 'compositions')->findOrFail($request->product_id);
        return view('products.raws-form', compact('product'));
    }

    public function add_photo(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod('store'), Photo::class);

        if ($request->hasFile('photo')) {
            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            // $answer = operator_right($this->entity_table, $this->entity_dependence, getmethod('index'));

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

            $raw = Raw::findOrFail($request->id);

            if ($raw->album_id == null) {
                $raw->album_id = $album_id;
                $raw->save();

                if (!$raw) {
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

            if ($upload_success) {

                // Переадресовываем на index
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
        $answer = operator_right($this->entity_table, $this->entity_dependence, getmethod('index'));

        // ГЛАВНЫЙ ЗАПРОС:
        $raw = Raw::with('album.photos')->moderatorLimit($answer)->findOrFail($request->raw_id);
        // dd($product);

        // Подключение политики
        $this->authorize(getmethod('edit'), $raw);

        return view('raws.photos', compact('raw'));
    }
}
