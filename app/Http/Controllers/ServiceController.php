<?php

namespace App\Http\Controllers;

// Модели
// use App\Article;
use App\Service;
use App\ServicesCategory;
use App\ServicesMode;
use App\Album;
use App\AlbumEntity;
use App\Photo;

use App\ArticleValue;


// Транслитерация
use Transliterate;


use Illuminate\Http\Request;

class ServiceController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'services';
    protected $entity_dependence = false;

    public function index()
    {

        // Подключение политики
        $this->authorize('index', Service::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // --------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------------------------------------

        $services = Service::with('author', 'company', 'services_product')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        // ->booklistFilter($request)
        // ->filter($request, 'author_id')
        // ->filter($request, 'company_id')
        // ->filter($request, 'products_category_id')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // dd($products);

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ----------------------------------------------------------------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        // $filter_query = Product::with('author', 'company', 'products_category')
        // ->moderatorLimit($answer)
        // ->companiesLimit($answer)
        // ->authors($answer)
        // ->systemItem($answer) // Фильтр по системным записям
        // ->get();
        // // dd($filter_query);

        // $filter['status'] = null;

        // $filter = addFilter($filter, $filter_query, $request, 'Выберите автора:', 'author', 'author_id');
        // $filter = addFilter($filter, $filter_query, $request, 'Выберите компанию:', 'company', 'company_id');
        // $filter = addFilter($filter, $filter_query, $request, 'Выберите категорию:', 'products_category', 'products_category_id');

        // // Добавляем данные по спискам (Требуется на каждом контроллере)
        // $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // dd($type);
        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('services.index', compact('services', 'page_info'));
    }

    public function types(Request $request)
    {

        // Подключение политики
        $this->authorize('index', Service::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // --------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------------------------------------

        $services = Service::with('author', 'company', 'product')
        ->whereHas('product', function ($query) use ($type) {
            $query->whereHas('products_category', function ($query) use ($type) {
                $query->where('type', $type);
            });
        })
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        // ->booklistFilter($request)
        // ->filter($request, 'author_id')
        // ->filter($request, 'company_id')
        // ->filter($request, 'products_category_id')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // dd($products);

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ----------------------------------------------------------------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        // $filter_query = Product::with('author', 'company', 'products_category')
        // ->moderatorLimit($answer)
        // ->companiesLimit($answer)
        // ->authors($answer)
        // ->systemItem($answer) // Фильтр по системным записям
        // ->get();
        // // dd($filter_query);

        // $filter['status'] = null;

        // $filter = addFilter($filter, $filter_query, $request, 'Выберите автора:', 'author', 'author_id');
        // $filter = addFilter($filter, $filter_query, $request, 'Выберите компанию:', 'company', 'company_id');
        // $filter = addFilter($filter, $filter_query, $request, 'Выберите категорию:', 'products_category', 'products_category_id');

        // // Добавляем данные по спискам (Требуется на каждом контроллере)
        // $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // dd($type);
        // Инфо о странице
        $page_info = pageInfo('services/'.$type);

        return view('services.index', compact('services', 'page_info', 'product', 'type'));
    }

    public function create(Request $request, $type)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Service::class);

        $service = new Service;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_products_categories = operator_right('products_categories', false, 'index');

        // Главный запрос
        $products_categories = ProductsCategory::moderatorLimit($answer_products_categories)
        ->companiesLimit($answer_products_categories)
        ->authors($answer_products_categories)
        ->systemItem($answer_products_categories) // Фильтр по системным записям
        ->where('type', $type)
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $products_categories_list = get_select_tree($products_categories, $request->parent_id, null, null);
        // echo $products_categories_list;


        return view('services.create', compact('service', 'products_categories_list', 'type'));
    }

    public function store(Request $request)
    {
        $products_category_id = $request->products_category_id;

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        if ($request->mode == 'mode-add') {

            $name = $request->name;

            $product = Product::where(['name' => $name, 'products_category_id' => $products_category_id])->first();

            if ($product) {
                $product_id = $product->id;
            } else {

                // Наполняем сущность данными
                $product = new Product;

                $product->name = $name;
                $product->unit_id = $request->unit_id;

                $product->products_category_id = $products_category_id;

                // Модерация и системная запись
                $product->system_item = $request->system_item;

                $product->display = $request->display;

                $product->company_id = $company_id;
                $product->author_id = $user_id;
                $product->save();

                if ($product) {
                    $product_id = $product->id;
                } else {
                    abort(403, 'Ошибка записи группы товаров');
                }
            }
        } else {

            $product = Product::findOrFail($request->product_id);

            $name = $product->name;
            $product_id = $product->id;
        }

        $service = new Service;

        $service->template = 1;

        $service->product_id = $product_id;

        $service->company_id = $company_id;
        $service->author_id = $user_id;
        $service->save();

        $service->name = $name.' ('. $service->id .')';
        $service->save();

        if ($service) {

            return Redirect('/services/'.$service->id.'/edit');
        } else {
            abort(403, 'Ошибка записи артикула');
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

        $service = Service::with(['product.products_category' => function ($query) {
            $query->with(['metrics.property', 'metrics.property', 'compositions' => function ($query) {
                $query->with(['services' => function ($query) {
                    $query->whereNull('template');
                }]);
            }])
            ->withCount('metrics', 'compositions');
        }, 'album.photos', 'company.manufacturers', 'metrics_values', 'compositions_values'])->withCount(['metrics_values', 'compositions_values'])->moderatorLimit($answer_services)->findOrFail($id);
        // dd($service);

        $manufacturers_list = $service->company->manufacturers->pluck('name', 'id');
        // dd($manufacturers_list);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $service);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_products_categories = operator_right('products_categories', false, 'index');

        // Категории
        $products_categories = ProductsCategory::moderatorLimit($answer_products_categories)
        ->companiesLimit($answer_products_categories)
        ->authors($answer_products_categories)
        ->systemItem($answer_products_categories) // Фильтр по системным записям
        ->whereType($service->product->products_category->type)
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
        $products_categories_list = get_select_tree($products_categories, $service->product->products_category_id, null, null);
        // dd($products_categories_list);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_products = operator_right($this->entity_name, $this->entity_dependence, 'index');

        // Категории
        $products_list = Product::moderatorLimit($answer_products)
        ->companiesLimit($answer_products)
        ->authors($answer_products)
        ->systemItem($answer_products) // Фильтр по системным записям
        ->where('products_category_id', $service->product->products_category_id)
        ->orderBy('sort', 'asc')
        ->get()
        ->pluck('name', 'id');
        // dd($products_list);

        // dd($type);

        $products_category = $service->product->products_category;

        $products_category_compositions = [];
        foreach ($products_category->compositions as $composition) {
            $products_category_compositions[] = $composition->id;
        }

        $type = $service->product->products_category->type;

        if ($products_category->type == 'goods') {
            if ($products_category->status == 'one') {
                $type = ['raws'];
            } else {
                $type = ['goods'];
            }
        }

        if ($products_category->type == 'raws') {
            $type = [];
        }

        if ($products_category->type == 'services') {
            if ($products_category->status == 'one') {
                $type = ['staff'];
            } else {
                $type = ['services'];
            }
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_products_modes = operator_right('products_modes', false, 'index');

        $products_modes = ProductsMode::with(['products_categories' => function ($query) use ($answer_products_categories) {
            $query->with(['products' => function ($query) {
                $query->with(['services' => function ($query) {
                    $query->whereNull('template');
                }]);
            }])
            ->withCount('products')
            ->moderatorLimit($answer_products_categories)
            ->companiesLimit($answer_products_categories)
            ->authors($answer_products_categories)
            ->systemItem($answer_products_categories); // Фильтр по системным записям 
        }])
        ->moderatorLimit($answer_products_modes)
        ->companiesLimit($answer_products_modes)
        ->authors($answer_products_modes)
        ->systemItem($answer_products_modes) // Фильтр по системным записям
        ->template($answer_products_modes)
        ->whereIn('type', $type)
        ->orderBy('sort', 'asc')
        ->get()
        ->toArray();

        // dd($products_modes);

        $products_modes_list = [];
        foreach ($products_modes as $products_mode) {
            $products_categories_id = [];
            foreach ($products_mode['products_categories'] as $products_cat) {
                $products_categories_id[$products_cat['id']] = $products_cat;
            }
            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
            $products_cat_list = get_parents_tree($products_categories_id, null, null, null);


            $products_modes_list[] = [
                'name' => $products_mode['name'],
                'alias' => $products_mode['alias'],
                'products_categories' => $products_cat_list,
            ];
        }

        // dd($products_modes_list);

        // dd($product->products_group->products_category->type);

        $metrics_values = $service->metrics_values->keyBy('id');
        $compositions_values = $service->compositions_values->keyBy('product_id');
        // dd($metrics_values);
        // dd($compositions_values->where('product_id', 4));

        $type = $service->product->products_category->type;

        // Инфо о странице
        $page_info = pageInfo('services/'.$service->product->products_category->type);

        return view('services.edit', compact('service', 'page_info', 'products_categories_list', 'products_list', 'manufacturers_list', 'type', 'products_modes_list', 'products_category_compositions', 'metrics_values', 'compositions_values'));
    }

    public function update(Request $request, $id)
    {

        // dd($request);
        $metrics_count = count($request->metrics);
        $compositions_count = count($request->compositions);

        // Если снят флаг черновика
        if (empty($request->template)) {

            // Проверка на наличие артикула
            // Вытаскиваем артикулы продукции с нужным нам числом метрик и составов
            $services = Service::with('metrics_values', 'compositions_values')
            ->where('product_id', $request->product_id)
            ->where(['metrics_count' => $metrics_count, 'compositions_count' => $compositions_count])
            ->get();
            // dd($services);

            // Создаем массив совпадений
            $coincidence = [];

            // dd($request->metrics);
            $metrics_values = [];
            foreach ($request->metrics as $metric_id => $value) {
                // dd($value['value']);
                $metrics_values[$id][$metric_id] = $value['value'];
            }
            // dd($metrics_values);

            // Сравниваем метрики
            $metrics_array = [];
            foreach ($services as $service) {
                foreach ($service->metrics_values as $metric) {
                // dd($metric);
                    $metrics_array[$service->id][$metric->id] = $metric->pivot->value;
                }
            }
            // dd($metrics_array);

            if ($metrics_values == $metrics_array) {
                // Если значения метрик совпали, создаюм ключ метрик
                $coincidence['metric'] = 1;
            }
            // dd($request->compositions);

            $compositions_values = [];
            foreach ($request->compositions as $composition_id => $value) {
                // dd($value['value']);
                $compositions_values[$id][$value['service']] = $value['count'];
            }
            // dd($compositions_values);

            // Сравниваем составы
            $compositions_array = [];
            foreach ($services as $service) {
                foreach ($service->compositions_values as $composition) {
                    $compositions_array[$service->id][$composition->id] = $composition->pivot->value;
                }
            }
            // dd($compositions_array);

            if ($compositions_values == $compositions_array) {
                // Если значения составов совпали, создаюм ключ составов
                $coincidence['composition'] = 1;
            }

            // Проверяем наличие ключей в массиве
            if ((array_key_exists('metric', $coincidence) && array_key_exists('composition', $coincidence)) || (array_key_exists('metric', $coincidence) && $service->product->products_category->compositions) || (array_key_exists('composition', $coincidence) && $service->product->products_category->metrics)) {
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
        $service = Service::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $service);

        // Наполняем сущность данными
        $service->product_id = $request->product_id;
        $service->name = $request->name;
        $service->external = $request->external;
        $service->cost = $request->cost;
        $service->price = $request->price;
        $service->manufacturer_id = $request->manufacturer_id;

        $service->metrics_count = $metrics_count;
        $service->compositions_count = $compositions_count;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false) {
            $service->moderation = 1;
        }

        // Системная запись
        $service->system_item = $request->system_item;

        $service->display = $request->display;
        $service->template = $request->template;
        $service->company_id = $company_id;
        $service->author_id = $user_id;
        $service->save();

        if ($service) {

            if ($service->template == 1) {

                if (isset($request->metrics)) {
                    $metrics_insert = [];
                    foreach ($request->metrics as $metric_id => $value) {
                        // dd($value['value']);
                        $metrics_insert[$metric_id]['entity'] = 'metrics';
                        $metrics_insert[$metric_id]['value'] = $value['value'];
                    }

                    // Пишем метрики
                    $service->metrics_values()->attach($metrics_insert);
                }
                
                // dd($metrics_insert);
                if (isset($request->compositions)) {
                    $compositions_insert = [];
                    foreach ($request->compositions as $composition_id => $value) {
                        // dd($value['value']);
                        $compositions_insert[$value['service']]['entity'] = 'services';
                        $compositions_insert[$value['service']]['value'] = $value['count'];
                    }

                    // Пишем состав
                    $service->compositions_values()->attach($compositions_insert);
                }
            }

            $result = [
                'error_status' => 0,
            ];

            // echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return Redirect('/services/'.$service->product->products_category->type);

        }
    }

    public function destroy($id)
    {
        //
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

                if (!$product) {
                    abort(403, 'Ошибка записи альбома в продукцию');
                }
            }

            $directory = $company_id.'/media/albums/'.$album_id.'/img/';
            $array = save_photo($request, $user_id, $company_id, $directory,  $alias.'-'.time(), $album_id);

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

    // Отображение на сайте
    public function ajax_display(Request $request)
    {

        if ($request->action == 'hide') {
          $display = null;
      } else {
          $display = 1;
      }

      $service = Service::findOrFail($request->id);
      $service->display = $display;
      $service->save();

      if ($service) {

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
}
