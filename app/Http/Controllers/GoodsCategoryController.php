<?php

namespace App\Http\Controllers;

// Модели
use App\Goods;
use App\GoodsMode;
use App\GoodsProduct;
use App\GoodsCategory;
use App\Property;
use App\RawsCategory;
use App\EntitySetting;

// use App\Company;
// use App\Photo;
// use App\Album;
// use App\AlbumEntity;

// use App\Metric;
// use App\Article;
// use App\Value;
// use App\Booklist;
// use App\Entity;
// use App\List_item;
// use App\Unit;
use App\UnitsCategory;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\GoodsCategoryRequest;

// Политика
use App\Policies\GoodsCategoryPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;

// Специфические классы 

// На удаление
use Illuminate\Support\Facades\Auth;

class GoodsCategoryController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'goods_categories';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), GoodsCategory::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------

        $goods_categories = GoodsCategory::with('goods_products')
        ->withCount('goods_products')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get()
        ->groupBy('parent_id');
        // dd($goods_categories);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем массив с вложенными элементами для отображения дерева с правами, отдаем обьекты сущности и авторизованного пользователя
        // $goods_categories_tree = get_index_tree_with_rights($goods_categories, $user);
        // dd($goods_categories_tree);

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('includes.menu-views.enter', ['grouped_items' => $goods_categories, 'class' => 'App\GoodsCategory', 'entity' => $this->entity_name, 'type' => 'edit', 'id' => $request->id]);
        }

        $entity = $this->entity_name;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
        // dd($page_info);

        return view('goods_categories.index', compact('goods_categories', 'page_info', 'entity'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), GoodsCategory::class);

        $goods_category = new GoodsCategory;

        $goods_modes_list = GoodsMode::get()->pluck('name', 'id');

        // Если добавляем вложенный элемент
        if (isset($request->parent_id)) {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

            // Главный запрос
            $goods_categories = GoodsCategory::moderatorLimit($answer)
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
            $goods_categories_list = get_select_tree($goods_categories, $request->parent_id, null, null);
            // echo $goods_categories_list;

            $goods_category = GoodsCategory::with('goods_products')->findOrFail($request->parent_id);
            $goods_list = $goods_category->goods_products->pluck('name', 'id');

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

            return view('goods_categories.create-medium', compact('goods_category', 'goods_categories_list', 'type', 'goods_modes_list', 'units_categories_list', 'goods_list'));
        } else {

            return view('goods_categories.create-first', compact('goods_category', 'goods_modes_list'));
        }
    }

    public function store(GoodsCategoryRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), GoodsCategory::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Пишем в базу
        $goods_category = new GoodsCategory;
        $goods_category->company_id = $company_id;
        $goods_category->author_id = $user_id;

        // Системная запись
        $goods_category->system_item = $request->system_item;

        $goods_category->display = $request->display;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $goods_category->moderation = 1;
        }

        // Смотрим что пришло
        // Если категория
        if ($request->first_item == 1) {
            $goods_category->goods_mode_id = $request->goods_mode_id;
            $goods_category->category_status = 1; 
        }

        // Если вложенный
        if ($request->medium_item == 1) {
            $goods_category->parent_id = $request->parent_id;
            $goods_category->category_id = $request->category_id;

            $category = GoodsCategory::findOrFail($request->category_id);

            $goods_category->goods_mode_id = $category->goods_mode_id;
        }

        // Делаем заглавной первую букву
        $goods_category->name = get_first_letter($request->name);

        $goods_category->save();

        if ($goods_category) {

            // Переадресовываем на index
            return redirect()->action('GoodsCategoryController@index', ['id' => $goods_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи сектора!',
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
        $answer_goods_categories = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $goods_category = GoodsCategory::with(['goods_mode', 'metrics.unit', 'metrics.values', 'compositions.raws_product.unit'])
        ->withCount('metrics', 'compositions')
        ->moderatorLimit($answer_goods_categories)
        ->findOrFail($id);
        // dd($goods_category);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_category);

        $goods_category_metrics = [];
        foreach ($goods_category->metrics as $metric) {
            $goods_category_metrics[] = $metric->id;
        }
        // dd($product_metrics);

        $goods_category_compositions = [];
        foreach ($goods_category->compositions as $composition) {
            $goods_category_compositions[] = $composition->id;
        }
        // dd($goods_category_compositions);

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

         // Отдаем Ajax
        if ($request->ajax()) {
            return view('goods_categories.metrics.properties-list', compact('properties', 'properties_list', 'goods_category_metrics'));
        }

        // dd($properties_list);

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
        $answer_raws_categories = operator_right('raws_categories', false, 'index');

        $answer_raws_products = operator_right('raws_products', false, 'index');

        $answer_raws = operator_right('raws', false, 'index');

        $raws_categories = RawsCategory::with(['raws_products' => function ($query) use ($answer_raws_products, $answer_raws) {
            $query->with(['raws_articles' => function ($query) use ($answer_raws) {
                $query->whereHas('raws', function ($query) {
                   $query->whereNull('draft'); 
               });
            // ->moderatorLimit($answer_raws)
            // ->companiesLimit($answer_raws)
            // ->authors($answer_raws)
            // ->systemItem($answer_raws) // Фильтр по системным записям 

            }])
            ->withCount('raws_articles');
            // ->moderatorLimit($answer_raws_products)
            // ->companiesLimit($answer_raws_products)
            // ->authors($answer_raws_products)
            // ->systemItem($answer_raws_products); // Фильтр по системным записям 
        }])
        ->withCount('raws_products')
        ->moderatorLimit($answer_raws_categories)
        ->companiesLimit($answer_raws_categories)
        ->authors($answer_raws_categories)
        ->systemItem($answer_raws_categories) // Фильтр по системным записям 
        ->get()
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $composition_categories_list = get_parents_tree($raws_categories, null, null, null);
        // dd($composition_categories_list);

        $composition_list = [
            'name' => 'Сырье',
            'alias' => 'raws',
            'composition_categories' => $composition_categories_list,
        ];
        // dd($composition_list);
        // dd($goods_modes_list);
        // $grouped_goods_types = $goods_modes->groupBy('alias');
        // dd($grouped_goods_types);

        // Инфо о странице
        $page_info = pageInfo('goods_categories');

        if ($goods_category->category_status == 1) {

            // Выбираем все типы без проверки, так как они статичны, добавляться не будут
            // $goods_types_list = goodsType::get()->pluck('name', 'id');

            // dd($goods_category);

            // echo $id;
            // Меняем категорию
            return view('goods_categories.edit', compact('goods_category', 'page_info', 'properties', 'properties_list', 'goods_category_metrics', 'goods_category_compositions', 'composition_list', 'units_categories_list', 'units_list'));
        } else {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

            // Главный запрос
            $goods_categories = GoodsCategory::moderatorLimit($answer)
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
            $goods_categories_list = get_select_tree($goods_categories, $goods_category->parent_id, null, $goods_category->id);

            // dd($goods_category);

            return view('goods_categories.edit', compact('goods_category', 'goods_categories_list', 'page_info', 'properties', 'properties_list', 'goods_category_metrics', 'goods_category_compositions', 'composition_list', 'units_categories_list', 'units_list'));
        }
    }

    public function update(GoodsCategoryRequest $request, $id)
    {

        // TODO -- На 15.06.18 нет нормального решения отправки фотографий по ajax с методом "PATCH"

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_name, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $goods_category = GoodsCategory::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_category);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $company_id = $user->company_id;

        // Если прикрепили фото
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

            // Директория
            $directory = $company_id.'/media/goods_categories/'.$goods_category->id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id компании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($goods_category->photo_id) {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, $goods_category->photo_id, $settings);
            } else {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);
            }

            $photo = $array['photo'];

            $goods_category->photo_id = $photo->id;
        }

        $goods_category->description = $request->description;
        $goods_category->seo_description = $request->seo_description;

        // Модерация и системная запись
        $goods_category->system_item = $request->system_item;
        $goods_category->moderation = $request->moderation;

        // $goods_category->parent_id = $request->parent_id;
        $goods_category->editor_id = $user_id;

        // Если сменили тип категории продукции, то меняем его и всем вложенным элементам
        if (($goods_category->category_status == 1) && ($goods_category->goods_type_id != $request->goods_type_id)) {
            $goods_category->goods_type_id = $request->goods_type_id;

            $goods_categories = GoodsCategory::whereCategory_id($id)
            ->update(['goods_mode_id' => $request->goods_mode_id]);
        }
        
        $goods_category->display = $request->display;

        // Делаем заглавной первую букву
        $goods_category->name = get_first_letter($request->name); 
        $goods_category->save();

        if ($goods_category) {

            return Redirect('/admin/goods_categories/'.$goods_category->type)->with('goods_category_id', $goods_category->id);

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
        $goods_category = GoodsCategory::withCount('goods_products')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_category);

        // Удаляем ajax
        // Проверяем содержит ли индустрия вложения
        $goods_category_parent = GoodsCategory::moderatorLimit($answer)->whereParent_id($id)->first();

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Если содержит, то даем сообщение об ошибке
        if ($goods_category_parent || ($goods_category->goods_products_count > 0)) {

            $result = [
                'error_status' => 1,
                'error_message' => 'Данная область содержит населенные пункты, удаление невозможно'
            ];
        } else {

            // Если нет, мягко удаляем
            if ($goods_category->category_status == 1) {
                $parent = null;
            } else {
                $parent = $goods_category->parent_id;
            }

            $goods_category->editor_id = $user_id;
            $goods_category->save();

            $goods_category = GoodsCategory::destroy($id);

            if ($goods_category) {

                // Переадресовываем на index
                return redirect()->action('GoodsCategoryController@index', ['id' => $parent]);
            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при записи сектора!'
                ];
            }
        }
    }

    // Проверка наличия в базе
    public function ajax_check(Request $request)
    {
        // Получаем авторизованного пользователя
        $user = $request->user();

        // Проверка отдела в нашей базе данных
        $goods_category = GoodsCategory::where(['name' => $request->name, 'company_id' => $user->company_id])->first();

        // Если такое название есть
        if ($goods_category) {
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
    public function goods_category_list(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

        // Главный запрос
        $goods_categories = GoodsCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();
        // dd($goods_categories);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $goods_categories_list = get_select_tree($goods_categories, $request->parent, null, $request->id);
        // dd($goods_categories_list);

        // Отдаем ajax
        echo json_encode($goods_categories_list, JSON_UNESCAPED_UNICODE);
    }

    // ------------------------------------------------ Ajax -------------------------------------------------

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;

        foreach ($request->goods_categories as $item) {
            GoodsCategory::where('id', $item)->update(['sort' => $i]);
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

        $item = GoodsCategory::where('id', $request->id)->update(['system_item' => $system]);

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

        $item = GoodsCategory::where('id', $request->id)->update(['display' => $display]);

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

    public function ajax_update(Request $request, $id)
    {
        
        // dd($request);
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_name, 'update');

        // ГЛАВНЫЙ ЗАПРОС:
        $goods_category = GoodsCategory::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod('update'), $goods_category);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $company_id = $user->company_id;
        
        // Если прикрепили фото
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

            // Директория
            $directory = $company_id.'/media/goods_categories/'.$goods_category->id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($goods_category->photo_id) {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, $goods_category->photo_id, $settings);
            } else {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);
            }
            $photo = $array['photo'];

            $goods_category->photo_id = $photo->id;
        }

        $goods_category->description = $request->description;
        $goods_category->seo_description = $request->seo_description;

        // Модерация и системная запись
        $goods_category->system_item = $request->system_item;
        $goods_category->moderation = $request->moderation;

        $goods_category->parent_id = $request->parent_id;
        $goods_category->editor_id = $user_id;

        // Если сменили тип категории продукции, то меняем его и всем вложенным элементам
        if (($goods_category->category_status == 1) && ($goods_category->goods_type_id != $request->goods_type_id)) {
            $goods_category->goods_type_id = $request->goods_type_id;

            $goods_categories = GoodsCategory::whereCategory_id($id)
            ->update(['goods_type_id' => $request->goods_type_id]);

        }
        
        $goods_category->display = $request->display;

        // Делаем заглавной первую букву
        $goods_category->name = get_first_letter($request->name); 

        $goods_category->save();

        if ($goods_category) {

            // Переадресовываем на index
            return redirect()->action('GoodsCategoryController@index', ['id' => $goods_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории продукции!'
            ];
        }
    }
    
}
