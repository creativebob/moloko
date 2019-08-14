<?php

namespace App\Http\Controllers;

// Модели
use App\RubricatorsItem;
use App\Rubricator;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\RubricatorsItemRequest;

// Подключаем трейт записи и обновления категорий
use App\Http\Controllers\Traits\CategoryControllerTrait;

// Транслитерация
use Illuminate\Support\Str;

class RubricatorsItemController extends Controller
{

    // Настройки сконтроллера
    public function __construct(RubricatorsItem $rubricators_item)
    {
        $this->middleware('auth');
        $this->rubricators_item = $rubricators_item;
        $this->class = RubricatorsItem::class;
        $this->model = 'App\RubricatorsItem';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'modal';
    }

    // Используем трейт записи и обновления категорий
    use CategoryControllerTrait;

    public function index(Request $request, $rubricator_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $columns = [
            'id',
            'rubricator_id',
            'name',
            'parent_id',
            'company_id',
            'sort',
            'display',
            'system',
            'moderation',
            'author_id'
        ];

        $rubricators_items = RubricatorsItem::with([
            'childs',
        ])
        // ->moderatorLimit($answer)
        // ->companiesLimit($answer)
        // ->authors($answer)
        // ->systemItem($answer)
        // ->template($answer)
        ->where('rubricator_id', $rubricator_id)
        ->get($columns);
        // dd($rubricators_items);

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('common.accordions.categories_list',
                [
                    'items' => $rubricators_items,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $rubricators_items->count(),
                    'id' => $request->id,
                    'nested' => 'childs_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('rubricators_items.index', [
            'rubricators_items' => $rubricators_items,
            'page_info' => pageInfo($this->entity_alias),
            'rubricator_id' => $rubricator_id,
        ]);
    }

    public function create(Request $request, $rubricator_id)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('common.accordions.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление рубрики',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id,
            'rubricator_id' => $rubricator_id,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function store(RubricatorsItemRequest $request, $rubricator_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Заполнение и проверка основных полей в трейте
        $rubricators_item = $this->storeCategory($request);

        $rubricators_item->rubricator_id = $rubricator_id;

        $rubricators_item->save();

        if ($rubricators_item) {

            // Переадресовываем на index
            return redirect()->route('rubricators_items.index', ['rubricator_id' => $rubricator_id, 'id' => $rubricators_item->id]);

        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи пункта каталога!'
            ];

        }
    }


    public function show($id)
    {
        //
    }

    public function edit(Request $request, $rubricator_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $rubricators_item = RubricatorsItem::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $rubricators_item);

        return view('common.accordions.edit', [
            'item' => $rubricators_item,
            'entity' => $this->entity_alias,
            'title' => 'Редактирование рубрики',
            'parent_id' => $rubricators_item->parent_id,
            'category_id' => $rubricators_item->category_id,
            'rubricator_id' => $rubricator_id,
        ]);
    }

    public function update(RubricatorsItemRequest $request, $rubricator_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $rubricators_item = RubricatorsItem::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $rubricators_item);

        // Заполнение и проверка основных полей в трейте
        $rubricators_item = $this->updateCategory($request, $rubricators_item);

        // $rubricators_item->tag = empty($request->tag) ? Str::slug($request->name) : $request->tag;

        $rubricators_item->save();
        // dd($rubricators_item);

        if ($rubricators_item) {

            // Переадресовываем на index
            return redirect()->route('rubricators_items.index', ['rubricator_id' => $rubricator_id, 'id' => $rubricators_item->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлени пункта меню!'
            ];
        }
    }

    public function destroy(Request $request, $rubricator_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $rubricators_item = RubricatorsItem::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $rubricators_item);

        $parent_id = $rubricators_item->parent_id;

        $rubricators_item->delete();

        if ($rubricators_item) {

            // Переадресовываем на index
            return redirect()->route('rubricators_items.index', ['rubricator_id' => $rubricator_id, 'id' => $parent_id]);

        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении!'
            ];
        }
    }

    // ------------------------------ Ajax ---------------------------------------
    public function get_rubricators_items(Request $request, $rubricator_id)
    {
        return view('news.rubricators.select_rubricators_items', compact('rubricator_id'));
    }
}
