<?php

namespace App\Http\Controllers;

// Модели
use App\GoodsProduct;
use App\GoodsCategory;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\GoodsProductRequest;

// Общие классы
use Illuminate\Support\Facades\Cookie;
use Carbon\Carbon;

// Специфические классы
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\ImageManagerStatic as Image;

class GoodsProductController extends Controller
{

    // Настройки контроллера
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
            'category',
            'articles'
        )
        ->withCount('articles')
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

        return view('goods_products.index',[
            'goods_products' => $goods_products,
            'page_info' => pageInfo($this->entity_alias),
            'filter' => $filter,
            'nested' => 'goods_articles_count'
        ]);
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('goods_products.create', [
            'goods_product' => new $this->class,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function store(GoodsProductRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Наполняем сущность данными
        $goods_product = new GoodsProduct;
        $goods_product->name = $request->name;
        $goods_product->description = $request->description;
        $goods_product->goods_category_id = $request->goods_category_id;
        $goods_product->unit_id = $request->unit_id;

        if (isset($request->set_status)) {
            $goods_product->set_status = $request->set_status;
        }

        $goods_product->system_item = $request->system_item;
        $goods_product->display = $request->display;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $goods_product->moderation = 1;
        }

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $goods_product->company_id = $user->company_id;
        $goods_product->author_id = hideGod($user);

        $goods_product->save();

        if ($goods_product) {
            return redirect()->route('goods_products.index');
        } else {
            abort(403, 'Ошибка записи сайта');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        $goods_product = GoodsProduct::moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_product);

        return view('goods_products.edit', [
            'goods_product' => $goods_product,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function update(GoodsProductRequest $request, $id)
    {

        $goods_product = GoodsProduct::moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_product);

        $goods_product->name = $request->name;
        $goods_product->description = $request->description;
        $goods_product->goods_category_id = $request->goods_category_id;
        $goods_product->unit_id = $request->unit_id;

        if (isset($request->set_status)) {
            $goods_product->set_status = $request->set_status;
        }

        // Модерация и системная запись
        $goods_product->system_item = $request->system_item;
        $goods_product->display = $request->display;

        $goods_product->moderation = $request->moderation;

        $goods_product->editor_id = hideGod($request->user());
        $goods_product->save();

        if ($goods_product) {
            return redirect()->route('goods_products.index');
        } else {
            abort(403, 'Ошибка обновления группы товаров');
        }
    }

    public function destroy(Request $request, $id)
    {

        $goods_product = GoodsProduct::with('articles')
        ->moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_product);

        $goods_product->editor_id = hideGod($request->user());
        $goods_product->save();

        $goods_product->delete();

        if ($goods_product) {
            return redirect()->route('goods_products.index');
        } else {
            abort(403, 'Ошибка удаления группы товаров');
        }
    }


    // ------------------------------------- Ajax ------------------------------------------
    public function ajax_change_create_mode(Request $request)
    {
        $mode = $request->mode;
        $goods_category_id = $request->goods_category_id;
        // $mode = 'mode-add';
        // $entity = 'service_categories';

        switch ($mode) {

            case 'mode-default':

            $goods_category = GoodsCategory::withCount('products')
            ->find($goods_category_id);
            $goods_products_count = $goods_category->products_count;

            return view('goods.create_modes.mode_default', compact('goods_products_count'));

            break;

            case 'mode-select':

            $goods_products = GoodsProduct::with('unit')
            ->where([
                'goods_category_id' => $goods_category_id,
                'set_status' => $request->set_status
            ])
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

        $goods_category = GoodsCategory::withCount('products')->with('products')->findOrFail($id);

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




}
