<?php

namespace App\Http\Controllers;

use App\Http\Requests\System\WorkplaceStoreRequest;
use App\Http\Requests\System\WorkplaceUpdateRequest;
use App\Workplace;

class WorkplaceController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * WorkplaceController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'workplaces';
        $this->entityDependence = true;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Workplace::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $workplaces = Workplace::with([
            'company',
            'filial',
            'author',
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->filials($answer)
//            ->orderBy('moderation', 'desc')
            ->oldest('sort')
            ->paginate(30);
//        dd($workplaces);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.workplaces.index', compact('workplaces', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param WorkplaceStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(WorkplaceStoreRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Workplace::class);

        $data = $request->validated();
        $workplace = Workplace::create($data);

        if ($workplace) {
            return redirect()->route('workplaces.edit', $workplace->id);
        } else {
            abort(403, __('errors.store'));
        }
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
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $workplace = Workplace::with([
            'outlet' => function ($q) {
                $q->with([
                   'staff',
                   'tools'
                ]);
            }
        ])
        ->moderatorLimit($answer)
            ->find($id);
//        dd($workplace);

        if (empty($workplace)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $workplace);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.workplaces.edit', compact('workplace', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param WorkplaceUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(WorkplaceUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $workplace = Workplace::moderatorLimit($answer)
            ->find($id);
        //        dd($workplace);

        if (empty($workplace)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $workplace);

        $data = $request->validated();

        $result = $workplace->update($data);

        $workplace->staff()->sync($request->staff);
        $workplace->tools()->sync($request->tools);

        if ($result) {
            return redirect()->route('workplaces.index');
        } else {
            abort(403, __('errors.update'));
        }
    }

    /**
     * Архивация указанного ресурса.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function archive($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('destroy'));

        // ГЛАВНЫЙ ЗАПРОС:
        $workplace = Workplace::moderatorLimit($answer)
            ->find($id);

        if (empty($workplace)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod('destroy'), $workplace);

        $workplace->archive();
        return redirect()->route('workplaces.index');
    }
}
