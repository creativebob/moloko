<?php

namespace App\Http\Controllers;

// Модели
use App\ServicesProduct;
use App\User;
use App\ServicesCategory;
use App\ServicesMode;
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
use App\Http\Requests\ServicesProductRequest;

// Политика
use App\Policies\ServicesProductPolicy;

// Общие классы
use Illuminate\Support\Facades\Cookie;
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

class ServicesProductController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'services_products';
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
        $this->authorize(getmethod(__FUNCTION__), ServicesProduct::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -----------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------

        $services_products = ServicesProduct::with('author', 'company', 'services_category')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        ->filter($request, 'company_id')
        ->filter($request, 'services_category_id')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);


        // ----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter_query = ServicesProduct::with('author', 'services_category')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        $filter['status'] = null;
        $filter['entity_name'] = $this->entity_name;

        $filter = addFilter($filter, $filter_query, $request, 'Выберите автора:', 'author', 'author_id', null, 'internal-id-one');
        $filter = addFilter($filter, $filter_query, $request, 'Выберите категорию:', 'services_category', 'services_category_id', null, 'internal-id-one');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // ----------------------------------------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('services_products.index', compact('services_products', 'page_info', 'filter'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(__FUNCTION__, ServicesProduct::class);

        $services_product = new ServicesProduct;

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_services_categories = operator_right('services_categories', false, 'index');

        // Категории
        $services_categories = ServicesCategory::moderatorLimit($answer_services_categories)
        ->companiesLimit($answer_services_categories)
        ->authors($answer_services_categories)
        ->systemItem($answer_services_categories)
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // dd($goods_categories);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $services_categories_list = get_select_tree($services_categories, 1, null, null);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('services_products.create', compact('services_product', 'page_info', 'services_categories_list'));
    }

    public function store(ServicesProductRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), ServicesProduct::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Наполняем сущность данными
        $services_product = new ServicesProduct;
        $services_product->name = $request->name;
        $services_product->description = $request->description;
        $services_product->services_category_id = $request->services_category_id;

        // Автоматически отправляем запись на модерацию
        // $product->moderation = 1;

        // Модерация и системная запись
        $services_product->system_item = $request->system_item;

        $services_product->display = $request->display;

        $services_product->company_id = $company_id;
        $services_product->author_id = $user_id;
        $services_product->save();

        return redirect('/admin/services_products');
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        
        // ГЛАВНЫЙ ЗАПРОС:
        $answer_services_products = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $services_product = ServicesProduct::with(['services_category'])

        ->moderatorLimit($answer_services_products)
        ->findOrFail($id);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_services_categories = operator_right('services_categories', false, 'index');

        // Категории
        $services_categories = ServicesCategory::moderatorLimit($answer_services_categories)
        ->companiesLimit($answer_services_categories)
        ->authors($answer_services_categories)
        ->systemItem($answer_services_categories)
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // dd($products_categories);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $services_categories_list = get_select_tree($services_categories, $services_product->services_category_id, null, null);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);



        // Вот это вообще надо? Или это уже упразднили?
        // --------------------------------------------------------------------------------------
        if ($request->ajax()) {
            // echo json_encode($properties);
            return view('products.properties-list', ['properties' => $properties, 'product_metrics' => $product_metrics, 'properties_list' => $properties_list]);
        }
        // --------------------------------------------------------------------------------------

        return view('services_products.edit', compact('services_product', 'page_info', 'services_categories_list'));
    }

    public function update(ServicesProductRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $services_product = ServicesProduct::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('update', $services_product);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        if ($request->hasFile('photo')) {

            $directory = $company_id.'/media/services_products/'.$services_product->id.'/img/';
            $name = 'avatar-'.time();

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($services_product->photo_id) {
                $array = save_photo($request, $directory, $name, null, $services_product->photo_id, $this->entity_name);

            } else {
                $array = save_photo($request, $directory, $name, null, null, $this->entity_name);
                
            }
            $photo = $array['photo'];

            $services_product->photo_id = $photo->id;
        } 

        $services_product->name = $request->name;
        $services_product->services_category_id = $request->services_category_id;
        $services_product->description = $request->description;

        // Модерация и системная запись
        $services_product->system_item = $request->system_item;
        $services_product->moderation = $request->moderation;

        // Отображение на сайте
        $services_product->display = $request->display;

        $services_product->editor_id = $user_id;
        $services_product->save();

        if ($services_product) {

            return Redirect('/admin/services_products');
        } else {

            abort(403, 'Ошибка обновления группы услуг');
        }
    }

    public function destroy(Request $request, $id)
    {

        
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $services_product = ServicesProduct::with('services')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $services_product);

        $user = $request->user();

        if ($services_product) {
            $services_product->editor_id = $user->id;
            $services_product->save();

            // Удаляем сайт с обновлением
            $services_product = ServicesProduct::destroy($id);

            if ($services_product) {

                return Redirect('/admin/services_products');
            } else {
                abort(403, 'Ошибка при удалении группы товаров');
            }
        } else {
            abort(403, 'Группа товаров не найдена');
        }
    }

    // Проверка наличия в базе
    public function services_check(Request $request)
    {
        $user = $request->user();

        // Проверка отдела в нашей базе данных
        $services_product = ServicesProduct::where(['name' => $request->name, 'company_id' => $user->company_id])->first();

        // Если такое название есть
        if ($services_product) {
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
    public function ajax_sort(Request $request)
    {

        $i = 1;

        foreach ($request->services_products as $item) {
            ServicesProduct::where('id', $item)->update(['sort' => $i]);
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

        $item = ServicesProduct::where('id', $request->id)->update(['system_item' => $system]);

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

        $item = ServicesProduct::where('id', $request->id)->update(['display' => $display]);

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


    // Добавление фоток
    public function product_photos(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod('index'));

        // ГЛАВНЫЙ ЗАПРОС:
        $services_product = ServicesProduct::with('album.photos')->moderatorLimit($answer)->findOrFail($id);
        // dd($product);

        // Подключение политики
        $this->authorize(getmethod('edit'), $product);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('services_products.photos', compact('page_info', 'services_product'));

    }

    // -------------------------------------- Exel ------------------------------------------
    public function products_download($type)
    {
        $data = ServicesProduct::get(['name', 'article', 'cost'])->toArray();
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

    public function ajax_count(Request $request)
    {
        $id = 2;
        // $entity = 'services_categories';

        // $id = $request->id;

        $services_category = ServicesCategory::withCount('services_products')->with('services_products')->findOrFail($id);


        if ($services_category->services_products_count > 0) {

            $services_products_list = $services_category->services_services_products->pluck('name', 'id');

            if ($services_products_list) {

                return view('services.mode-select', compact('services_products_list'));
            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при формировании списка групп товаров!',
                ];
            }

        } else {

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

            return view('services.mode-add', compact('units_categories_list'));
        }
    }

    public function ajax_modes(Request $request)
    {
        $mode = $request->mode;
        $services_category_id = $request->services_category_id;
        // $mode = 'mode-add';
        // $entity = 'service_categories';

        switch ($mode) {

            case 'mode-default':

            $services_category = ServicesCategory::withCount('services_products')->find($services_category_id);
            $services_products_count = $services_category->services_products_count;

            return view('services.mode-default', compact('services_products_count'));

            break;

            case 'mode-select':

            $services_products_list = ServicesProduct::where('services_category_id', $services_category_id)->get()->pluck('name', 'id');
            return view('services.mode-select', compact('services_products_list'));

            break;

            case 'mode-add':

            // // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            // $answer_units_categories = operator_right('units_categories', false, 'index');

            //     // Главный запрос
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

            return view('services.mode-add');

            break;
            
        }

    }

    
}
