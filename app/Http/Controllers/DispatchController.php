<?php

namespace App\Http\Controllers;

use App\Dispatch;
use App\Http\Requests\System\DispatchRequest;
use Illuminate\Http\Request;

class DispatchController extends Controller
{

    // Настройки контроллера
    public function __construct(Dispatch $dispatch)
    {
        $this->middleware('auth');
        $this->dispatch = $dispatch;
        $this->class = Dispatch::class;
        $this->model = 'App\Dispatch';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    /**
     * Отображение списка ресурсов.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $dispatches = Dispatch::with([
            'author',
            'company',
        ])
            // ->withCount('pages')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->template($answer)
            ->booklistFilter($request)
//            ->filter($filters)
            // ->filter($request, 'author_id')
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc')
            ->paginate(30);


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            // 'author',               // Автор записи
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        return view('system.pages.dispatches.index',[
            'dispatches' => $dispatches,
            'pageInfo' => pageInfo($this->entity_alias),
            'filter' => $filter,
            'nested' => 'pages_count'
        ]);
    }

    /**
     * Показать форму для создания нового ресурса.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('system.pages.dispatches.create', [
            'dispatch' => Dispatch::make(),
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }

    /**
     * Сохранение созданного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DispatchRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $dispatch = Dispatch::create($data);

        if ($dispatch) {
            return redirect()->route('dispatches.index');
        } else {
            abort(403, 'Ошибка записи сайта');
        }
    }

    /**
     * Отображение указанного ресурса.
     *
     * @param  \App\Dispatch  $dispatch
     * @return \Illuminate\Http\Response
     */
    public function show(Dispatch $dispatch)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  \App\Dispatch  $dispatch
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $dispatch = Dispatch::moderatorLimit($answer)
            ->findOrFail($id);
        // dd($dispatch);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $dispatch);

        return view('system.pages.dispatches.edit', [
            'dispatch' => $dispatch,
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dispatch  $dispatch
     * @return \Illuminate\Http\Response
     */
    public function update(DispatchRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $dispatch = Dispatch::moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $dispatch);

        $data = $request->input();
        $result = $dispatch->update($data);

        if ($result) {
            return redirect()->route('dispatches.index');
        } else {
            abort(403, 'Ошибка обновления');
        }
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param  \App\Dispatch  $dispatch
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $dispatch = Dispatch::moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $dispatch);

        $dispatch->delete();

        if ($dispatch) {
            return redirect()->route('dispatches.index');
        } else {
            abort(403, 'Ошибка при удалении');
        }
    }
}
