<?php

namespace App\Http\Controllers;

// Модели
use App\Service;
use App\ServicesMode;
use App\ServicesProduct;
use App\ServicesCategory;
use App\Property;
use App\Company;
use App\PhotoSetting;
use App\UnitsCategory;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\ServicesCategoryRequest;

// На удаление
use Illuminate\Support\Facades\Auth;

class ServicesCategoryController extends Controller
{

    // Настройки сконтроллера
    public function __construct(ServicesCategory $services_category)
    {
        $this->middleware('auth');
        $this->services_category = $services_category;
        $this->class = ServicesCategory::class;
        $this->model = 'App\ServicesCategory';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'edit';
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Отдаем Ajax
        if ($request->ajax()) {

            $id = $request->id;
            return view('includes.menu_views.category_list',
                [
                    'items' => $this->services_category->getIndex($request, $answer),
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => count($this->services_category->getIndex($request, $answer)),
                    'id' => $request->id
                ]
            );
        }

        // Отдаем на шаблон
        return view('includes.menu_views.index',
            [
                'items' => $this->services_category->getIndex($request, $answer),
                'page_info' => $page_info,
                'entity' => $this->entity_alias,
                'class' => $this->model,
                'type' => $this->type,
                'filter' => setFilter($this->entity_alias, $request, [
                    'booklist'
                ]),
            ]
        );
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('services_categories.create', ['services_category' => new $this->class, 'parent_id' => $request->parent_id, 'category_id' => $request->category_id]);
    }

    public function store(ServicesCategoryRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), ServicesCategory::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Пишем в базу
        $services_category = new ServicesCategory;
        $services_category->company_id = $company_id;
        $services_category->author_id = $user_id;

        // Системная запись
        $services_category->system_item = $request->system_item;

