<?php

namespace App\Http\Controllers;

// Модели
use App\Sector;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\SectorRequest;

// Подключаем трейт записи и обновления категорий
use App\Http\Controllers\Traits\CategoryControllerTrait;

// Транслитерация
use Transliterate;

class SectorController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Sector $sector)
    {
        $this->middleware('auth');
        $this->sector = $sector;
        $this->class = Sector::class;
        $this->model = 'App\Sector';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'modal';
    }

    // Используем трейт записи и обновления категорий
    use CategoryControllerTrait;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $sectors = Sector::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->booklistFilter($request)
        ->withCount('companies')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('includes.menu_views.category_list',
                [
                    'items' => $sectors,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $sectors->count(),
                    'id' => $request->id,
                    'nested' => 'companies_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('includes.menu_views.index',
            [
                'items' => $sectors,
                'page_info' => pageInfo($this->entity_alias),
                'entity' => $this->entity_alias,
                'class' => $this->model,
                'type' => $this->type,
                'filter' => $filter,
                'id' => $request->id,
                'nested' => 'companies_count',
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
            'title' => 'Добавление сектора',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    public function store(SectorRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Заполнение и проверка основных полей в трейте
        $sector = $this->storeCategory($request);

        // Тег
        $sector->tag = empty($request->tag) ? Transliterate::make($request->name, ['type' => 'url', 'lowercase' => true]) : $request->tag;

        $sector->save();

        if ($sector) {
            // Переадресовываем на index
            return redirect()->route('sectors.index', ['id' => $sector->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи сектора!'
            ];
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {

        $sector = Sector::moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $sector);

        return view('includes.menu_views.edit', [
            'item' => $sector,
            'entity' => $this->entity_alias,
            'title' => 'Редактирование сектора',
            'parent_id' => $sector->parent_id,
            'category_id' => $sector->category_id
        ]);
    }

    public function update(SectorRequest $request, $id)
    {

        $sector = Sector::moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $sector);

        // Заполнение и проверка основных полей в трейте
        $sector = $this->updateCategory($request, $sector);

        $sector->tag = empty($request->tag) ? Transliterate::make($request->name, ['type' => 'url', 'lowercase' => true]) : $request->tag;

        $sector->save();

        if ($sector) {
            // Переадресовываем на index
            return redirect()->route('sectors.index', ['id' => $sector->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении сектора!'
            ];
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $sector = Sector::with('childs', 'companies')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $sector);

        // Проверяем содержит ли сектор вложения
        $sectors_count = Sector::moderatorLimit($answer)
        ->whereParent_id($sector->id)
        ->count();

        // Если содержит, то даем сообщение об ошибке
        if ($sectors_count > 0) {
            $result = [
                'error_status' => 1,
                'error_message' => 'Категория содержит вложенные элементы, удаление невозможно!'
            ];
        } else {

            // Скрываем бога
            $sector->editor_id = hideGod($request->user());
            $sector->save();

            $parent_id = $sector->parent_id;

            $sector = Sector::destroy($id);

            if ($sector) {
                // Переадресовываем на index
                return redirect()->route('sectors.index', ['id' => $parent_id]);
            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при удалении сектора!'
                ];
            }
        }
    }
}
