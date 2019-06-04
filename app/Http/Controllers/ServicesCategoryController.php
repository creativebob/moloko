<?php

namespace App\Http\Controllers;

// Модели
use App\ServicesCategory;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\ServicesCategoryRequest;

// Подключаем трейт записи и обновления категорий
use App\Http\Controllers\Traits\CategoryControllerTrait;
// use App\Http\Controllers\Traits\DirectionTrait;

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

    // Используем трейт записи и обновления категорий
    use CategoryControllerTrait;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $services_categories = ServicesCategory::with([
            'services',
            'childs',
            'groups',
            // 'direction'
        ])
        ->withCount('childs')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        // ->withCount('products')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();
        // dd($services_categories);

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('common.accordions.categories_list',
                [
                    'items' => $services_categories,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $services_categories->count(),
                    'id' => $request->id,
                    'nested' => 'childs_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('common.accordions.index',
            [
                'items' => $services_categories,
                'page_info' => pageInfo($this->entity_alias),
                'entity' => $this->entity_alias,
                'class' => $this->model,
                'type' => $this->type,
                'id' => $request->id,
                'nested' => 'childs_count',
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

        return view('common.accordions.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление категории услуг',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function store(ServicesCategoryRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Заполнение и проверка основных полей в трейте
        $services_category = $this->storeCategory($request);

        // Тип услуг
        $services_category->processes_type_id = $request->processes_type_id;

        $services_category->save();

        if ($services_category) {
            // Переадресовываем на index
            return redirect()->route('services_categories.index', ['id' => $services_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории услуг!',
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
        $services_category = ServicesCategory::with([
            'workflows.process.group.unit',
            'workflows.category',
            'manufacturers',
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);
        // dd($services_category);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $services_category);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $settings = getSettings($this->entity_alias);

        return view('products.processes_categories.common.edit.edit', [
            'title' => 'Редактирование категории услуг',
            'category' => $services_category,
            'page_info' => $page_info,
            'settings' => $settings,
            'entity' => $this->entity_alias,
        ]);
    }

    public function update(ServicesCategoryRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $services_category = ServicesCategory::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $services_category);

        // Заполнение и проверка основных полей в трейте
        $services_category = $this->updateCategory($request, $services_category);
        // dd($request);

        $services_category->processes_type_id = $request->processes_type_id;

        // // Если сменили тип категории сырья, то меняем его и всем вложенным элементам
        // if (($services_category->parent_id == null) && ($services_category->processes_type_id != $request->processes_type_id)) {
        //     $services_category->processes_type_id = $request->processes_type_id;

        //     $services_categories = ServicesCategory::whereCategory_id($id)
        //     ->update(['processes_type_id' => $request->processes_type_id]);
        // }

        $services_category->save();

        if ($services_category) {

            // Производители
            $services_category->manufacturers()->sync($request->manufacturers);

            // Cостав
            $services_category->workflows()->sync($request->workflows);

            // Переадресовываем на index
            return redirect()->route('services_categories.index', ['id' => $services_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории услуг!'
            ];
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $services_category = ServicesCategory::with([
            'childs',
            'services'
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $services_category);

        // Скрываем бога
        $services_category->editor_id = hideGod($request->user());
        $services_category->save();

        $parent_id = $services_category->parent_id;

        $services_category->delete();

        if ($services_category) {

            // Переадресовываем на index
            return redirect()->route('services_categories.index', ['id' => $parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении категории!'
            ];
        }
    }
}
