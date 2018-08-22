<?php

namespace App\Http\Controllers;

// Модели
use App\Raw;
use App\RawsMode;
use App\RawsProduct;
use App\RawsCategory;
use App\Property;

use App\EntitySetting;

use App\Company;

use App\UnitsCategory;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\RawsCategoryRequest;

// Политика
use App\Policies\RawsCategoryPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;

// Специфические классы 

// На удаление
use Illuminate\Support\Facades\Auth;

class RawsCategoryController extends Controller
{
    // Сущность над которой производит операции контроллер
    protected $entity_name = 'raws_categories';
    protected $entity_dependence = false;

    public function index(Request $request)
    {
        // dd($alias);
        // Подключение политики
        $this->authorize('index', RawsCategory::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------
        $raws_categories = RawsCategory::with('raws_products')
        ->withCount('raws_products')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get()
        ->groupBy('parent_id');
        // dd($raws_categories);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем массив с вложенными элементами дял отображения дерева с правами, отдаем обьекты сущности и авторизованного пользователя
        // $raws_categories_tree = get_index_tree_with_rights($raws_categories, $user);
        // dd($raws_categories_tree);

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('includes.menu-views.enter', ['grouped_items' => $raws_categories, 'class' => 'App\RawsCategory', 'entity' => $this->entity_name, 'type' => 'edit', 'id' => $request->id]);
        }

        $entity = $this->entity_name;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
        // dd($page_info);

        return view('raws_categories.index', compact('raws_categories', 'page_info', 'entity'));
    }

