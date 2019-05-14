<?php

namespace App\Http\Controllers;

// Модели
use App\RoomsCategory;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\RoomsCategoryRequest;

// Подключаем трейт записи и обновления категорий
use App\Http\Controllers\Traits\CategoryControllerTrait;

class RoomsCategoryController extends Controller
{

    // Настройки сконтроллера
    public function __construct(RoomsCategory $rooms_category)
    {
        $this->middleware('auth');
        $this->rooms_category = $rooms_category;
        $this->class = RoomsCategory::class;
        $this->model = 'App\RoomsCategory';
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

        $rooms_categories = RoomsCategory::with([
            'rooms',
            'childs',
            'groups'
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
        // dd($rooms_categories);

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('includes.menu_views.category_list',
                [
                    'items' => $rooms_categories,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $rooms_categories->count(),
                    'id' => $request->id,
                    // 'nested' => 'rooms_products_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('includes.menu_views.index',
            [
                'items' => $rooms_categories,
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

        return view('includes.menu_views.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление категории помещений',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    public function store(RoomsCategoryRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Заполнение и проверка основных полей в трейте
        $rooms_category = $this->storeCategory($request);

        $rooms_category->save();

        if ($rooms_category) {
            // Переадресовываем на index
            return redirect()->route('rooms_categories.index', ['id' => $rooms_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории помещений!',
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
        $rooms_category = RoomsCategory::with([
            // 'mode',
            // 'one_metrics' => function ($q) {
            //     $q->with('unit', 'values');
            // },
            'manufacturers',
        ])
        // ->withCount('one_metrics')
        ->moderatorLimit($answer)
        ->findOrFail($id);
        // dd($rooms_category);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $rooms_category);
        // dd($rooms_category_metrics);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $settings = getSettings($this->entity_alias);

        // dd($goods_category->direction);
        return view('tmc_categories.edit.edit', [
            'title' => 'Редактирование категории помещений',
            'category' => $rooms_category,
            'page_info' => $page_info,
            'settings' => $settings,
            'entity' => $this->entity_alias,
        ]);
    }

    public function update(RoomsCategoryRequest $request, $id)
    {

        // TODO -- На 15.06.18 нет нормального решения отправки фотографий по ajax с методом "PATCH"

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $rooms_category = RoomsCategory::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $rooms_category);

        // Заполнение и проверка основных полей в трейте
        $rooms_category = $this->updateCategory($request, $rooms_category);

        $rooms_category->save();

        if ($rooms_category) {

            // Производители
            $rooms_category->manufacturers()->sync($request->manufacturers);

           // Переадресовываем на index
            return redirect()->route('rooms_categories.index', ['id' => $rooms_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении категории помещений!'
            ];
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $rooms_category = RoomsCategory::with([
            'childs',
            'rooms'
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $rooms_category);

        // Скрываем бога
        $rooms_category->editor_id = hideGod($request->user());
        $rooms_category->save();

        $parent_id = $rooms_category->parent_id;

        $rooms_category->delete();

        if ($rooms_category) {

                // Переадресовываем на index
            return redirect()->route('rooms_categories.index', ['id' => $parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении категории!'
            ];
        }
    }
}
