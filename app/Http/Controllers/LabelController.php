<?php

namespace App\Http\Controllers;

use App\Http\Requests\System\LabelRequest;
use App\Label;

class LabelController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * WorkplaceController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'labels';
        $this->entityDependence = false;
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
        $this->authorize(getmethod(__FUNCTION__), Label::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $labels = Label::with([
            'author',
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
//            ->orderBy('moderation', 'desc')
            ->oldest('sort')
            ->paginate(30);
//        dd($labels);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.labels.index', compact('labels', 'pageInfo'));
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
        $this->authorize(getmethod(__FUNCTION__), Label::class);

        $label = Label::make();
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.labels.create', compact('label', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LabelRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(LabelRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Label::class);

        $data = $request->validated();
        $label = Label::create($data);

        if ($label) {
            return redirect()->route('labels.index');
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

        $label = Label::moderatorLimit($answer)
            ->find($id);
//        dd($label);

        if (empty($label)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $label);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.labels.edit', compact('label', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LabelRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(LabelRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $label = Label::moderatorLimit($answer)
            ->find($id);
        //        dd($label);

        if (empty($label)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $label);

        $data = $request->validated();
        $result = $label->update($data);

        if ($result) {
            return redirect()->route('labels.index');
        } else {
            abort(403, __('errors.update'));
        }
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
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $label = Label::moderatorLimit($answer)
            ->find($id);
        //        dd($label);

        if (empty($label)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod('destroy'), $label);

        $res = $label->delete();

        if ($res) {
            return redirect()->route('labels.index');
        } else {
            abort(403, __('errors.destroy'));
        }
    }

    public function get()
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $labels = Label::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->oldest('sort')
            ->get([
                'id',
                'name'
            ]);
//        dd($labels);

        return response()->json($labels);
    }
}
