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

// На удаление
use Illuminate\Support\Facades\Auth;

class RawsCategoryController extends Controller
{

    // Настройки сконтроллера
    public function __construct(RawsCategory $raws_category)
    {
        $this->middleware('auth');
        $this->raws_category = $raws_category;
        $this->class = RawsCategory::class;
        $this->model = 'App\RawsCategory';
        $this->entity_table = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'edit';
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Инфо о странице
        $page_info = pageInfo($this->entity_table);

        $answer = operator_right($this->entity_table, $this->entity_dependence, getmethod(__FUNCTION__));

        // Отдаем Ajax
        if ($request->ajax()) {

            $id = $request->id;
            return view('includes.menu_views.category_list',
                [
                    'items' => $this->raws_category->getIndex($answer, $request),
                    'entity' => $this->entity_table,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => count($this->raws_category->getIndex($answer, $request)),
                    'id' => $request->id
                ]
            );
        }

        // Отдаем на шаблон
        return view('includes.menu_views.index',
            [
                'items' => $this->raws_category->getIndex($answer, $request),
                'page_info' => $page_info,
                'entity' => $this->entity_table,
                'class' => $this->model,
                'type' => $this->type,
            ]
        );
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('raws_categories.create', ['raws_categories' => new $this->class, 'parent_id' => $request->parent_id, 'category_id' => $request->category_id]);
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
        $answer = operator_right($this->entity_table, $this->entity_dependence, getmethod(__FUNCTION__));

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
        $answer_raws_categories = operator_right($this->entity_table, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $raws_category = RawsCategory::with(['raws_mode', 'metrics.unit', 'metrics.values'])
        ->withCount('metrics')
        ->moderatorLimit($answer_raws_categories)
        ->findOrFail($id);
        // dd($raws_category);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raws_category);

        $raws_category_metrics = [];
        foreach ($raws_category->metrics as $metric) {
            $raws_category_metrics[] = $metric->id;
        }
        // dd($raws_category_metrics);

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
            $answer = operator_right($this->entity_table, $this->entity_dependence, 'index');

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
        $answer = operator_right($this->entity_table, $this->entity_table, getmethod(__FUNCTION__));

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
            $get_settings = EntitySetting::where(['entity' => $this->entity_table])->first();

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
        $answer = operator_right($this->entity_table, $this->entity_dependence, getmethod(__FUNCTION__));

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
        $answer = operator_right($this->entity_table, $this->entity_dependence, 'index');

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
        $answer = operator_right($this->entity_table, $this->entity_table, 'update');

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
            $get_settings = EntitySetting::where(['entity' => $this->entity_table])->first();

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
