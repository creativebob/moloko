<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\ToolsCategoryUpdateRequest;
use App\Http\Requests\System\ToolsCategoryStoreRequest;
use App\ToolsCategory;
use Illuminate\Http\Request;


class ToolsCategoryController extends Controller
{

    // Настройки сконтроллера
    public function __construct(ToolsCategory $tools_category)
    {
        $this->middleware('auth');
        $this->tools_category = $tools_category;
        $this->class = ToolsCategory::class;
        $this->model = 'App\ToolsCategory';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'page';
    }

    use Photable;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $tools_categories = ToolsCategory::with([
            'tools',
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
//        dd($tools_categories);

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('system.common.categories.index.categories_list',
                [
                    'items' => $tools_categories,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $tools_categories->count(),
                    'id' => $request->id,
                    // 'nested' => 'raws_products_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('system.common.categories.index.index',
            [
                'items' => $tools_categories,
                'pageInfo' => pageInfo($this->entity_alias),
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

        return view('system.common.categories.create.modal.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление категории инструментов',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    public function store(ToolsCategoryStoreRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $tools_category = (new $this->class())->create($data);

        if ($tools_category) {
            // Переадресовываем на index
            return redirect()->route('tools_categories.index', ['id' => $tools_category->id]);
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
        $tools_category = ToolsCategory::with([
            'manufacturers',
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);
        // dd($tools_category);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $tools_category);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        $settings = getPhotoSettings($this->entity_alias);

        // При добавлении метрики отдаем ajax новый список свойст и метрик
        if ($request->ajax()) {
            return view('products.common.metrics.properties_list', [
                'category' => $tools_category,
                'pageInfo' => $pageInfo,
            ]);
        }

        // dd($goods_category->direction);
        return view('products.articles_categories.common.edit.edit', [
            'title' => 'Редактирование категории оборудования',
            'category' => $tools_category,
            'pageInfo' => $pageInfo,
            'settings' => $settings,
            'entity' => $this->entity_alias,
        ]);
    }

    public function update(ToolsCategoryUpdateRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $tools_category = ToolsCategory::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $tools_category);

        // Заполнение и проверка основных полей в трейте
        $data = $request->input();
        $data['photo_id'] = $this->getPhotoId($request, $tools_category);
        $result = $tools_category->update($data);

        if ($result) {

            $tools_category->manufacturers()->sync($request->manufacturers);
            $tools_category->metrics()->sync($request->metrics);

           // Переадресовываем на index
            return redirect()->route('tools_categories.index', ['id' => $tools_category->id]);
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
        $tools_category = ToolsCategory::with([
            'childs',
        'tools'
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $tools_category);

        $parent_id = $tools_category->parent_id;

        $tools_category->delete();

        if ($tools_category) {

            // Переадресовываем на index
            return redirect()->route('tools_categories.index', ['id' => $parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении категории!'
            ];
        }
    }
}