        $services_category->display = $request->display;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $services_category->moderation = 1;
        }

        // Смотрим что пришло
        // Если категория
        if ($request->first_item == 1) {
            $services_category->category_status = 1;
            $services_category->services_mode_id = $request->services_mode_id;
        }

        // Если вложенный
        if ($request->medium_item == 1) {
            $services_category->parent_id = $request->parent_id;
            $services_category->category_id = $request->category_id;

            $category = ServicesCategory::findOrFail($request->category_id);

            $services_category->services_mode_id = $category->services_mode_id;
        }

        if ($request->status == 'set') {
            $services_category->status = $request->status;
        } else {
            $services_category->status = 'one';
        }

        // Делаем заглавной первую букву
        $services_category->name = get_first_letter($request->name);

        $services_category->save();

        if ($services_category) {

            // Переадресовываем на index
            return redirect()->action('ServicesCategoryController@index', ['id' => $services_category->id]);

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
        $answer_services_categories = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $services_category = ServicesCategory::with(['services_mode', 'metrics.unit', 'metrics.values'])
        ->withCount('metrics')
        ->moderatorLimit($answer_services_categories)
        ->findOrFail($id);
        // dd($services_category);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $services_category);

        $services_category_metrics = [];
        foreach ($services_category->metrics as $metric) {
            $services_category_metrics[] = $metric->id;
        }
        // dd($services_category_metrics);

        // $services_category_compositions = [];
        // foreach ($services_category->compositions as $composition) {
        //     $services_category_compositions[] = $composition->id;
        // }
        // dd($services_category_compositions);

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
            return view('services_categories.metrics.properties-list', compact('properties', 'properties_list', 'services_category_metrics'));
        }

        // dd($properties_list);

        // if ($services_category->type == 'goods') {
        //     if ($services_category->status == 'one') {
        //         $type = ['raws'];
        //     } else {
        //         $type = ['goods'];
        //     }
        // }

        // if ($services_category->type == 'raws') {
        //     $type = [];
        // }

        // if ($services_category->type == 'services') {
        //     if ($services_category->status == 'one') {
        //         $type = ['staff'];
        //     } else {
        //         $type = ['services'];
        //     }
        // }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_services_modes = operator_right('services_modes', false, 'index');

        $services_modes = ServicesMode::with(['services_categories' => function ($query) use ($answer_services_categories) {
            $query->with('services_products')
            ->withCount('services_products')
            ->moderatorLimit($answer_services_categories)
            ->companiesLimit($answer_services_categories)
            ->authors($answer_services_categories)
            ->systemItem($answer_services_categories); // Фильтр по системным записям
        }])
        ->moderatorLimit($answer_services_modes)
        ->companiesLimit($answer_services_modes)
        ->authors($answer_services_modes)
        ->systemItem($answer_services_modes) // Фильтр по системным записям
        ->template($answer_services_modes)
        ->orderBy('sort', 'asc')
        ->get()
        ->toArray();

        // dd($services_modes);
        $services_modes_list = [];
        foreach ($services_modes as $services_mode) {
            $services_categories_id = [];
            foreach ($services_mode['services_categories'] as $services_cat) {
                $services_categories_id[$services_cat['id']] = $services_cat;
            }
            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
            $services_categories_list = get_parents_tree($services_categories_id, null, null, null);


            $services_modes_list[] = [
                'name' => $services_mode['name'],
                'alias' => $services_mode['alias'],
                'services_categories' => $services_categories_list,
            ];
        }
        // dd($services_modes_list);
        // $grouped_services_types = $services_modes->groupBy('alias');
        // dd($grouped_services_types);

        // Инфо о странице
        $page_info = pageInfo('services_categories');

        if ($services_category->category_status == 1) {

            // Выбираем все типы без проверки, так как они статичны, добавляться не будут
            // $services_types_list = servicesType::get()->pluck('name', 'id');

            // dd($services_category);

            // echo $id;
            // Меняем категорию
            return view('services_categories.edit', compact('services_category', 'page_info', 'properties', 'properties_list', 'services_category_metrics', 'services_category_compositions', 'services_modes_list', 'units_categories_list', 'units_list'));
        } else {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entity_alias, $this->entity_dependence, 'index');

            // Главный запрос
            $services_categories = ServicesCategory::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->where('id', $request->category_id)
            ->orWhere('category_id', $request->category_id)
            ->orderBy('sort', 'asc')
            ->get(['id','name','parent_id'])
            ->keyBy('id')
            ->toArray();

            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
            $services_categories_list = get_select_tree($services_categories, $services_category->parent_id, null, $services_category->id);

            // dd($services_category);

            return view('services_categories.edit', compact('services_category', 'services_categories_list', 'page_info', 'properties', 'properties_list', 'services_category_metrics', 'services_category_compositions', 'services_modes_list', 'units_categories_list', 'units_list'));
        }
    }

    public function update(servicesCategoryRequest $request, $id)
    {

        // TODO -- На 15.06.18 нет нормального решения отправки фотографий по ajax с методом "PATCH"

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_alias, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $services_category = servicesCategory::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $services_category);

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
            $get_settings = PhotoSetting::where(['entity' => $this->entity_alias])->first();

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
            $directory = $company_id.'/media/services_categories/'.$services_category->id.'/img';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($services_category->photo_id) {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, $services_category->photo_id, $settings);
            } else {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);

            }
            $photo = $array['photo'];

            $services_category->photo_id = $photo->id;
        }

        $services_category->description = $request->description;
        $services_category->seo_description = $request->seo_description;

        // Модерация и системная запись
        $services_category->system_item = $request->system_item;
        $services_category->moderation = $request->moderation;

        // $services_category->parent_id = $request->parent_id;
        $services_category->editor_id = $user_id;

        // Если сменили тип категории продукции, то меняем его и всем вложенным элементам
        if (($services_category->category_status == 1) && ($services_category->services_type_id != $request->services_type_id)) {
            $services_category->services_type_id = $request->services_type_id;

            $services_categories = ServicesCategory::whereCategory_id($id)
            ->update(['services_mode_id' => $request->services_mode_id]);

        }

        $services_category->display = $request->display;

        // Делаем заглавной первую букву
        // $services_category->name = get_first_letter($request->name);
        //
        $services_category->name = $request->name;

        $services_category->save();

        if ($services_category) {

            return Redirect('/admin/services_categories/'.$services_category->type)->with('services_category_id', $services_category->id);

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
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $services_category = ServicesCategory::withCount('services_products')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $services_category);

        // Удаляем ajax
        // Проверяем содержит ли индустрия вложения
        $services_category_parent = ServicesCategory::moderatorLimit($answer)->whereParent_id($id)->first();

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Если содержит, то даем сообщение об ошибке
        if ($services_category_parent || ($services_category->services_products_count > 0)) {

            $result = [
                'error_status' => 1,
                'error_message' => 'Данная область содержит населенные пункты, удаление невозможно'
            ];
        } else {

            // Если нет, мягко удаляем
            if ($services_category->category_status == 1) {
                $parent = null;
            } else {
                $parent = $services_category->parent_id;
            }

            $services_category->editor_id = $user_id;
            $services_category->save();

            $services_category = ServicesCategory::destroy($id);

            if ($services_category) {

                // Переадресовываем на index
                return redirect()->action('ServicesCategoryController@index', ['id' => $parent]);
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
        $services_category = ServicesCategory::where(['name' => $request->name, 'company_id' => $user->company_id])->first();

        // Если такое название есть
        if ($services_category) {
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
    public function services_category_list(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, 'index');

        // Главный запрос
        $services_categories = ServicesCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->get(['id','name','parent_id'])
        ->keyBy('id')
        ->toArray();

        // dd($services_categories);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $services_categories_list = get_select_tree($services_categories, $request->parent, null, $request->id);
        // dd($services_categories_list);

        // Отдаем ajax
        echo json_encode($services_categories_list, JSON_UNESCAPED_UNICODE);
    }

    // ------------------------------------------------ Ajax -------------------------------------------------

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;

        foreach ($request->services_categories as $item) {
            ServicesCategory::where('id', $item)->update(['sort' => $i]);
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

        $item = ServicesCategory::where('id', $request->id)->update(['system_item' => $system]);

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

        $item = ServicesCategory::where('id', $request->id)->update(['display' => $display]);

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
        $answer = operator_right($this->entity_alias, $this->entity_alias, 'update');

        // ГЛАВНЫЙ ЗАПРОС:
        $services_category = ServicesCategory::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod('update'), $services_category);

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
            $get_settings = PhotoSetting::where(['entity' => $this->entity_alias])->first();

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
            $directory = $company_id.'/media/services_categories/'.$services_category->id.'/img';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($services_category->photo_id) {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, $services_category->photo_id, $settings);

            } else {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);

            }
            $photo = $array['photo'];

            $services_category->photo_id = $photo->id;
        }

        $services_category->description = $request->description;
        $services_category->seo_description = $request->seo_description;

        // Модерация и системная запись
        $services_category->system_item = $request->system_item;
        $services_category->moderation = $request->moderation;

        $services_category->parent_id = $request->parent_id;
        $services_category->editor_id = $user_id;

        // Если сменили тип категории продукции, то меняем его и всем вложенным элементам
        if (($services_category->category_status == 1) && ($services_category->services_type_id != $request->services_type_id)) {
            $services_category->services_type_id = $request->services_type_id;

            $services_categories = ServicesCategory::whereCategory_id($id)
            ->update(['services_type_id' => $request->services_type_id]);

        }

        $services_category->display = $request->display;

        // Делаем заглавной первую букву
        $services_category->name = get_first_letter($request->name);

        $services_category->save();

        if ($services_category) {

            // Переадресовываем на index
            return redirect()->action('ServicesCategoryController@index', ['id' => $services_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории продукции!'
            ];
        }
    }

    // Конкретная категория
    public function api_show(Request $request, $id)
    {

        // dd('lol');
        $site = Site::where('api_token', $request->token)->first();
        if ($site) {
            // return Cache::remember('staff', 1, function() use ($domen) {
            $services_category = ServicesCategory::with(['photo', 'services_products' => function ($query) {
                $query->with([ 'services' => function ($query) {
                    $query->where('display', 1);
                }])->where('display', 1);
            }])->findOrFail($id);
            return $services_category;
            // });
        } else {
            return json_encode('Нет доступа, холмс!', JSON_UNESCAPED_UNICODE);
        }
    }
}
