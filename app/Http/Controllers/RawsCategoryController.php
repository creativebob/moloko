<?php

namespace App\Http\Controllers;

// Модели
use App\RawsCategory;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\RawsCategoryRequest;

// Подключаем трейт записи и обновления категорий
use App\Http\Controllers\Traits\CategoryControllerTrait;

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

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $raws_categories = RawsCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->withCount('products')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('includes.menu_views.category_list',
                [
                    'items' => $raws_categories,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $raws_categories->count(),
                    'id' => $request->id,
                    'nested' => 'raws_products_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('includes.menu_views.index',
            [
                'items' => $raws_categories,
                'page_info' => pageInfo($this->entity_alias),
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
        $this->authorize(getmethod(__FUNCTION__), $this->class);

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
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $raws_category = RawsCategory::with([
            'mode',
            'one_metrics' => function ($q) {
                $q->with('unit', 'values');
            },
            'manufacturers',
        ])
        ->withCount('one_metrics')
        ->moderatorLimit($answer)
        ->findOrFail($id);
        // dd($raws_category);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raws_category);
        // dd($raws_category_metrics);

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('includes.metrics_category.properties_form', [
                'set_status' => $request->set_status,
                'category' => $raws_category
            ]);
        }
        // dd($properties_list);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $settings = getSettings($this->entity_alias);

        return view('raws_categories.edit', compact('raws_category', 'page_info', 'settings'));
    }

    public function update(RawsCategoryRequest $request, $id)
    {

        // TODO -- На 15.06.18 нет нормального решения отправки фотографий по ajax с методом "PATCH"

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $raws_category = RawsCategory::moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raws_category);

        // Заполнение и проверка основных полей в трейте
        $raws_category = $this->updateCategory($request, $raws_category);

        // Если сменили тип категории сырья, то меняем его и всем вложенным элементам
        if (($raws_category->parent_id == null) && ($raws_category->goods_mode_id != $request->goods_mode_id)) {
            $raws_category->goods_mode_id = $request->goods_mode_id;

            $raws_categories = RawsCategory::whereCategory_id($id)
            ->update(['raws_mode_id' => $request->raws_mode_id]);
        }

        $raws_category->save();

        if ($raws_category) {

            // Производители
            if (isset($request->manufacturers)) {
                $raws_category->manufacturers()->sync($request->manufacturers);
            } else {
                $raws_category->manufacturers()->detach();
            }

           // Переадресовываем на index
            return redirect()->route('raws_categories.index', ['id' => $raws_category->id]);
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
        $raws_category = RawsCategory::withCount('childs', 'products')
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raws_category);

        // Скрываем бога
        $raws_category->editor_id = hideGod($request->user());
        $raws_category->save();

        $parent_id = $raws_category->parent_id;

        $raws_category = RawsCategory::destroy($id);

        if ($raws_category) {

                // Переадресовываем на index
            return redirect()->route('raws_categories.index', ['id' => $parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении категории!'
            ];
        }
    }
}
