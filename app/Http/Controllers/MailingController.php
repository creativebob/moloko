<?php

namespace App\Http\Controllers;

use App\Http\Requests\System\MailingRequest;
use App\Mailing;

class MailingController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * MailingController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'mailings';
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
        $this->authorize(getmethod(__FUNCTION__), Mailing::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $mailings = Mailing::with([
            'list',
            'template',
            'author',
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
//            ->orderBy('moderation', 'desc')
            ->oldest('sort')
            ->paginate(30);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.mailings.index', compact('mailings', 'pageInfo'));
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
        $this->authorize(getmethod(__FUNCTION__), Mailing::class);

        $mailing = Mailing::make();

        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.mailings.create', compact('mailing', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MailingRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(MailingRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Mailing::class);

        $data = $request->validated();
        $mailing = Mailing::create($data);

        if ($mailing) {
            return redirect()->route('mailings.index');
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

        $mailing = Mailing::moderatorLimit($answer)
            ->find($id);
//        dd($mailing);

        if (empty($mailing)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $mailing);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.mailings.edit', compact('mailing', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MailingRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(MailingRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $mailing = Mailing::moderatorLimit($answer)
            ->find($id);
        //        dd($mailing);

        if (empty($mailing)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $mailing);

        $data = $request->validated();
        $result = $mailing->update($data);

        if ($result) {
            return redirect()->route('mailings.index');
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

        $mailing = Mailing::moderatorLimit($answer)
            ->find($id);
        //        dd($mailing);

        if (empty($mailing)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod('destroy'), $mailing);

        $res = $mailing->delete();

        if ($res) {
            return redirect()->route('mailings.index');
        } else {
            abort(403, __('errors.destroy'));
        }
    }
}
