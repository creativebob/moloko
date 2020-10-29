<?php

namespace App\Http\Controllers;

use App\Http\Requests\System\MailingListRequest;
use App\MailingList;

class MailingListController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * MailingListController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'mailing_lists';
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
        $this->authorize(getmethod(__FUNCTION__), MailingList::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $mailingLists = MailingList::with([
            'author',
        ])
            ->withCount([
                'items'
            ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
//            ->orderBy('moderation', 'desc')
            ->oldest('created_at')
            ->paginate(30);
//        dd($mailingLists);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.mailing_lists.index', compact('mailingLists', 'pageInfo'));
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
        $this->authorize(getmethod(__FUNCTION__), MailingList::class);

        $mailingList = MailingList::make();

        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.mailing_lists.create', compact('mailingList', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MailingListRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(MailingListRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), MailingList::class);

        $data = $request->validated();
        $mailingList = MailingList::create($data);

        if ($mailingList) {
            return redirect()->route('mailing_lists.index');
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

        $mailingList = MailingList::moderatorLimit($answer)
            ->find($id);
//        dd($mailingList);

        if (empty($mailingList)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $mailingList);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.mailing_lists.edit', compact('mailingList', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MailingListRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(MailingListRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $mailingList = MailingList::moderatorLimit($answer)
            ->find($id);
        //        dd($mailingList);

        if (empty($mailingList)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $mailingList);

        $data = $request->validated();
        $result = $mailingList->update($data);

        if ($result) {
            return redirect()->route('mailing_lists.index');
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
        $mailingList = MailingList::moderatorLimit($answer)
            ->find($id);

        if (empty($mailingList)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod('destroy'), $mailingList);

        $mailingList->archive();

        return redirect()->route('mailing_lists.index');
    }
}
