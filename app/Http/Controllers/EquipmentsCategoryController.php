<?php

namespace App\Http\Controllers;

use App\Http\Requests\EquipmentsCategoryUpdateRequest;
use App\Http\Requests\EquipmentsCategoryStoreRequest;
use App\EquipmentsCategory;
use Illuminate\Http\Request;


class EquipmentsCategoryController extends Controller
{

    // Настройки сконтроллера
    public function __construct(EquipmentsCategory $equipments_category)
    {
        $this->middleware('auth');
        $this->equipments_category = $equipments_category;
        $this->class = EquipmentsCategory::class;
        $this->model = 'App\EquipmentsCategory';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'edit';
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $equipments_categories = EquipmentsCategory::with([
            'equipments',
            'childs',
            'groups'
        ])
        ->withCount('childs')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();
//        dd($equipments_categories);

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('system.common.accordions.categories_list',
                [
                    'items' => $equipments_categories,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $equipments_categories->count(),
                    'id' => $request->id,
                    // 'nested' => 'raws_products_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('system.common.accordions.index',
            [
                'items' => $equipments_categories,
                'page_info' => pageInfo($this->entity_alias),
                'entity' => $this->entity_alias,
                'class' => $this->model,
                'type' => $this->type,
                'id' => $request->id,
                'nested' => 'childs_count',
                'filter' => setFilter($this->entity_alias, $request, [
                    'booklist',
                ]),
            ]
        );
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('system.common.accordions.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление категории оборудования',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    public function store(EquipmentsCategoryStoreRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $equipments_category = (new $this->class())->create($data);

        if ($equipments_category) {
            // Переадресовываем на index
            return redirect()->route('equipments_categories.index', ['id' => $equipments_category->id]);
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
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $equipments_category = EquipmentsCategory::with([
            'manufacturers',
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);
        // dd($equipments_category);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $equipments_category);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $settings = getSettings($this->entity_alias);

        // При добавлении метрики отдаем ajax новый список свойст и метрик
        if ($request->ajax()) {
            return view('products.common.metrics.properties_list', [
                'category' => $equipments_category,
                'page_info' => $page_info,
            ]);
        }

        // dd($goods_category->direction);
        return view('products.articles_categories.common.edit.edit', [
            'title' => 'Редактирование категории оборудования',
            'category' => $equipments_category,
            'page_info' => $page_info,
            'settings' => $settings,
            'entity' => $this->entity_alias,
        ]);
    }

    public function update(EquipmentsCategoryUpdateRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $equipments_category = EquipmentsCategory::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $equipments_category);

        // Заполнение и проверка основных полей в трейте
        $data = $request->input();
        $result = $equipments_category->update($data);

        if ($result) {

            $equipments_category->manufacturers()->sync($request->manufacturers);
            $equipments_category->metrics()->sync($request->metrics);

           // Переадресовываем на index
            return redirect()->route('equipments_categories.index', ['id' => $equipments_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении категории сырья!'
            ];
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $equipments_category = EquipmentsCategory::with([
            'childs',
        'equipments'
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $equipments_category);

        $parent_id = $equipments_category->parent_id;

        $equipments_category->delete();

        if ($equipments_category) {

            // Переадресовываем на index
            return redirect()->route('equipments_categories.index', ['id' => $parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении категории!'
            ];
        }
    }
}
