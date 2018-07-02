<?php

namespace App\Http\Controllers;

// Модели
use App\ProductsCategory;
use App\ProductsMode;
use App\Company;
use App\Photo;
use App\Album;
use App\AlbumEntity;
use App\Property;
use App\Metric;
use App\Article;
use App\Value;
use App\Booklist;
use App\Entity;
use App\List_item;
use App\Unit;
use App\UnitsCategory;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\ProductsCategoryRequest;

// Политика
use App\Policies\ProductsCategoryPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;

// Специфические классы 

// На удаление
use Illuminate\Support\Facades\Auth;

class ProductsCategoryController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'products_categories';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), ProductsCategory::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------

        $products_categories = ProductsCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get();

        // dd(get_parents_tree($products_categories));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем массив с вложенными элементами дял отображения дерева с правами, отдаем обьекты сущности и авторизованного пользователя
        $products_categories_tree = get_index_tree_with_rights($products_categories, $user);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('products_categories.category-list', ['products_categories_tree' => $products_categories_tree, 'id' => $request->id]);
        }

        return view('products_categories.index', compact('products_categories_tree', 'page_info'));
    }

    public function types(Request $request, $type, $status = null)
    {

        // dd($alias);
        // Подключение политики
        $this->authorize('index', ProductsCategory::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        if (empty($type)) {
           $type = $request->type;
        }

        // -----------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------

        if ($status == null) {
            $products_categories = ProductsCategory::where('type', $type)
            ->whereIn('status', ['one', 'set'])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->orderBy('sort', 'asc')
            ->get();
        } else {
            $products_categories = ProductsCategory::where(['type' => $type, 'status' => $status])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->orderBy('sort', 'asc')
            ->get();
        }

        // dd($products_categories);

        // dd(get_parents_tree($products_categories));

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('products_categories.category-list', ['products_categories_tree' => $products_categories_tree, 'id' => $request->id]);
        }

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем массив с вложенными элементами дял отображения дерева с правами, отдаем обьекты сущности и авторизованного пользователя
        $products_categories_tree = get_index_tree_with_rights($products_categories, $user);

        // Инфо о странице
        $page_info = pageInfo('products_categories/'.$type);

        // dd($page_info);

        

        if (session('products_category_id')) {
            $id = session('products_category_id');
        } else {
            $id = null;
        }

        // dd($id);

        return view('products_categories.index', compact('products_categories_tree', 'page_info', 'type', 'id'));
    }

    public function create(Request $request, $type)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), ProductsCategory::class);

        $products_category = new ProductsCategory;

        $products_modes_list = ProductsMode::where('type', $type)->get()->pluck('name', 'id');

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

        // $units_list = Unit::where('units_category_id', $products_category->unit->units_category_id)->get()->pluck('name', 'id');

        // Если добавляем вложенный элемент
        if (isset($request->parent_id)) {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

            // Главный запрос
            $products_categories = ProductsCategory::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->where('type', $type)
            ->where('id', $request->category_id)
            ->orWhere('category_id', $request->category_id)
            ->orderBy('sort', 'asc')
            ->get(['id','name','category_status','parent_id'])
            ->keyBy('id')
            ->toArray();

            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
            $products_categories_list = get_select_tree($products_categories, $request->parent_id, null, null);
            // echo $products_categories_list;

            return view('products_categories.create-medium', compact('products_category', 'products_categories_list', 'type', 'products_modes_list', 'units_categories_list'));
        } else {

            return view('products_categories.create-first', compact('products_category', 'type', 'products_modes_list', 'units_categories_list'));
        }
    }

    public function store(ProductsCategoryRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), ProductsCategory::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Пишем в базу
        $products_category = new ProductsCategory;
        $products_category->company_id = $company_id;
        $products_category->author_id = $user_id;

        
        // Модерация и системная запись
        $products_category->system_item = $request->system_item;

        $products_category->products_mode_id = $request->products_mode_id;

        $products_category->type = $request->type;
        
        $products_category->unit_id = $request->unit_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $products_category->moderation = 1;
        }

        // Смотрим что пришло
        // Если категория
        if ($request->first_item == 1) {
            $products_category->category_status = 1;
            
        }

        // Если вложенный
        if ($request->medium_item == 1) {
            $products_category->parent_id = $request->parent_id;
            $products_category->category_id = $request->category_id;

            $category = ProductsCategory::findOrFail($request->category_id);

            $products_category->products_mode_id = $category->products_mode_id;
        }

        $products_category->display = $request->display;

        if ($request->status == 'set') {
            $products_category->status = $request->status;
        } else {
            $products_category->status = 'one';
        }


        // Делаем заглавной первую букву
        $products_category->name = get_first_letter($request->name);

        $products_category->save();

        

        if ($products_category) {

            // Отправляем на редактирование записи
            return Redirect('/products_categories/'.$products_category->id.'/edit');

        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи сектора!'
            ];
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_products_categories = operator_right($this->entity_name, true, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $products_category = ProductsCategory::with(['products_mode', 'metrics.unit', 'metrics.values', 'compositions'])
        ->withCount('metrics', 'compositions')
        ->moderatorLimit($answer_products_categories)
        ->findOrFail($id);
        // dd($products_category);

        $products_category_metrics = [];
        foreach ($products_category->metrics as $metric) {
            $products_category_metrics[] = $metric->id;
        }
        // dd($product_metrics);

        $products_category_compositions = [];
        foreach ($products_category->compositions as $composition) {
            $products_category_compositions[] = $composition->id;
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $products_category);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_properties = operator_right('properties', false, 'index');

        $answer_metrics = operator_right('metrics', false, 'index');

        $properties = Property::moderatorLimit($answer_properties)
        ->companiesLimit($answer_properties)
        ->authors($answer_properties)
        ->systemItem($answer_properties) // Фильтр по системным записям
        ->template($answer_properties)
        ->with(['metrics' => function ($query) use ($answer_metrics) {
            $query->with('values')
            ->moderatorLimit($answer_metrics)
            ->companiesLimit($answer_metrics)
            ->authors($answer_metrics)
            ->systemItem($answer_metrics); // Фильтр по системным записям 
        }])
        ->withCount('metrics')
        ->orderBy('sort', 'asc')
        ->get();

        $properties_list = $properties->pluck('name', 'id');

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
            $query->withCount('products')
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
            $products_categories_list = get_parents_tree($products_categories_id, null, null, null);


            $products_modes_list[] = [
                'name' => $products_mode['name'],
                'alias' => $products_mode['alias'],
                'products_categories' =>$products_categories_list,
            ];
        }

        // dd($products_modes_list);

        // $grouped_products_types = $products_modes->groupBy('alias');

        // dd($grouped_products_types);

        // Инфо о странице
        $page_info = pageInfo('products_categories/'.$products_category->type);


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

        $units_list = Unit::where('units_category_id', $products_category->unit->units_category_id)->get()->pluck('name', 'id');


        if ($products_category->category_status == 1) {

            // Выбираем все типы без проверки, так как они статичны, добавляться не будут
            // $products_types_list = ProductsType::get()->pluck('name', 'id');

            // dd($products_category);

            // echo $id;
            // Меняем категорию
            return view('products_categories.edit', compact('products_category', 'page_info', 'properties', 'properties_list', 'products_category_metrics', 'products_category_compositions', 'products_modes_list', 'units_categories_list', 'units_list'));
        } else {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

            // Главный запрос
            $products_categories = ProductsCategory::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->where('id', $request->category_id)
            ->orWhere('category_id', $request->category_id)
            ->orderBy('sort', 'asc')
            ->get(['id','name','category_status','parent_id'])
            ->keyBy('id')
            ->toArray();

            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
            $products_categories_list = get_select_tree($products_categories, $products_category->parent_id, null, $products_category->id);

            // dd($products_category);

            return view('products_categories.edit', compact('products_category', 'products_categories_list', 'page_info', 'properties', 'properties_list', 'products_category_metrics', 'products_category_compositions', 'products_modes_list', 'units_categories_list', 'units_list'));
        }
    }

    public function update(ProductsCategoryRequest $request, $id)
    {

        // TODO -- На 15.06.18 нет нормального решения отправки фотографий по ajax с методом "PATCH"

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_name, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $products_category = ProductsCategory::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $products_category);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $company_id = $user->company_id;

        // Если прикрепили фото
        if ($request->hasFile('photo')) {

            // Директория
            $directory = $company_id.'/media/products_categories/'.$products_category->id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($products_category->photo_id) {
                $array = save_photo($request, $user_id, $company_id, $directory, 'avatar-'.time(), null, $products_category->photo_id);

            } else {
                $array = save_photo($request, $user_id, $company_id, $directory, 'avatar-'.time());
                
            }
            $photo = $array['photo'];

            $products_category->photo_id = $photo->id;
        }

        $products_category->description = $request->description;
        $products_category->seo_description = $request->seo_description;

        // Модерация и системная запись
        $products_category->system_item = $request->system_item;
        $products_category->moderation = $request->moderation;

        // $products_category->parent_id = $request->parent_id;
        $products_category->editor_id = $user_id;

        $products_category->unit_id = $request->unit_id;

        // Если сменили тип категории продукции, то меняем его и всем вложенным элементам
        if (($products_category->category_status == 1) && ($products_category->products_type_id != $request->products_type_id)) {
            $products_category->products_type_id = $request->products_type_id;

            $products_categories = ProductsCategory::whereCategory_id($id)
            ->update(['products_mode_id' => $request->products_mode_id]);

        }
        
        $products_category->display = $request->display;

        // Делаем заглавной первую букву
        $products_category->name = get_first_letter($request->name); 

        $products_category->save();

        if ($products_category) {

            return Redirect('/products_categories/'.$products_category->type)->with('products_category_id', $products_category->id);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории продукции!'
            ];
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $products_category = ProductsCategory::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $products_category);

        // Удаляем ajax
        // Проверяем содержит ли индустрия вложения
        $products_category_parent = ProductsCategory::moderatorLimit($answer)->whereParent_id($id)->first();

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $type = $products_category->type;

        // Если содержит, то даем сообщение об ошибке
        if ($products_category_parent) {

            $result = [
                'error_status' => 1,
                'error_message' => 'Данная область содержит населенные пункты, удаление невозможно'
            ];
        } else {

            // Если нет, мягко удаляем
            if ($products_category->category_status == 1) {
                $parent = null;
            } else {
                $parent = $products_category->parent_id;
            }

            $products_category->editor_id = $user_id;
            $products_category->save();

            $products_category = ProductsCategory::destroy($id);

            if ($products_category) {

                // Переадресовываем на index
                return redirect()->action('ProductsCategoryController@types', ['id' => $parent, 'type' => $type]);
            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при записи сектора!'
                ];
            }
        }
    }


    

    // Проверка наличия в базе
    public function products_category_check(Request $request)
    {
        // Получаем авторизованного пользователя
        $user = $request->user();

        // Проверка отдела в нашей базе данных
        $products_category = ProductsCategory::where(['name' => $request->name, 'company_id' => $user->company_id])->first();

        // Если такое название есть
        if ($products_category) {
            $result = [
                'error_status' => 1,
            ];

        // Если нет
        } else {
            $result = [
                'error_status' => 0,
            ];
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // Список категорий альбомов
    public function products_category_list(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

        // Главный запрос
        $products_categories = ProductsCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // dd($products_categories);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $products_categories_list = get_select_tree($products_categories, $request->parent, null, $request->id);
        // dd($products_categories_list);

        // Отдаем ajax
        echo json_encode($products_categories_list, JSON_UNESCAPED_UNICODE);
    }

    // Сортировка
    public function products_categories_sort(Request $request)
    {

        $result = '';
        $i = 1;

        foreach ($request->products_categories as $item) {

            $products_category = ProductsCategory::findOrFail($item);
            $products_category->sort = $i;
            $products_category->save();
            $i++;
        }
    }


    // ------------------------------------------------ Ajax -------------------------------------------------

    public function ajax_update(Request $request, $id)
    {
        // dd($request);
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_name, 'update');

        // ГЛАВНЫЙ ЗАПРОС:
        $products_category = ProductsCategory::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod('update'), $products_category);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $company_id = $user->company_id;
        
        // Если прикрепили фото
        if ($request->hasFile('photo')) {

            // Директория
            $directory = $company_id.'/media/products_categories/'.$products_category->id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($products_category->photo_id) {
                $array = save_photo($request, $user_id, $company_id, $directory, 'avatar-'.time(), null, $products_category->photo_id);

            } else {
                $array = save_photo($request, $user_id, $company_id, $directory, 'avatar-'.time());
                
            }
            $photo = $array['photo'];

            $products_category->photo_id = $photo->id;
        }

        $products_category->description = $request->description;
        $products_category->seo_description = $request->seo_description;

        // Модерация и системная запись
        $products_category->system_item = $request->system_item;
        $products_category->moderation = $request->moderation;

        $products_category->parent_id = $request->parent_id;
        $products_category->editor_id = $user_id;

        // Если сменили тип категории продукции, то меняем его и всем вложенным элементам
        if (($products_category->category_status == 1) && ($products_category->products_type_id != $request->products_type_id)) {
            $products_category->products_type_id = $request->products_type_id;

            $products_categories = ProductsCategory::whereCategory_id($id)
            ->update(['products_type_id' => $request->products_type_id]);

        }
        
        $products_category->display = $request->display;

        // Делаем заглавной первую букву
        $products_category->name = get_first_letter($request->name); 

        $products_category->save();

        if ($products_category) {

            // Переадресовываем на index
            return redirect()->action('ProductsCategoryController@index', ['id' => $products_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории продукции!'
            ];
        }
    }
}