    public function create(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), RawsCategory::class);

        $raws_category = new RawsCategory;

        $raws_modes_list = RawsMode::get()->pluck('name', 'id');

        // Если добавляем вложенный элемент
        if (isset($request->parent_id)) {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

            // Главный запрос
            $raws_categories = RawsCategory::moderatorLimit($answer)
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
            $raws_categories_list = get_select_tree($raws_categories, $request->parent_id, null, null);
            // echo $raws_categories_list;

            $raws_category = RawsCategory::with('raws_products')->findOrFail($request->parent_id);
            $raws_list = $raws_category->raws_products->pluck('name', 'id');

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


            return view('raws_categories.create-medium', compact('raws_category', 'raws_categories_list', 'type', 'raws_modes_list', 'units_categories_list', 'raws_list'));
        } else {

            return view('raws_categories.create-first', compact('raws_category', 'raws_modes_list'));
        }
    }

    public function store(RawsCategoryRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), RawsCategory::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Пишем в базу
        $raws_category = new RawsCategory;
        $raws_category->company_id = $company_id;
        $raws_category->author_id = $user_id;

        // Системная запись
        $raws_category->system_item = $request->system_item;

        $raws_category->display = $request->display;

    
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $raws_category->moderation = 1;
        }

        // Смотрим что пришло
        // Если категория
        if ($request->first_item == 1) {
            $raws_category->raws_mode_id = $request->raws_mode_id;
            $raws_category->category_status = 1; 
        }

        // Если вложенный
        if ($request->medium_item == 1) {
            $raws_category->parent_id = $request->parent_id;
            $raws_category->category_id = $request->category_id;

            $category = RawsCategory::findOrFail($request->category_id);

            $raws_category->raws_mode_id = $category->raws_mode_id;
        }

        
        if ($request->status == 'set') {
            $raws_category->status = $request->status;
        } else {
            $raws_category->status = 'one';
        }

        // Делаем заглавной первую букву
        $raws_category->name = get_first_letter($request->name);

        $raws_category->save();

        if ($raws_category) {

            // Переадресовываем на index
            return redirect()->action('RawsCategoryController@index', ['id' => $raws_category->id]);

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
        $answer_raws_categories = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $raws_category = RawsCategory::with(['raws_mode', 'metrics.unit', 'metrics.values'])
        ->withCount('metrics')
        ->moderatorLimit($answer_raws_categories)
        ->findOrFail($id);
        // dd($raws_category);

        $raws_category_metrics = [];
        foreach ($raws_category->metrics as $metric) {
            $raws_category_metrics[] = $metric->id;
        }
        // dd($product_metrics);

        // dd($raws_category_compositions);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raws_category);

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
            return view('raws_categories.metrics.properties-list', compact('properties', 'properties_list', 'raws_category_metrics'));
        }

        // dd($properties_list);

        // if ($raws_category->type == 'raws') {
        //     if ($raws_category->status == 'one') {
        //         $type = ['raws'];
        //     } else {
        //         $type = ['raws'];
        //     }
        // }

        // if ($raws_category->type == 'raws') {
        //     $type = [];
        // }

        // if ($raws_category->type == 'raws') {
        //     if ($raws_category->status == 'one') {
        //         $type = ['staff'];
        //     } else {
        //         $type = ['raws'];
        //     }
        // }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_raws_modes = operator_right('raws_modes', false, 'index');

        $raws_modes = RawsMode::with(['raws_categories' => function ($query) use ($answer_raws_categories) {
            $query->with('raws_products')
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

        // dd($raws_modes);
        
        $raws_modes_list = [];
        foreach ($raws_modes as $raws_mode) {
            $raws_categories_id = [];
            foreach ($raws_mode['raws_categories'] as $raws_cat) {
                $raws_categories_id[$raws_cat['id']] = $raws_cat;
            }
            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
            $raws_categories_list = get_parents_tree($raws_categories_id, null, null, null);


            $raws_modes_list[] = [
                'name' => $raws_mode['name'],
                'alias' => $raws_mode['alias'],
                'raws_categories' => $raws_categories_list,
            ];
        }


        
        // dd($raws_modes_list);
        // $grouped_raws_types = $raws_modes->groupBy('alias');
        // dd($grouped_raws_types);

        // Инфо о странице
        $page_info = pageInfo('raws_categories');

        if ($raws_category->category_status == 1) {

            // Выбираем все типы без проверки, так как они статичны, добавляться не будут
            // $raws_types_list = rawsType::get()->pluck('name', 'id');

            // dd($raws_category);

            // echo $id;
            // Меняем категорию
            return view('raws_categories.edit', compact('raws_category', 'page_info', 'properties', 'properties_list', 'raws_category_metrics', 'raws_modes_list', 'units_categories_list', 'units_list'));
        } else {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

            // Главный запрос
            $raws_categories = RawsCategory::moderatorLimit($answer)
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
            $raws_categories_list = get_select_tree($raws_categories, $raws_category->parent_id, null, $raws_category->id);

            // dd($raws_category);

            return view('raws_categories.edit', compact('raws_category', 'raws_categories_list', 'page_info', 'properties', 'properties_list', 'raws_category_metrics', 'raws_modes_list', 'units_categories_list', 'units_list'));
        }
    }

    public function update(RawsCategoryRequest $request, $id)
    {

        // TODO -- На 15.06.18 нет нормального решения отправки фотографий по ajax с методом "PATCH"

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_name, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $raws_category = RawsCategory::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raws_category);

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
            $directory = $company_id.'/media/raws_categories/'.$raws_category->id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($raws_category->photo_id) {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, $raws_category->photo_id, $settings);
            } else {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);
            }

            $photo = $array['photo'];

            $raws_category->photo_id = $photo->id;
        }

        $raws_category->description = $request->description;
        $raws_category->seo_description = $request->seo_description;

        // Модерация и системная запись
        $raws_category->system_item = $request->system_item;
        $raws_category->moderation = $request->moderation;

        // $raws_category->parent_id = $request->parent_id;
        $raws_category->editor_id = $user_id;

        // Если сменили тип категории продукции, то меняем его и всем вложенным элементам
        if (($raws_category->category_status == 1) && ($raws_category->raws_type_id != $request->raws_type_id)) {
            $raws_category->raws_type_id = $request->raws_type_id;

            $raws_categories = RawsCategory::whereCategory_id($id)
            ->update(['raws_mode_id' => $request->raws_mode_id]);

        }
        
        $raws_category->display = $request->display;

        // Делаем заглавной первую букву
        $raws_category->name = get_first_letter($request->name); 

        $raws_category->save();

        if ($raws_category) {

            return Redirect('/admin/raws_categories/'.$raws_category->type)->with('raws_category_id', $raws_category->id);

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
        $raws_category = RawsCategory::withCount('raws_products')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raws_category);

        // Удаляем ajax
        // Проверяем содержит ли индустрия вложения
        $raws_category_parent = RawsCategory::moderatorLimit($answer)->whereParent_id($id)->first();

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Если содержит, то даем сообщение об ошибке
        if ($raws_category_parent || ($raws_category->raws_products_count > 0)) {

            $result = [
                'error_status' => 1,
                'error_message' => 'Данная область содержит населенные пункты, удаление невозможно'
            ];
        } else {

            // Если нет, мягко удаляем
            if ($raws_category->category_status == 1) {
                $parent = null;
            } else {
                $parent = $raws_category->parent_id;
            }

            $raws_category->editor_id = $user_id;
            $raws_category->save();

            $raws_category = RawsCategory::destroy($id);

            if ($raws_category) {

                // Переадресовываем на index
                return redirect()->action('RawsCategoryController@index', ['id' => $parent]);
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
        $raws_category = RawsCategory::where(['name' => $request->name, 'company_id' => $user->company_id])->first();

        // Если такое название есть
        if ($raws_category) {
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
    public function raws_category_list(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

        // Главный запрос
        $raws_categories = RawsCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // dd($raws_categories);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $raws_categories_list = get_select_tree($raws_categories, $request->parent, null, $request->id);
        // dd($raws_categories_list);

        // Отдаем ajax
        echo json_encode($raws_categories_list, JSON_UNESCAPED_UNICODE);
    }


    // ------------------------------------------------ Ajax -------------------------------------------------

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;

        foreach ($request->raws_categories as $item) {
            RawsCategory::where('id', $item)->update(['sort' => $i]);
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

        $item = RawsCategory::where('id', $request->id)->update(['system_item' => $system]);

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

        $item = RawsCategory::where('id', $request->id)->update(['display' => $display]);

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
        $raws_category = RawsCategory::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod('update'), $raws_category);

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
            $directory = $company_id.'/media/raws_categories/'.$raws_category->id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($raws_category->photo_id) {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, $raws_category->photo_id, $settings);
            } else {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);
            }
            $photo = $array['photo'];

            $raws_category->photo_id = $photo->id;
        }

        $raws_category->description = $request->description;
        $raws_category->seo_description = $request->seo_description;

        // Модерация и системная запись
        $raws_category->system_item = $request->system_item;
        $raws_category->moderation = $request->moderation;

        $raws_category->parent_id = $request->parent_id;
        $raws_category->editor_id = $user_id;

        // Если сменили тип категории продукции, то меняем его и всем вложенным элементам
        if (($raws_category->category_status == 1) && ($raws_category->raws_type_id != $request->raws_type_id)) {
            $raws_category->raws_type_id = $request->raws_type_id;

            $raws_categories = RawsCategory::whereCategory_id($id)
            ->update(['raws_type_id' => $request->raws_type_id]);

        }
        
        $raws_category->display = $request->display;

        // Делаем заглавной первую букву
        $raws_category->name = get_first_letter($request->name); 

        $raws_category->save();

        if ($raws_category) {

            // Переадресовываем на index
            return redirect()->action('RawsCategoryController@index', ['id' => $raws_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории продукции!'
            ];
        }
    }
    
}
