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
     * Display a listing of the resource.
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
     * Show the form for creating a new resource.
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
     * Store a newly created resource in storage.
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
     * Display the specified resource.
     *
     * @param  \App\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function show(Domain $domain)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
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
            },
            'files'
        ])
        ->moderatorLimit($answer)
            ->find($id);
        // dd($domain);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $domain);

        return view('system.pages.domains.edit', [
            'domain' => $domain,
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }

    /**
     * Update the specified resource in storage.
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
            ->find($id);

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
     * Remove the specified resource from storage.
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
            ->find($id);

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
