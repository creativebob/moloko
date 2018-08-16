<?php

namespace App\Http\Controllers;

// Модели
// use App\Article;
use App\Raw;
use App\RawsCategory;
use App\RawsMode;
use App\RawsProduct;
use App\Album;
use App\AlbumEntity;
use App\Photo;
use App\UnitsCategory;

use App\EntitySetting;

use App\ArticleValue;

// Политика
use App\Policies\RawsPolicy;
// use App\Policies\AreaPolicy;
// use App\Policies\RegionPolicy;

// Куки
use Illuminate\Support\Facades\Cookie;

// Транслитерация
use Transliterate;


use Illuminate\Http\Request;

class RawController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'raws';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Включение контроля активного фильтра 
        $filter_url = autoFilter($request, $this->entity_name);
        if(($filter_url != null)&&($request->filter != 'active')){

            Cookie::queue(Cookie::forget('filter_' . $this->entity_name));
            return Redirect($filter_url);
        };
        
        // Подключение политики
        $this->authorize('index', Raw::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------

        $raws = Raw::with('author', 'company', 'raws_product')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        ->filter($request, 'raws_product_id')
        ->filter($request, 'raws_category_id', 'raws_product')
        ->whereNull('archive')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // --------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА
        // --------------------------------------------------------------------------------------------------------

        $filter_query = Raw::with('author', 'company', 'raws_product', 'raws_product.raws_category')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->whereNull('archive')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        // Создаем контейнер фильтра
        $filter['status'] = null;
        $filter['entity_name'] = $this->entity_name;

        $filter = addFilter($filter, $filter_query, $request, 'Выберите автора:', 'author', 'author_id', null, 'internal-id-one');
        $filter = addFilter($filter, $filter_query, $request, 'Выберите категорию:', 'raws_category', 'raws_category_id', 'raws_product', 'external-id-one');
        $filter = addFilter($filter, $filter_query, $request, 'Выберите группу:', 'raws_product', 'raws_product_id', null, 'internal-id-one');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // -------------------------------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

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
        // dd($request);

        $raws_category_id = $request->raws_category_id;

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        $name = $request->name;

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
                // $raws_product->unit_id = $request->unit_id;

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
                $raws_product->unit_id = 26;

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

            $service_product_name = $raws_product->name;
            $raws_product_id = $raws_product->id;
            break;
        }

        $raw = new Raw;

        $raw->draft = 1;
        $raw->raws_product_id = $raws_product_id;
        $raw->company_id = $company_id;
        $raw->author_id = $user_id;
        $raw->name = $name;
        $raw->cost = $request->cost;
        $raw->save();

        if ($raw) {

            // Пишем сессию
            $mass = [
                'raws_category' => $raws_category_id,
            ];

            Cookie::queue('conditions', $mass, 1440);

            if ($request->quickly == 1) {
                return redirect('/admin/raws');
            } else {
                return redirect('/admin/raws/'.$raw->id.'/edit'); 
            }

        } else {
            abort(403, 'Ошибка записи артикула товара');
        }   
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // ГЛАВНЫЙ ЗАПРОС:
        $answer_raws = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // $cur_raws = raws::with(['raws_product.raws_category' => function ($query) {
        //     $query->with(['metrics.property', 'metrics.property', 'compositions' => function ($query) {
        //         $query->with(['raws' => function ($query) {
        //             $query->whereNull('template');
        //         }]);
        //     }])
        //     ->withCount('metrics', 'compositions');
        // }, 'album.photos', 'company.manufacturers', 'metrics_values', 'compositions_values'])->withCount(['metrics_values', 'compositions_values'])->moderatorLimit($answer_raws)->findOrFail($id);

        $raw = Raw::with(['raws_product.raws_category' => function ($query) {
            $query->with(['metrics.property', 'metrics.unit'])
            ->withCount('metrics');
        }, 'album.photos', 'company.manufacturers', 'metrics_values'])
        ->withCount(['metrics_values'])->moderatorLimit($answer_raws)->findOrFail($id);

        // $cur_raws = raws::with(['raws_product.raws_category.metrics', 'raws_product.raws_category.compositions.raws_products',  'album.photos', 'company.manufacturers'])->moderatorLimit($answer_raws)->findOrFail($id);
        // dd($cur_raws);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raw);


        $manufacturers_list = $raw->company->manufacturers->pluck('name', 'id');
        // dd($manufacturers_list);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_raws_categories = operator_right('raws_categories', false, 'index');
        // dd($answer_raws_categories);

        // Категории
        $raws_categories = RawsCategory::moderatorLimit($answer_raws_categories)
        ->companiesLimit($answer_raws_categories)
        ->authors($answer_raws_categories)
        ->systemItem($answer_raws_categories) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
        $raws_categories_list = get_select_tree($raws_categories, $raw->raws_product->raws_category_id, null, null);
        // dd($raws_categories_list);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_raws_products = operator_right('raws_products', false, 'index');
        // dd($answer_raws_products);

        // Группы товаров
        $raws_products_list = RawsProduct::where('raws_category_id', $raw->raws_product->raws_category_id)
        ->orderBy('sort', 'asc')
        ->get()
        ->pluck('name', 'id');

        // $raws_products_list = rawsProduct::moderatorLimit($answer_raws_products)
        // ->companiesLimit($answer_raws_products)
        // ->authors($answer_raws_products)
        // ->systemItem($answer_raws_products) // Фильтр по системным записям
        // ->where('raws_category_id', $cur_raws->raws_product->raws_category_id)
        // ->orderBy('sort', 'asc')
        // ->get()
        // ->pluck('name', 'id');
        // dd($raws_products_list);

        // dd($type);

        $raws_category = $raw->raws_product->raws_category;



        // $raws_category_compositions = [];
        // foreach ($raws_category->compositions as $composition) {
        //     $raws_category_compositions[] = $composition->id;
        // }


        // dd($raws_category);

        // $type = $raw->goods_product->goods_category->type;

        // if ($goods_category->type == 'goods') {
        //     if ($goods_category->status == 'one') {
        //         $type = ['raws'];
        //     } else {
        //         $type = ['goods'];
        //     }
        // }

        // if ($goods_category->type == 'raws') {
        //     $type = [];
        // }

        // if ($goods_category->type == 'goods') {
        //     if ($goods_category->status == 'one') {
        //         $type = ['staff'];
        //     } else {
        //         $type = ['goods'];
        //     }
        // }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_raws_modes = operator_right('raws_modes', false, 'index');

        $raws_modes = RawsMode::with(['raws_categories' => function ($query) use ($answer_raws_categories) {
            $query->with(['raws_products' => function ($query) {
                $query->with(['raws' => function ($query) {
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


        // dd($raws_modes_list);

        // // dd($raws_modes_list);

        // // dd($product->raws_group->raws_category->type);
        if ($raw->metrics_values_count > 0) {
            $metrics_values = [];
            foreach ($raw->metrics_values as $metric) {
                $metrics_values[$metric->id][] = $metric->pivot->value;
            }
        } else {
            $metrics_values = null;
        }


        // $compositions_values = $cur_raws->compositions_values->keyBy('product_id');
        // dd($metrics_values);
        // // dd($compositions_values->where('product_id', 4));

        // $type = $raw->goods_product->goods_category->type;

        // dd($raw->goods_product->goods_category->compositions);

        // foreach ($raw->goods_product->goods_category->compositions as $composition) {
        //     dd($composition->name);
        // }


        // Получаем настройки по умолчанию
        $settings = config()->get('settings');
        // dd($settings);

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

        // dd($settings_album);

        // Инфо о странице
        $page_info = pageInfo('raws');

        // dd($raw);

        return view('raws.edit', compact('raw', 'page_info', 'raws_categories_list', 'raws_products_list', 'manufacturers_list', 'raws_modes_list', 'raws_category_compositions', 'metrics_values', 'compositions_values', 'settings', 'settings_album'));
    }

    public function update(Request $request, $id)
    {

        if (isset($request->metrics)) {
            $metrics_count = count($request->metrics);
        } else {
            $metrics_count = 0;
        }
        
        // $compositions_count = count($request->compositions);

        // Если снят флаг черновика
        if (empty($request->template)) {

            // Проверка на наличие артикула
            // Вытаскиваем артикулы продукции с нужным нам числом метрик и составов
            // $goods = raw::with('metrics_values', 'compositions_values')
            // ->where('product_id', $request->product_id)
            // ->where(['metrics_count' => $metrics_count, 'compositions_count' => $compositions_count])
            // ->get();

            $raws = Raw::with('metrics_values')
            ->withCount('metrics_values')
            ->where('raws_product_id', $request->raws_product_id)
            ->where('metrics_count', $metrics_count)
            ->get();
            // dd($raws);

            // Создаем массив совпадений
            $coincidence = [];

            // dd($request);


            // Сравниваем метрики
            $metrics_array = [];
            foreach ($raws as $raw) {

                // dd($raw);
                foreach ($raw->metrics_values as $metric) {
                // dd($metric);
                    $metrics_array[$raw->id][$metric->id][] = $metric->pivot->value;
                }
            }
            // dd($metrics_array);

            $metrics_values[$id] = $request->metrics;
            // dd($metrics_values);

            if ($metrics_values == $metrics_array) {
                // Если значения метрик совпали, создаюм ключ метрик
                $coincidence['metric'] = 1;
            }
            // dd($coincidence);
            // dd($request->compositions);

            // $compositions_values = [];
            // foreach ($request->compositions as $composition_id => $value) {
            //     // dd($value['value']);
            //     $compositions_values[$id][$value['raw']] = $value['count'];
            // }
            // // dd($compositions_values);

            // // Сравниваем составы
            // $compositions_array = [];
            // foreach ($goods as $raw) {
            //     foreach ($raw->compositions_values as $composition) {
            //         $compositions_array[$raw->id][$composition->id] = $composition->pivot->value;
            //     }
            // }
            // dd($compositions_array);

            // if ($compositions_values == $compositions_array) {
            //     // Если значения составов совпали, создаюм ключ составов
            //     $coincidence['composition'] = 1;
            // }

            // Проверяем наличие ключей в массиве
            // if ((array_key_exists('metric', $coincidence) && array_key_exists('composition', $coincidence)) || (array_key_exists('metric', $coincidence) && $raw->product->products_category->compositions) || (array_key_exists('composition', $coincidence) && $raw->product->products_category->metrics)) {
            //     // Если ключи присутствуют, даем ошибку
            //     $result = [
            //         'error_status' => 1,
            //         'error_message' => 'Такой артикул уже существует!',
            //     ];

            //     echo json_encode($result, JSON_UNESCAPED_UNICODE);
            // }

            if (array_key_exists('metric', $coincidence)) {
                // Если ключи присутствуют, даем ошибку
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Такой артикул уже существует!',
                ];

                echo json_encode($result, JSON_UNESCAPED_UNICODE);
            }

            // dd($coincidence);
        }

        // Если что то не совпало, пишем новый артикул

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $raw = Raw::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raw);

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
            
            

            $directory = $company_id.'/media/raws/'.$raw->id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($raw->photo_id) {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, $raw->photo_id, $settings);

            } else {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);

            }
            $photo = $array['photo'];

            $raw->photo_id = $photo->id;
        } 

        // Наполняем сущность данными
        $raw->raws_product_id = $request->raws_product_id;
        $raw->name = $request->name;

        $raw->manually = $request->manually;
        // $raw->external = $request->external;
        $raw->cost = $request->cost;
        $raw->price = $request->price;

        $raw->description = $request->description;

        $raw->manufacturer_id = $request->manufacturer_id;

        $raw->metrics_count = $metrics_count;
        // $raw->compositions_count = $compositions_count;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false) {
            $raw->moderation = 1;
        }

        $raw->sail_status = $request->sail_status;

        // Системная запись
        $raw->system_item = $request->system_item;

        $raw->display = $request->display;
        $raw->draft = $request->draft;
        $raw->company_id = $company_id;
        $raw->author_id = $user_id;
        $raw->save();

        if ($raw) {

            if ($raw->draft == 1) {



            //     // dd($metrics_insert);
            //     if (isset($request->compositions)) {
            //         $compositions_insert = [];
            //         foreach ($request->compositions as $composition_id => $value) {
            //             // dd($value['value']);
            //             $compositions_insert[$value['raw']]['entity'] = 'goods';
            //             $compositions_insert[$value['raw']]['value'] = $value['count'];
            //         }

            //         // Пишем состав
            //         $raw->compositions_values()->attach($compositions_insert);
            //     }
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
                            // $metrics_insert[$metric_id]['entity'] = 'metrics';
                            // $metrics_insert[$metric_id]['value'] = $value; 
                    }
                }
                    // dd($metrics_insert);

                    // Пишем метрики
                    // $raw->metrics_values()->attach($metrics_insert);
            }

            // $result = [
            //     'error_status' => 0,
            // ];

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
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'delete');

        // ГЛАВНЫЙ ЗАПРОС:
        $raw = Raw::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('delete', $raw);

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
        $raw = Raw::with('album.photos')->moderatorLimit($answer)->findOrFail($request->raw_id);
        // dd($product);

        // Подключение политики
        $this->authorize(getmethod('edit'), $raw);

        return view('raws.photos', compact('raw'));

    }

}
