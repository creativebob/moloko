<?php

namespace App\Http\Controllers;

// Модели
use App\RawsProduct;
use App\User;
use App\RawsCategory;
use App\Company;
use App\Photo;
use App\Booklist;
use App\Entity;
use App\List_item;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\RawsProductRequest;

// Политика
use App\Policies\RawsProductPolicy;

// Общие классы
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DB;

// Специфические классы 
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\ImageManagerStatic as Image;

// На удаление
use Illuminate\Support\Facades\Auth;

class RawsProductController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'raws_products';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), RawsProduct::class);

        // Включение контроля активного фильтра 
        $filter_url = autoFilter($request, $this->entity_name);
        if(($filter_url != null)&&($request->filter != 'active')){

            Cookie::queue(Cookie::forget('filter_' . $this->entity_name));
            return Redirect($filter_url);
        };

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------------

        $raws_products = RawsProduct::with('author', 'company', 'raws_category', 'raws_articles.raws')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        ->filter($request, 'company_id')
        ->filter($request, 'raws_category_id')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // ----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter_query = RawsProduct::with('author', 'raws_category')
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
        $filter = addFilter($filter, $filter_query, $request, 'Выберите категорию:', 'raws_category', 'raws_category_id', null, 'internal-id-one');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // ----------------------------------------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('raws_products.index', compact('raws_products', 'page_info', 'product', 'filter'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), RawsProduct::class);

        // ГЛАВНЫЙ ЗАПРОС:
        $answer_raws_products = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $raws_product = new RawsProduct;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_raws_categories = operator_right('raws_categories', false, 'index');

        // Категории
        $raws_categories = RawsCategory::moderatorLimit($answer_raws_categories)
        ->companiesLimit($answer_raws_categories)
        ->authors($answer_raws_categories)
        ->systemItem($answer_raws_categories)
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();
        // dd($raws_categories);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $raws_categories_list = get_select_tree($raws_categories, 1, null, null);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('raws_products.create', compact('raws_product', 'page_info', 'raws_categories_list'));
    }

    public function store(RawsProductRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), RawsProduct::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Наполняем сущность данными
        $raws_product = new RawsProduct;
        $raws_product->name = $request->name;
        $raws_product->description = $request->description;
        $raws_product->raws_category_id = $request->raws_category_id;

        // Модерация и системная запись
        $raws_product->system_item = $request->system_item;
        $raws_product->display = $request->display;

        $raws_product->company_id = $company_id;
        $raws_product->author_id = $user_id;
        $raws_product->save();

        return redirect('/admin/raws_products');
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // ГЛАВНЫЙ ЗАПРОС:
        $answer_raws_products = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $raws_product = RawsProduct::with(['raws_category'])
        ->moderatorLimit($answer_raws_products)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raws_product);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_raws_categories = operator_right('raws_categories', false, 'index');

        // Категории
        $raws_categories = RawsCategory::moderatorLimit($answer_raws_categories)
        ->companiesLimit($answer_raws_categories)
        ->authors($answer_raws_categories)
        ->systemItem($answer_raws_categories)
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();
        // dd($products_categories);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $raws_categories_list = get_select_tree($raws_categories, $raws_product->raws_category_id, null, null);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('raws_products.edit', compact('raws_product', 'page_info', 'raws_categories_list'));
    }

    public function update(RawsProductRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $raws_product = RawsProduct::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raws_product);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        if ($request->hasFile('photo')) {

            $directory = $company_id.'/media/raws_products/'.$raws_product->id.'/img/';
            $name = 'avatar-'.time();

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($raws_product->photo_id) {
                $array = save_photo($request, $directory, $name, null, $raws_product->photo_id, $this->entity_name);

            } else {
                $array = save_photo($request, $directory, $name, null, null, $this->entity_name);
                
            }
            $photo = $array['photo'];

            $raws_product->photo_id = $photo->id;
        } 

        $raws_product->name = $request->name;
        $raws_product->raws_category_id = $request->raws_category_id;
        $raws_product->description = $request->description;

        // Модерация и системная запись
        $raws_product->system_item = $request->system_item;
        $raws_product->moderation = $request->moderation;

        // Отображение на сайте
        $raws_product->display = $request->display;

        $raws_product->editor_id = $user_id;
        $raws_product->save();

        if ($raws_product) {

            return Redirect('/admin/raws_products');
        } else {

            abort(403, 'Ошибка обновления группы товаров');
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $raws_product = RawsProduct::with('raws')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raws_product);

        $user = $request->user();

        if ($raws_product) {
            $raws_product->editor_id = $user->id;
            $raws_product->save();

            // Удаляем сайт с обновлением
            $raws_product = RawsProduct::destroy($id);

            if ($raws_product) {
            // $relations = AlbumMedia::whereAlbum_id($id)->pluck('media_id')->toArray();
            // $photos = Photo::whereIn('id', $relations)->delete();
            // $media = AlbumMedia::whereAlbum_id($id)->delete();

                return Redirect('/admin/raws_products');
            } else {
                abort(403, 'Ошибка при удалении группы товаров');
            }
        } else {
            abort(403, 'Группа товаров не найдена');
        }
    }

    // Проверка наличия в базе
    public function ajax_check(Request $request)
    {
        $user = $request->user();

        // Проверка отдела в нашей базе данных
        $raws_product = RawsProduct::where(['name' => $request->name, 'company_id' => $user->company_id])->first();

        // Если такое название есть
        if ($raws_product) {
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
        
        foreach ($request->raws_products as $item) {
            RawsProduct::where('id', $item)->update(['sort' => $i]);
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

    // -------------------------------------- Exel ------------------------------------------
    public function raws_products_download($type)
    {
        $data = RawsProduct::get(['name', 'description'])->toArray();
        // dd($data);

        return Excel::create('raws_products-'.Carbon::now()->format('d.m.Y'), function($excel) use ($data) {
            $excel->sheet('Группы товаров', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download($type);
    }

    public function raws_products_import(Request $request)
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
                    $data['description'] = $row['description'];
                    $data['raws_category_id'] = $row['raws_category_id'];
                    $data['author_id'] = $user_id;

                    if(!empty($data)) {
                        DB::table('raws_products')->insert($data);
                    }
                }
            });
        }

        return back();
    }

    public function ajax_count(Request $request)
    {
        // $id = 2;

        $id = $request->id;

        $raws_category = RawsCategory::withCount('raws_products')->with('raws_products')->findOrFail($id);


        if ($raws_category->raws_products_count > 0) {

            $raws_products_list = $raws_category->raws_products->pluck('name', 'id');

            if ($raws_products_list) {

                return view('raws.mode-select', compact('raws_products_list'));
            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при формировании списка групп товаров!',
                ];
            }

        } else {

            return view('raws.mode-add');
        }
    }

    public function ajax_modes(Request $request)
    {
        $mode = $request->mode;
        $raws_category_id = $request->raws_category_id;
        // $mode = 'mode-add';
        // $entity = 'service_categories';

        switch ($mode) {

            case 'mode-default':

            $raws_category = RawsCategory::withCount('raws_products')->find($raws_category_id);
            $raws_products_count = $raws_category->raws_products_count;

            return view('raws.mode-default', compact('raws_products_count'));

            break;

            case 'mode-select':

            $raws_products_list = RawsProduct::where('raws_category_id', $raws_category_id)->get()->pluck('name', 'id');
            return view('raws.mode-select', compact('raws_products_list'));

            break;

            case 'mode-add':

            return view('raws.mode-add');

            break;

        }
   }


}
