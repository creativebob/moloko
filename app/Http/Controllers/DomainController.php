<?php

namespace App\Http\Controllers;

use App\Domain;
use App\Entity;
use Illuminate\Http\Request;
use App\Http\Requests\System\DomainRequest;
use Illuminate\Support\Facades\View;

class DomainController extends Controller
{

    /**
     * DomainController constructor.
     * @param Domain $domain
     */
    public function __construct(Domain $domain)
    {
        $this->middleware('auth');
        $this->domain = $domain;
        $this->class = Domain::class;
        $this->model = 'App\Domain';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;

    }

    /**
     * Отображение списка ресурсов.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
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

        $domains = Domain::with([
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

        return view('system.pages.domains.index',[
            'domains' => $domains,
            'pageInfo' => pageInfo($this->entity_alias),
            'filter' => $filter,
            'nested' => 'pages_count'
        ]);
    }

    /**
     * Показать форму для создания нового ресурса.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('system.pages.domains.create', [
            'domain' => Domain::make(),
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }

    /**
     * Сохранение созданного ресурса в хранилище.
     *
     * @param DomainRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(DomainRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $domain = Domain::create($data);

        if ($domain) {

            $departments = session('access.all_rights.index-departments-allow');
            if ($departments) {
                $domain->filials()->sync($request->filials);
            }

            return redirect()
                ->route('domains.edit', [$domain->id])
                ->with(['success' => 'Успешно сохранено']);
        } else {
            return back()
                ->withErrors(['msg' => 'Ошибка сохранения'])
                ->withInput();
        }

//        if ($domain) {
//            return redirect()->route('domains.index');
//        } else {
//            abort(403, 'Ошибка записи сайта');
//        }
    }

    /**
     * Отображение указанного ресурса.
     *
     * @param  \App\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function show(Domain $domain)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $domain = Domain::with([
            'plugins.account.source_service' => function ($q) {
                $q->with([
                    'source:id,name'
                ])
                ->select([
                    'id',
                    'name',
                    'source_id'
                ]);
            }
        ])
        ->moderatorLimit($answer)
            ->findOrFail($id);
        // dd($domain);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $domain);

        return view('system.pages.domains.edit', [
            'domain' => $domain,
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param DomainRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(DomainRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $domain = Domain::moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $domain);

        $data = $request->input();
        $result = $domain->update($data);

        if ($result) {

            $departments = session('access.all_rights.index-departments-allow');
            if ($departments) {
                $domain->filials()->sync($request->filials);
            }

            return redirect()
                ->route('domains.edit', $domain->id)
                ->with(['success' => 'Успешно сохранено']);
        } else {
            return back()
                ->withErrors(['msg' => 'Ошибка сохранения'])
                ->withInput();
        }

//        if ($result) {
//            return redirect()->route('domains.index');
//        } else {
//            abort(403, 'Ошибка обновления сайта');
//        }
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $domain = Domain::with([
        ])
            ->moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $domain);

        $domain->delete();

        if ($domain) {
            return redirect()->route('domains.index');
        } else {
            abort(403, 'Ошибка при удалении');
        }
    }
}
