<?php

namespace App\Http\Controllers;

use App\Http\Requests\System\SectorUpdateRequest;
use App\Http\Requests\System\SectorStoreRequest;
use App\Sector;
use Illuminate\Http\Request;

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

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

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

            return view('system.common.categories.index.categories_list',
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
        return view('system.common.categories.index.index',
            [
                'items' => $sectors,
                'pageInfo' => pageInfo($this->entity_alias),
                'entity' => $this->entity_alias,
                'class' => $this->model,
                'type' => $this->type,
                'id' => $request->id,
                'nested' => 'companies_count',
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

        return view('system.common.categories.create.modal.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление сектора',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    public function store(SectorStoreRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $sector = (new $this->class())->create($data);

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

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $sector = Sector::moderatorLimit($answer)
        ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $sector);

        return view('system.common.categories.edit', [
            'item' => $sector,
            'entity' => $this->entity_alias,
            'title' => 'Редактирование сектора',
            'parent_id' => $sector->parent_id,
            'category_id' => $sector->category_id
        ]);
    }

    public function update(SectorUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $sector = Sector::moderatorLimit($answer)
        ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $sector);

        $data = $request->input();
        $result = $sector->update($data);

        if ($result) {
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

        $sector = Sector::with('childs', 'companies')->moderatorLimit($answer)->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $sector);

        // Скрываем бога
        $sector->editor_id = hideGod($request->user());
        $sector->save();

        $parent_id = $sector->parent_id;

        $sector->delete();

        if ($sector) {

            // Переадресовываем на index
            return redirect()->route('sectors.index', ['id' => $parent_id]);

        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении!'
            ];
        }
    }
}
