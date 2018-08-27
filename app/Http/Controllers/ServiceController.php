<?php

namespace App\Http\Controllers;

// Модели
// use App\Article;
use App\Service;
use App\ServicesCategory;
use App\ServicesMode;
use App\ServicesProduct;
use App\ServicesArticle;
use App\Album;
use App\AlbumEntity;
use App\Photo;
use App\Catalog;

use App\Sector;

use App\EntitySetting;

use App\ArticleValue;

// Политика
use App\Policies\ServicePolicy;

// Куки
use Illuminate\Support\Facades\Cookie;

// Транслитерация
use Transliterate;


use Illuminate\Http\Request;

class ServiceController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'services';
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
        $this->authorize(getmethod(__FUNCTION__), Service::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // --------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------------------------------------

        $services = Service::with('author', 'company', 'services_article.services_product.services_category', 'catalogs.site')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        // ->filter($request, 'services_article_id', 'services_product_id')
        ->filter($request, 'services_category_id', 'services_article.services_product')
        // ->filter($request, 'company_id')
        ->filter($request, 'services_product_id', 'services_article')
        ->whereNull('archive')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // dd($services);

        // --------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА
        // --------------------------------------------------------------------------------------------------------

        $filter_query = Service::with('author', 'company', 'services_article.services_product.services_category')
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
        $filter = addFilter($filter, $filter_query, $request, 'Выберите категорию:', 'services_category', 'services_category_id', 'services_article.services_product', 'external-id-one-one');


        $filter = addFilter($filter, $filter_query, $request, 'Выберите группу:', 'services_product', 'services_product_id', 'services_article', 'external-id-one');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // dd($filter);
        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('services.index', compact('services', 'page_info', 'filter'));
    }

    public function search($text_fragment)
    {

        $entity_name = $this->entity_name;

        // Подключение политики
        $this->authorize('index', Service::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ------------------------------------------------------------------------------------------------------------

        $result_search = Service::with('author', 'company', 'services_product', 'services_product.services_category')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->where('name', 'LIKE', '%'.$text_fragment.'%')
        ->whereNull('archive')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        if($result_search->count()){

            return view('includes.search', compact('result_search', 'entity_name'));
        } else {

            return view('includes.search');
        }
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Service::class);

        // $service = new Service;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_services_categories = operator_right('services_categories', false, 'index');

        // Главный запрос
        $services_categories = ServicesCategory::withCount('services_products')
        ->with('services_products')
        ->moderatorLimit($answer_services_categories)
        ->companiesLimit($answer_services_categories)
        ->authors($answer_services_categories)
        ->systemItem($answer_services_categories) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get();

        if($services_categories->count() < 1){

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
            $ajax_error['text'] = "Для начала необходимо создать категории услуг. А уже потом будем добавлять сами услуги. Ок?";
            $ajax_error['link'] = "/admin/services_categories"; // Ссылка на кнопке
            $ajax_error['title_link'] = "Идем в раздел категорий"; // Текст на кнопке

            return view('ajax_error', compact('ajax_error'));
        }

        $services_products_count = $services_categories[0]->services_products_count;
        $parent_id = null;

        if ($request->cookie('conditions') != null) {

            $condition = Cookie::get('conditions');
            if(isset($condition['services_category'])) {
                $services_category_id = $condition['services_category'];

                $services_category = $services_categories->find($services_category_id);
                // dd($services_category);

                $services_products_count = $services_category->services_products_count;
                $parent_id = $services_category_id;
                // dd($services_products_count);
            }
        }

        // dd($services_categories);

        // dd($request->parent_id);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $services_categories_list = get_select_tree($services_categories->keyBy('id')->toArray(), $parent_id, null, null);
        // echo $services_categories_list;


        return view('services.create', compact('services_categories_list', 'services_products_count'));
    }

    public function store(Request $request)
    {

        // dd($request);
        $services_category_id = $request->services_category_id;

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        $name = $request->name;

        // dd($request);

        switch ($request->mode) {
            case 'mode-default':

            $services_product = ServicesProduct::where(['name' => $name, 'services_category_id' => $services_category_id])->first();

            if ($services_product) {
                $services_product_id = $services_product->id;
            } else {

                $services_product = new ServicesProduct;
                $services_product->name = $name;
                $services_product->services_category_id = $services_category_id;
                $services_product->unit_id = 26;
                // $services_product->unit_id = $request->unit_id;

                // Модерация и системная запись
                $services_product->system_item = $request->system_item;

                $services_product->display = 1;
                $services_product->company_id = $company_id;
                $services_product->author_id = $user_id;
                $services_product->save();

                if ($services_product) {
                    $services_product_id = $services_product->id;
                } else {
                    abort(403, 'Ошибка записи группы услуг');
                }
            }
            break;
            
            case 'mode-add':
            $service_product_name = $request->service_product_name;

            $services_product = ServicesProduct::where(['name' => $service_product_name, 'services_category_id' => $services_category_id])->first();

            if ($services_product) {
                $services_product_id = $services_product->id;
            } else {

                // Наполняем сущность данными
                $services_product = new ServicesProduct;

                $services_product->name = $service_product_name;
                $services_product->unit_id = 26;

                $services_product->services_category_id = $services_category_id;

                // Модерация и системная запись
                $services_product->system_item = $request->system_item;

                $services_product->display = 1;

                $services_product->company_id = $company_id;
                $services_product->author_id = $user_id;
                $services_product->save();

                if ($services_product) {
                    $services_product_id = $services_product->id;
                } else {
                    abort(403, 'Ошибка записи группы услуг');
                }
            }
            break;

            case 'mode-select':


            $services_product = ServicesProduct::findOrFail($request->services_product_id);

            $service_product_name = $services_product->name;
            $services_product_id = $services_product->id;
            break;
        }

        $services_article = new ServicesArticle;

        $services_article->services_product_id = $services_product_id;

        $services_article->company_id = $company_id;
        $services_article->author_id = $user_id;

        $services_article->name = $name;
        $services_article->save();

        if ($services_article) {

            $service = new Service;

            $service->price = $request->price;
            $service->company_id = $company_id;
            $service->author_id = $user_id;
            $service->draft = 1;
            $service->services_article()->associate($services_article);

            $service->save();

            if ($service) {

                 // Пишем сессию
                $mass = [
                    'services_category' => $services_category_id,
                ];
                Cookie::queue('conditions', $mass, 1440);

                if ($request->quickly == 1) {
                    return redirect('/admin/services');
                } else {
                    return redirect('/admin/services/'.$service->id.'/edit'); 
                }
            } else {
                abort(403, 'Ошибка записи услуги');
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
        $answer_services = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // $answer_services_articles = operator_right('services_articles', false, 'index');

        // dd($answer_services_infos);

        // $service = Service::with(['services_product.services_category' => function ($query) {
        //     $query->with(['metrics.property', 'metrics.property', 'compositions' => function ($query) {
        //         $query->with(['services' => function ($query) {
        //             $query->whereNull('draft');
        //         }]);
        //     }])
        //     ->withCount('metrics', 'compositions');
        // }, 'album.photos', 'company.manufacturers', 'metrics_values', 'compositions_values'])->withCount(['metrics_values', 'compositions_values'])->moderatorLimit($answer_services)->findOrFail($id);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $service = Service::with(['services_article.services_product.services_category', 'album.photos', 'company.manufacturers', 'photo', 'catalogs'])
        ->moderatorLimit($answer_services)
        ->findOrFail($id);
        // dd($service);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $service);

        $manufacturers_list = $service->company->manufacturers->pluck('name', 'id');
        // dd($manufacturers_list);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_services_categories = operator_right('services_categories', false, 'index');
        // dd($answer_services_categories);

        // Категории
        $services_categories = ServicesCategory::moderatorLimit($answer_services_categories)
        ->companiesLimit($answer_services_categories)
        ->authors($answer_services_categories)
        ->systemItem($answer_services_categories) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
        
        $services_categories_list = get_select_tree($services_categories, $service->services_article->services_product->services_category_id, null, null);
        // dd($services_categories_list);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_services_products = operator_right('services_products', false, 'index');
        // dd($answer_services_products);

        // Услуги
        $services_products_list = ServicesProduct::where('services_category_id', $service->services_article->services_product->services_category_id)
        ->orderBy('sort', 'asc')
        ->get()
        ->pluck('name', 'id');

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_catalogs = operator_right('catalogs', false, 'index');

        // $catalogs_list = Catalog::moderatorLimit($answer_catalogs)
        // ->companiesLimit($answer_catalogs)
        // ->systemItem($answer_catalogs) // Фильтр по системным записям
        // ->whereSite_id(2)
        // // ->orderBy('sort', 'asc')
        // ->get()
        // ->pluck('name', 'id');

        $catalogs = Catalog::moderatorLimit($answer_catalogs)
        ->companiesLimit($answer_catalogs)
        ->systemItem($answer_catalogs) // Фильтр по системным записям
        ->whereSite_id(2)
        // ->orderBy('sort', 'asc')
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

            // dd($items);
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

             // dd('lol');
            return $menu;
            
        }

        // dd($service->catalogs->implode('id', ', '));

        $parents = [];

        foreach ($service->catalogs as $catalog) {
            $parents[] = $catalog->id;
        }
        // dd($parents);

        // Получаем HTML разметку
        $catalogs_list = show_cats($catalogs_tree, '', $parents);

        // dd($catalogs_list);

        // $services_products_list = ServicesProduct::moderatorLimit($answer_services_products)
        // ->companiesLimit($answer_services_products)
        // ->authors($answer_services_products)
        // ->systemItem($answer_services_products) // Фильтр по системным записям
        // ->where('services_category_id', $service->services_product->services_category_id)
        // ->orderBy('sort', 'asc')
        // ->get()
        // ->pluck('name', 'id');
        // dd($services_products_list);

        // dd($type);

        // $services_category = $service->services_product->services_category;

        // $services_category_compositions = [];
        // foreach ($services_category->compositions as $composition) {
        //     $services_category_compositions[] = $composition->id;
        // }

        // $type = $service->services_product->services_category->type;

        // if ($services_category->type == 'goods') {
        //     if ($services_category->status == 'one') {
        //         $type = ['raws'];
        //     } else {
        //         $type = ['goods'];
        //     }
        // }

        // if ($services_category->type == 'raws') {
        //     $type = [];
        // }

        // if ($services_category->type == 'services') {
        //     if ($services_category->status == 'one') {
        //         $type = ['staff'];
        //     } else {
        //         $type = ['services'];
        //     }
        // }

        // // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer_services_modes = operator_right('services_modes', false, 'index');

        // $services_modes = ServicesMode::with(['services_categories' => function ($query) use ($answer_services_categories) {
        //     $query->with(['services_products' => function ($query) {
        //         $query->with(['services' => function ($query) {
        //             $query->whereNull('draft');
        //         }]);
        //     }])
        //     ->withCount('services_products')
        //     ->moderatorLimit($answer_services_categories)
        //     ->companiesLimit($answer_services_categories)
        //     ->authors($answer_services_categories)
        //     ->systemItem($answer_services_categories); // Фильтр по системным записям 
        // }])
        // ->moderatorLimit($answer_services_modes)
        // ->companiesLimit($answer_services_modes)
        // ->authors($answer_services_modes)
        // ->systemItem($answer_services_modes) // Фильтр по системным записям
        // ->template($answer_services_modes)
        // ->orderBy('sort', 'asc')
        // ->get()
        // ->toArray();

        // // dd($services_modes);

        // $services_modes_list = [];
        // foreach ($services_modes as $services_mode) {
        //     $services_categories_id = [];
        //     foreach ($services_mode['services_categories'] as $services_cat) {
        //         $services_categories_id[$services_cat['id']] = $services_cat;
        //     }
        //     // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        //     $services_cat_list = get_parents_tree($services_categories_id, null, null, null);


        //     $services_modes_list[] = [
        //         'name' => $services_mode['name'],
        //         'alias' => $services_mode['alias'],
        //         'services_categories' => $services_cat_list,
        //     ];
        // }

        // // dd($services_modes_list);

        // // dd($product->services_group->services_category->type);

        // $metrics_values = $service->metrics_values->keyBy('id');
        // $compositions_values = $service->compositions_values->keyBy('product_id');
        // // dd($metrics_values);
        // // dd($compositions_values->where('product_id', 4));

        // $type = $service->services_product->services_category->type;



        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('services.edit', compact('service', 'page_info', 'services_categories_list', 'services_products_list', 'manufacturers_list', 'type', 'services_modes_list', 'services_category_compositions', 'metrics_values', 'compositions_values', 'catalogs_list'));
    }

    public function update(Request $request, $id)
    {

        // dd($request);
        // $metrics_count = count($request->metrics);
        // $compositions_count = count($request->compositions);

        // Если снят флаг черновика
        // if (empty($request->draft)) {

        //     // Проверка на наличие артикула
        //     // Вытаскиваем артикулы продукции с нужным нам числом метрик и составов
        //     $services = Service::with('metrics_values', 'compositions_values')
        //     ->where('product_id', $request->product_id)
        //     ->where(['metrics_count' => $metrics_count, 'compositions_count' => $compositions_count])
        //     ->get();

        //      $services = Service::with('metrics_values', 'compositions_values')
        //     ->where('product_id', $request->product_id)
        //     ->where(['metrics_count' => $metrics_count, 'compositions_count' => $compositions_count])
        //     ->get();
        //     // dd($services);

        //     // Создаем массив совпадений
        //     $coincidence = [];

        //     // dd($request->metrics);
        //     $metrics_values = [];
        //     foreach ($request->metrics as $metric_id => $value) {
        //         // dd($value['value']);
        //         $metrics_values[$id][$metric_id] = $value['value'];
        //     }
        //     // dd($metrics_values);

        //     // Сравниваем метрики
        //     $metrics_array = [];
        //     foreach ($services as $service) {
        //         foreach ($service->metrics_values as $metric) {
        //         // dd($metric);
        //             $metrics_array[$service->id][$metric->id] = $metric->pivot->value;
        //         }
        //     }
        //     // dd($metrics_array);

        //     if ($metrics_values == $metrics_array) {
        //         // Если значения метрик совпали, создаюм ключ метрик
        //         $coincidence['metric'] = 1;
        //     }
        //     // dd($request->compositions);

        //     $compositions_values = [];
        //     foreach ($request->compositions as $composition_id => $value) {
        //         // dd($value['value']);
        //         $compositions_values[$id][$value['service']] = $value['count'];
        //     }
        //     // dd($compositions_values);

        //     // Сравниваем составы
        //     $compositions_array = [];
        //     foreach ($services as $service) {
        //         foreach ($service->compositions_values as $composition) {
        //             $compositions_array[$service->id][$composition->id] = $composition->pivot->value;
        //         }
        //     }
        //     // dd($compositions_array);

        //     if ($compositions_values == $compositions_array) {
        //         // Если значения составов совпали, создаюм ключ составов
        //         $coincidence['composition'] = 1;
        //     }

        //     // Проверяем наличие ключей в массиве
        //     if ((array_key_exists('metric', $coincidence) && array_key_exists('composition', $coincidence)) || (array_key_exists('metric', $coincidence) && $service->product->products_category->compositions) || (array_key_exists('composition', $coincidence) && $service->product->products_category->metrics)) {
        //         // Если ключи присутствуют, даем ошибку
        //         $result = [
        //             'error_status' => 1,
        //             'error_message' => 'Такой артикул уже существует!',
        //         ];

        //         echo json_encode($result, JSON_UNESCAPED_UNICODE);
        //     }

        //     // dd($coincidence);
        // }

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
        $service = Service::with('services_article')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $service);

        if ($request->hasFile('photo')) {

            // Вытаскиваем настройки
            // Вытаскиваем базовые настройки сохранения фото
            $settings = config()->get('settings');

            // Начинаем проверку настроек, от компании до альбома
            // Смотрим общие настройки для сущности
            $get_settings = EntitySetting::where(['entity' => $this->entity_name])->first();

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

            $directory = $company_id.'/media/services/'.$service->id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id компании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($service->photo_id) {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, $service->photo_id, $settings);
            } else {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);
            }

            $photo = $array['photo'];

            $service->photo_id = $photo->id;
        } 

        // Наполняем сущность данными
        // $service->services_product_id = $request->services_product_id;
        // $service->name = $request->name;

        // -------------------------------------------------------------------------------------------------
        // ПЕРЕНОС ГРУППЫ УСЛУГИ В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ

        // Получаем выбранную категорию со старницы (то, что указал пользователь)
        $services_category_id = $request->services_category_id;

        // Смотрим: была ли она изменена
        if($service->services_article->services_product->services_category_id != $services_category_id){

            // Была изменена! Переназначаем категорию группе:
            $item = ServicesProduct::where('id', $service->services_article->services_product_id)->update(['services_category_id' => $services_category_id]);
        };



        // -------------------------------------------------------------------------------------------------
        // ПЕРЕНОС ТОВАРА В ДРУГУЮ ГРУППУ ПОЛЬЗОВАТЕЛЕМ
        // Важно! Важно проверить, соответствеут ли группа в которую переноситься товар, метрикам самого товара
        // Если не соответствует - дать отказ. Если соответствует - осуществить перенос


        // Тут должен быть код проверки !!! 


        // А, пока изменяем без проверки
        $service->services_article->services_product_id = $request->services_product_id;



        // $service->manufacturer_id = $request->manufacturer_id;

        // $service->metrics_count = $metrics_count;
        // $service->compositions_count = $compositions_count;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false) {
            $service->moderation = 1;
        }

        // Системная запись
        $service->system_item = $request->system_item;

        $service->description = $request->description;
        $service->display = $request->display;
        $service->draft = $request->draft;

        $service->manually = $request->manually;
        $service->cost = $request->cost;
        $service->price = $request->price;


        $service->company_id = $company_id;
        $service->author_id = $user_id;
        $service->save();

        if ($service) {

            if ($service->services_article->name != $request->name) {
                $services_article = $service->services_article;
                $services_article->name = $request->name;
                $services_article->save();
            }

            if (isset($request->catalogs)) {

                $mass = [];
                foreach ($request->catalogs as $catalog) {
                    $mass[$catalog] = ['display' => 1];
                }

                // dd($mass);
                $service->catalogs()->sync($mass);
            } else {
                $service->catalogs()->detach();
            }



            // $service->external = $request->external;




            // if ($service->draft == 1) {

            //     if (isset($request->metrics)) {
            //         $metrics_insert = [];
            //         foreach ($request->metrics as $metric_id => $value) {
            //             // dd($value['value']);
            //             $metrics_insert[$metric_id]['entity'] = 'metrics';
            //             $metrics_insert[$metric_id]['value'] = $value['value'];
            //         }

            //         // Пишем метрики
            //         $service->metrics_values()->attach($metrics_insert);
            //     }

            //     // dd($metrics_insert);
            //     if (isset($request->compositions)) {
            //         $compositions_insert = [];
            //         foreach ($request->compositions as $composition_id => $value) {
            //             // dd($value['value']);
            //             $compositions_insert[$value['service']]['entity'] = 'services';
            //             $compositions_insert[$value['service']]['value'] = $value['count'];
            //         }

            //         // Пишем состав
            //         $service->compositions_values()->attach($compositions_insert);
            //     }
            // }

            // $result = [
            //     'error_status' => 0,
            // ];

            // echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return Redirect('/admin/services');

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
        $service = Service::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('delete', $service);

        if ($service) {

            // Получаем пользователя
            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);

            $service->editor_id = $user_id;
            $service->archive = 1;
            $service->save();

            if ($service) {
                return Redirect('/admin/services');
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

        foreach ($request->services as $item) {
            Service::where('id', $item)->update(['sort' => $i]);
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

        $item = Service::where('id', $request->id)->update(['system_item' => $system]);

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

        $item = Service::where('id', $request->id)->update(['display' => $display]);

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
        return view('products.service-form', compact('product'));

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

            $service = Service::findOrFail($request->id);

            if ($service->album_id == null) {
                $service->album_id = $album_id;
                $service->save();

                if (!$service) {
                    abort(403, 'Ошибка записи альбома в продукцию');
                }
            }

            // Вытаскиваем настройки
            // Вытаскиваем базовые настройки сохранения фото
            $settings = config()->get('settings');

            // Начинаем проверку настроек, от компании до альбома
            // Смотрим общие настройки для сущности
            $get_settings = EntitySetting::where(['entity' => $this->entity_name])->first();

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
            $array = save_photo($request, $directory, $alias.'-'.time(), $album_id, null, $settings);

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
        $service = Service::with('album.photos')->moderatorLimit($answer)->findOrFail($request->service_id);
        // dd($product);

        // Подключение политики
        $this->authorize(getmethod('edit'), $service);

        return view('services.photos', compact('service'));

    }

}
