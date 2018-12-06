<?php

namespace App\Http\Controllers;

// Модели
use App\RawsCategory;


use App\Raw;
use App\RawsMode;
use App\RawsProduct;

use App\Property;
use App\EntitySetting;
use App\Company;
use App\UnitsCategory;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\RawsCategoryRequest;

// Подключаем трейт записи и обновления категорий
use App\Http\Controllers\Traits\CategoryControllerTrait;

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
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'edit';
    }

    // Используем трейт записи и обновления категорий
    use CategoryControllerTrait;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('includes.menu_views.category_list',
                [
                    'items' => $this->raws_category->getIndex($request, $answer),
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => count($this->raws_category->getIndex($request, $answer)),
                    'id' => $request->id,
                    'nested' => 'raws_products_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('includes.menu_views.index',
            [
                'items' => $this->raws_category->getIndex($request, $answer),
                'page_info' => $page_info,
                'entity' => $this->entity_alias,
                'class' => $this->model,
                'type' => $this->type,
                'id' => $request->id,
                'nested' => 'raws_products_count',
            ]
        );
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('includes.menu_views.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление категории сырья',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    public function store(RawsCategoryRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), RawsCategory::class);

        // Заполнение и проверка основных полей в трейте
        $raws_category = $this->storeCategory($request);

        // Режим товаров
        $raws_category->raws_mode_id = $request->raws_mode_id;

        $raws_category->save();

        if ($raws_category) {
            // Переадресовываем на index
            return redirect()->route('raws_categories.index', ['id' => $raws_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории сырья!',
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
        $answer_raws_categories = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

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
            $answer = operator_right($this->entity_alias, $this->entity_dependence, 'index');

            // Главный запрос
            $raws_categories = RawsCategory::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->where('id', $request->category_id)
            ->orWhere('category_id', $request->category_id)
            ->orderBy('sort', 'asc')
            ->get(['id', 'name', 'parent_id'])
            ->keyBy('id')
            ->toArray();

            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
            // $raws_categories_list = get_select_tree($raws_categories, $raws_category->parent_id, null, $raws_category->id);

            // dd($raws_category);

            return view('raws_categories.edit', compact('raws_category', 'page_info', 'properties', 'properties_list', 'raws_category_metrics', 'raws_modes_list', 'units_categories_list', 'units_list'));
        }
    }

    public function update(RawsCategoryRequest $request, $id)
    {

        // TODO -- На 15.06.18 нет нормального решения отправки фотографий по ajax с методом "PATCH"

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $raws_category = $this->raws_category->getItem($id, operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)));

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raws_category);

        // Заполнение и проверка основных полей в трейте
        $raws_category = $this->updateCategory($request, $raws_category);

        // Если сменили тип категории продукции, то меняем его и всем вложенным элементам
        if (($raws_category->parent_id == null) && ($raws_category->raws_type_id != $request->raws_type_id)) {
            $raws_category->raws_type_id = $request->raws_type_id;

            $raws_categories = RawsCategory::whereCategory_id($id)
            ->update(['raws_mode_id' => $request->raws_mode_id]);
        }

        $raws_category->save();

        if ($raws_category) {

           // Переадресовываем на index
            return redirect()->route('raws_categories.index', ['id' => $raws_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории сырья!'
            ];
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

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
                'error_message' => 'Категория не пуста!'
            ];
        } else {

            // Если нет, мягко удаляем
            $parent = $raws_category->parent_id;

            $raws_category->editor_id = $user_id;
            $raws_category->save();

            $raws_category = RawsCategory::destroy($id);

            if ($raws_category) {

                // Переадресовываем на index
                return redirect()->route('raws_categories.index', ['id' => $parent]);
            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при записи сектора!'
                ];
            }
        }
    }
}
