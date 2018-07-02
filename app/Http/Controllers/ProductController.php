<?php

namespace App\Http\Controllers;

// Модели
use App\Product;
use App\User;
use App\ProductsCategory;
use App\ProductsMode;
use App\UnitsCategory;
use App\Company;
use App\Photo;
use App\Album;
use App\AlbumEntity;
use App\Property;
use App\Metric;
use App\Article;
use App\Unit;
use App\Value;
use App\Booklist;
use App\Entity;
use App\List_item;


// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;

// Политика
use App\Policies\ProductPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DB;

// Специфические классы 
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\ImageManagerStatic as Image;

// Транслитерация
use Transliterate;

// На удаление
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'products';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Product::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // --------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------------------------------------

        $products = Product::with('author', 'company', 'products_category', 'unit', 'country')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        ->filter($request, 'company_id')
        ->filter($request, 'products_category_id')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // dd($products);

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ----------------------------------------------------------------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        $filter_query = Product::with('author', 'company', 'products_category')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->get();
        // dd($filter_query);

        $filter['status'] = null;

        $filter = addFilter($filter, $filter_query, $request, 'Выберите автора:', 'author', 'author_id');
        $filter = addFilter($filter, $filter_query, $request, 'Выберите компанию:', 'company', 'company_id');
        $filter = addFilter($filter, $filter_query, $request, 'Выберите категорию:', 'products_category', 'products_category_id');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // ---------------------------------------------------------------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('products.index', compact('products', 'page_info', 'product', 'filter'));
    }

    public function types(Request $request, $type)
    {

        // Подключение политики
        $this->authorize('index', Product::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // --------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------------------------------------

        $products = Product::with('author', 'company', 'products_category', 'unit', 'country')
        ->whereHas('products_category', function ($query) use ($type) {
            $query->where('type', $type);
        })
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        ->filter($request, 'company_id')
        ->filter($request, 'products_category_id')
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
        $page_info = pageInfo('products/'.$type);

        return view('products.index', compact('products', 'page_info', 'product', 'filter', 'type'));
    }

    public function create(Request $request, $type)
    {

        // Подключение политики
        $this->authorize(__FUNCTION__, Product::class);

        $product = new Product;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_products_categories = operator_right('products_categories', false, 'index');

        // Категории
        $products_categories = ProductsCategory::moderatorLimit($answer_products_categories)
        ->companiesLimit($answer_products_categories)
        ->authors($answer_products_categories)
        ->systemItem($answer_products_categories) // Фильтр по системным записям
        ->whereHas('products_mode', function ($query) use ($type) {
            $query->whereType($type);
        })
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
        $products_categories_list = get_select_tree($products_categories, null, null, null);
        // dd($countries_list);

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

        return view('products.create', compact('product', 'products_categories_list', 'units_categories_list'));
    }

    public function store(ProductRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Product::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Наполняем сущность данными
        $product = new Product;
        $product->name = $request->name;

        $product->products_category_id = $request->products_category_id;
        // $product->unit_id = $request->unit_id;
        
        // Автоматически отправляем запись на модерацию
        $product->moderation = 1;

        // Модерация и системная запись
        $product->system_item = $request->system_item;

        $product->company_id = $company_id;
        $product->author_id = $user_id;
        $product->save();

        if ($product) {


                // Отправляем на редактирование записи
                return Redirect('/products/'.$product->id.'/edit');

        } else {
            abort(403, 'Ошибка записи товара');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // ГЛАВНЫЙ ЗАПРОС:
        $answer_products = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        $product = Product::with(['products_category.metrics', 'products_category.compositions.products', 'album.photos'])->moderatorLimit($answer_products)->findOrFail($id);

        // if ($product->metrics_count > 0) {
        //     dd($product->compositions_count);
        // }

        // dd($product);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_products_categories = operator_right('products_categories', false, 'index');

        // Главный запрос
        $products_categories = ProductsCategory::moderatorLimit($answer_products_categories)
        ->companiesLimit($answer_products_categories)
        ->authors($answer_products_categories)
        ->systemItem($answer_products_categories) // Фильтр по системным записям
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $products_categories_list = get_select_tree($products_categories, $product->products_category_id, null, null);

        // dd($products_categories_list);

        // $product_metrics = [];
        // foreach ($product->metrics as $metric) {
        //     $product_metrics[] = $metric->id;
        // }
        // // dd($product_metrics);

        // $product_compositions = [];
        // foreach ($product->compositions as $composition) {
        //     $product_compositions[] = $composition->id;
        // }

        // dd($product_compositions);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $product);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer_units_categories = operator_right('units_categories', false, 'index');

        // // Главный запрос
        // $units_categories_list = UnitsCategory::with(['units' => function ($query) {
        //     $query->pluck('name', 'id');
        // }])
        // ->moderatorLimit($answer_units_categories)
        // ->companiesLimit($answer_units_categories)
        // ->authors($answer_units_categories)
        // ->systemItem($answer_units_categories) // Фильтр по системным записям
        // ->template($answer_units_categories)
        // ->orderBy('sort', 'asc')
        // ->get()
        // ->pluck('name', 'id');

        // $units_list = Unit::where('units_category_id', $product->unit->units_category_id)->get()->pluck('name', 'id');

        // // dd($units_categories_list[$product->unit->units_category_id]);

        // // Получаем данные для авторизованного пользователя
        // $user = $request->user();

        // // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer_properties = operator_right('properties', false, 'index');

        // $answer_metrics = operator_right('metrics', false, 'index');

        // $properties = Property::moderatorLimit($answer_properties)
        // ->companiesLimit($answer_properties)
        // ->authors($answer_properties)
        // ->systemItem($answer_properties) // Фильтр по системным записям
        // ->template($answer_properties)
        // ->with(['metrics' => function ($query) use ($answer_metrics) {
        //     $query->with('values')->moderatorLimit($answer_metrics)
        //     ->companiesLimit($answer_metrics)
        //     ->authors($answer_metrics)
        //     ->systemItem($answer_metrics); // Фильтр по системным записям 
        // }])
        // ->withCount('metrics')
        // ->orderBy('sort', 'asc')
        // ->get();

        // // dd($properties);


        // $properties_list = $properties->pluck('name', 'id');
        // // dd($properties_list);

        // // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer_manufacturers = operator_right('companies', false, 'index');

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Главный запрос
        $company = Company::with('manufacturers')
        ->findOrFail($user->company_id);
        // dd($company);

        $manufacturers_list = $company->manufacturers->pluck('name', 'id');
        // dd($manufacturers_list);

        $photo = New Photo;

        // // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer_products_categories = operator_right('products_categories', false, 'index');

        // // Категории
        // $products_categories = ProductsCategory::moderatorLimit($answer_products_categories)
        // ->companiesLimit($answer_products_categories)
        // ->authors($answer_products_categories)
        // ->systemItem($answer_products_categories) // Фильтр по системным записям
        // ->whereHas('products_mode', function ($query) {
        //     $query->whereType('goods');
        // })
        // ->orderBy('sort', 'asc')
        // ->get(['id','name','category_status','parent_id'])
        // ->keyBy('id')
        // ->toArray();

        // // dd($products_categories);

        // // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        // $products_categories_list = get_select_tree($products_categories, $product->products_category_id, null, null);

       

        

        // // // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer_products_modes = operator_right('products_modes', false, 'index');

        // $products_modes = ProductsMode::with(['products_categories' => function ($query) use ($answer_products_categories) {
        //     $query->with('articles')
        //     ->withCount('articles')
        //     ->moderatorLimit($answer_products_categories)
        //     ->companiesLimit($answer_products_categories)
        //     ->authors($answer_products_categories)
        //     ->systemItem($answer_products_categories) // Фильтр по системным записям
        //     ->template($answer_products_categories);
        // }])
        // ->moderatorLimit($answer_products_modes)
        // ->companiesLimit($answer_products_modes)
        // ->authors($answer_products_modes)
        // ->systemItem($answer_products_modes) // Фильтр по системным записям
        // ->template($answer_products_modes)
        // ->whereType('goods')
        // ->orderBy('sort', 'asc')
        // ->get();

        // $grouped_products_modes = $products_modes->groupBy('alias');

        // // Отдаем Ajax
        // if ($request->ajax()) {
        //     // echo json_encode($properties);
        //     return view('products.properties-list', ['properties' => $properties, 'product_metrics' => $product_metrics, 'properties_list' => $properties_list]);
        // }

        // , 'products_categories_list', 'units_categories_list', 'manufacturers_list', 'photo', 'product_metrics', 'product_compositions', 'grouped_products_modes', 'units_list'

        // dd($product);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('products.edit', compact('product', 'page_info', 'products_categories_list', 'manufacturers_list', 'photo'));
    }

    public function update(ProductRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $product = Product::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('update', $product);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        if ($request->hasFile('photo')) {

            $directory = $company_id.'/media/products/'.$product->id.'/img/';
            $name = 'avatar-'.time();

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($product->photo_id) {
                $array = save_photo($request, $user_id, $company_id, $directory, $name, null, $product->photo_id);

            } else {
                $array = save_photo($request, $user_id, $company_id, $directory, $name);
                
            }
            $photo = $array['photo'];

            $product->photo_id = $photo->id;
        } 

        $product->name = $request->name;

        $product->unit_id = $request->unit_id;
        

        $product->manufacturer_id = $request->manufacturer_id;

        $product->products_category_id = $request->products_category_id;
        $product->description = $request->description;

        // Модерация и системная запись
        $product->system_item = $request->system_item;
        $product->moderation = $request->moderation;

        // Отображение на сайте
        $product->display = $request->display;

        $product->editor_id = $user_id;
        $product->save();



        if ($product) {

            // dd($request->metrics);
            // Когда новость обновилась, смотрим пришедние для нее альбомы и сравниваем с существующими
            if (isset($request->metrics)) {

                $metrics = [];
                foreach ($request->metrics as $metric) {
                    $metrics[$metric] = [
                        'entity' => $this->entity_name,
                    ];
                }
                $product->metrics()->sync($metrics);

            } else {

                // Если удалили последний альбом для новости и пришел пустой массив
                $product->metrics()->detach();
            }


            return Redirect('/products');
        } else {
            abort(403, 'Ошибка обновления товара');
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $product = Product::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $product);

        $user = $request->user();

        if ($product) {
            $product->editor_id = $user->id;
            $product->save();

            // Удаляем сайт с обновлением
            $product = Product::destroy($id);
            if ($product) {
            // $relations = AlbumMedia::whereAlbum_id($id)->pluck('media_id')->toArray();
            // $photos = Photo::whereIn('id', $relations)->delete();
            // $media = AlbumMedia::whereAlbum_id($id)->delete();

                return Redirect('products');
            } else {
                abort(403, 'Ошибка при удалении товара');
            }
        } else {
            abort(403, 'Товар не найден');
        }
    }

    // Проверка наличия в базе
    public function product_check(Request $request)
    {
        $user = $request->user();

        // Проверка отдела в нашей базе данных
        $product = Product::where(['name' => $request->name, 'company_id' => $user->company_id])->first();

        // Если такое название есть
        if ($product) {
            $result = [
                'error_status' => 1,
            ];

        // Если нет
        } else {
            $result = [
                'error_status' => 0
            ];
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // Сортировка
    public function products_sort(Request $request)
    {
        $result = '';
        $i = 1;
        foreach ($request->products as $item) {

            $product = Product::findOrFail($item);
            $product->sort = $i;
            $product->save();

            $i++;
        }
    }

   

   

    // Добавление фоток
    public function product_photos(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod('index'));

        // ГЛАВНЫЙ ЗАПРОС:
        $product = Product::with('album.photos')->moderatorLimit($answer)->findOrFail($id);
        // dd($product);

        // Подключение политики
        $this->authorize(getmethod('edit'), $product);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('products.photos', compact('page_info', 'product'));

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

            $product = Product::findOrFail($request->id);

            if ($product->album_id == null) {
                $product->album_id = $album_id;
                $product->save();

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
        $product = Product::with('album.photos')->moderatorLimit($answer)->findOrFail($request->product_id);
        // dd($product);

        // Подключение политики
        $this->authorize(getmethod('edit'), $product);

        return view('products.photos', compact('product'));

    }


    public function delete_composition(Request $request)
    {

        $product = Product::findOrFail($request->product_id);
        $res = $product->compositions()->toggle([$request->id]);

        if ($res) {
            $result = [
                'error_status' => 0,
            ];
        } else {
            $result = [
                'error_message' => 'Не удалось удалить состав!',
                'error_status' => 1,
            ];
        }
        
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function get_product_inputs(Request $request)
    {

        $product = Product::with('metrics.property', 'compositions')->findOrFail(1);

        // $request->product_id

        dd($product);

        
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }


    // -------------------------------------- Exel ------------------------------------------
    public function products_download($type)
    {
        $data = Product::get(['name', 'article', 'cost'])->toArray();
        // dd($data);

        return Excel::create('products-'.Carbon::now()->format('d.m.Y'), function($excel) use ($data) {
            $excel->sheet('Продукция', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download($type);
    }

    public function products_import(Request $request)
    {
        if($request->hasFile('file')) {

            // Получаем данные для авторизованного пользователя
            $user = $request->user();

            // Смотрим компанию пользователя
            $company_id = $user->company_id;

            // Скрываем бога
            $user_id = hideGod($user);

            Excel::load($request->file('file')->getRealPath(), function ($reader) use ($user_id, $company_id){
                foreach ($reader->toArray() as $key => $row) {
                    $data['company_id'] = $company_id;
                    $data['name'] = $row['name'];
                    $data['article'] = $row['article'];
                    $data['cost'] = $row['cost'];
                    // $data['description'] = $row['description'];
                    $data['author_id'] = $user_id;

                    if(!empty($data)) {
                        DB::table('products')->insert($data);
                    }
                }
            });
        }

        return back();
    }
}
