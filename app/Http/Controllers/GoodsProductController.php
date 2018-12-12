<?php

namespace App\Http\Controllers;

// Модели
use App\GoodsProduct;
use App\User;
use App\GoodsCategory;
use App\Company;
use App\Photo;
use App\Booklist;
use App\Entity;
use App\List_item;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\GoodsProductRequest;

// Общие классы
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DB;

// Специфические классы
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\ImageManagerStatic as Image;

class GoodsProductController extends Controller
{

    // Настройки сконтроллера
    public function __construct(GoodsProduct $goods_product)
    {
        $this->middleware('auth');
        $this->goods_product = $goods_product;
        $this->class = GoodsProduct::class;
        $this->model = 'App\GoodsProduct';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);
        if(($filter_url != null)&&($request->filter != 'active')){

            Cookie::queue(Cookie::forget('filter_' . $this->entity_alias));
            return Redirect($filter_url);
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------------

        $goods_products = GoodsProduct::with(
            'author',
            'company',
            'goods_category',
            'goods_articles.goods'
        )
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        ->filter($request, 'company_id')
        ->filter($request, 'goods_category_id')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
            'goods_category',       // Категория товара
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('goods_products.index', compact('goods_products', 'page_info', 'filter'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // ГЛАВНЫЙ ЗАПРОС:
        $answer_goods_products = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $goods_product = new GoodsProduct;

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_goods_categories = operator_right('goods_categories', false, 'index');

        // Категории
        $goods_categories = GoodsCategory::moderatorLimit($answer_goods_categories)
        ->companiesLimit($answer_goods_categories)
        ->authors($answer_goods_categories)
        ->systemItem($answer_goods_categories)
        ->orderBy('sort', 'asc')
        ->get(['id','name','parent_id'])
        ->keyBy('id')
        ->toArray();

        // dd($goods_categories);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $goods_categories_list = get_select_tree($goods_categories, 1, null, null);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('goods_products.create', compact('goods_product', 'page_info', 'goods_categories_list'));
    }

    public function store(GoodsProductRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), GoodsProduct::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Наполняем сущность данными
        $goods_product = new GoodsProduct;
        $goods_product->name = $request->name;
        $goods_product->description = $request->description;
        $goods_product->goods_category_id = $request->goods_category_id;

        // Автоматически отправляем запись на модерацию
        // $product->moderation = 1;

        // Модерация и системная запись
        $goods_product->system_item = $request->system_item;

        $goods_product->display = $request->display;

        $goods_product->company_id = $company_id;
        $goods_product->author_id = $user_id;
        $goods_product->save();

        return redirect('/admin/goods_products');
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // ГЛАВНЫЙ ЗАПРОС:
        $answer_goods_products = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $goods_product = GoodsProduct::with(['goods_category'])
        ->moderatorLimit($answer_goods_products)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_product);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_goods_categories = operator_right('goods_categories', false, 'index');

        // Категории
        $goods_categories = GoodsCategory::moderatorLimit($answer_goods_categories)
        ->companiesLimit($answer_goods_categories)
        ->authors($answer_goods_categories)
        ->systemItem($answer_goods_categories)
        ->orderBy('sort', 'asc')
        ->get(['id','name','parent_id'])
        ->keyBy('id')
        ->toArray();

        // dd($products_categories);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $goods_categories_list = get_select_tree($goods_categories, $goods_product->goods_category_id, null, null);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);



        // Вот это вообще надо? Или это уже упразднили?
        // --------------------------------------------------------------------------------------
        if ($request->ajax()) {
            // echo json_encode($properties);
            return view('products.properties-list', ['properties' => $properties, 'product_metrics' => $product_metrics, 'properties_list' => $properties_list]);
        }
        // --------------------------------------------------------------------------------------

        return view('goods_products.edit', compact('goods_product', 'page_info', 'goods_categories_list'));
    }

    public function update(GoodsProductRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $goods_product = GoodsProduct::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_product);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        if ($request->hasFile('photo')) {

            $directory = $company_id.'/media/goods_products/'.$goods_product->id.'/img';
            $name = 'avatar-'.time();

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($goods_product->photo_id) {
                $array = save_photo($request, $directory, $name, null, $goods_product->photo_id, $this->entity_alias);

            } else {
                $array = save_photo($request, $directory, $name, null, null, $this->entity_alias);

            }
            $photo = $array['photo'];

            $goods_product->photo_id = $photo->id;
        }

        $goods_product->name = $request->name;
        $goods_product->goods_category_id = $request->goods_category_id;
        $goods_product->description = $request->description;

        // Модерация и системная запись
        $goods_product->system_item = $request->system_item;
        $goods_product->moderation = $request->moderation;

        // Отображение на сайте
        $goods_product->display = $request->display;

        $goods_product->editor_id = $user_id;
        $goods_product->save();

        if ($goods_product) {

            return Redirect('/admin/goods_products');
        } else {

            abort(403, 'Ошибка обновления группы товаров');
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $goods_product = GoodsProduct::with('goods')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_product);

        $user = $request->user();

        if ($goods_product) {
            $goods_product->editor_id = $user->id;
            $goods_product->save();

            // Удаляем сайт с обновлением
            $goods_product = GoodsProduct::destroy($id);

            if ($goods_product) {
            // $relations = AlbumMedia::whereAlbum_id($id)->pluck('media_id')->toArray();
            // $photos = Photo::whereIn('id', $relations)->delete();
            // $media = AlbumMedia::whereAlbum_id($id)->delete();

                return Redirect('/admin/goods_products');
            } else {
                abort(403, 'Ошибка при удалении группы товаров');
            }
        } else {
            abort(403, 'Группа товаров не найдена');
        }
    }

    // Добавление фоток
    public function product_photos(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('index'));

        // ГЛАВНЫЙ ЗАПРОС:
        $product = Product::with('album.photos')->moderatorLimit($answer)->findOrFail($id);
        // dd($product);

        // Подключение политики
        $this->authorize(getmethod('edit'), $product);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('products.photos', compact('page_info', 'product'));

    }

    // -------------------------------------- Exel ------------------------------------------
    public function goods_products_download($type)
    {
        $data = GoodsProduct::get(['name', 'description'])->toArray();
        // dd($data);

        return Excel::create('goods_products-'.Carbon::now()->format('d.m.Y'), function($excel) use ($data) {
            $excel->sheet('Группы товаров', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download($type);
    }

    public function goods_products_import(Request $request)
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
                    $data['goods_category_id'] = $row['goods_category_id'];
                    $data['author_id'] = $user_id;

                    if(!empty($data)) {
                        DB::table('goods_products')->insert($data);
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

        $goods_category = GoodsCategory::withCount('goods_products')->with('goods_products')->findOrFail($id);

        if ($goods_category->goods_products_count > 0) {

            $goods_products_list = $goods_category->goods_products->pluck('name', 'id');

            if ($goods_products_list) {

                return view('goods.mode-select', compact('goods_products_list'));
            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при формировании списка групп товаров!',
                ];
            }

        } else {

            return view('goods.create_modes.mode-add');
        }
    }

    public function ajax_change_create_mode(Request $request)
    {
        $mode = $request->mode;
        $goods_category_id = $request->goods_category_id;
        // $mode = 'mode-add';
        // $entity = 'service_categories';

        switch ($mode) {

            case 'mode-default':

            $goods_category = GoodsCategory::withCount('goods_products')->find($goods_category_id);
            $goods_products_count = $goods_category->goods_products_count;

            return view('goods.create_modes.mode_default', compact('goods_products_count'));

            break;

            case 'mode-select':

            $goods_products = GoodsProduct::with('unit')->where(['goods_category_id' => $goods_category_id, 'set_status' => $request->set_status])
            ->get(['id', 'name', 'unit_id']);
            return view('goods.create_modes.mode_select', compact('goods_products'));

            break;

            case 'mode-add':

            return view('goods.create_modes.mode_add');

            break;

        }
    }

    public function ajax_get_products_list(Request $request)
    {

        $goods_products = GoodsProduct::where(['goods_category_id' => $request->goods_category_id, 'set_status' => $request->set_status])
        ->orWhere('id', $request->goods_product_id)
        ->get(['id', 'name']);

        return view('includes.selects.goods_products', ['goods_products' => $goods_products, 'goods_product_id' => $request->goods_product_id, 'set_status' => $request->set_status]);
    }


}
