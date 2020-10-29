<?php

namespace App\Http\Controllers;

use App\Http\Requests\System\SubscriberRequest;
use App\Imports\SubscribersImport;
use App\MailingList;
use App\MailingListItem;
use App\Subscriber;
use Maatwebsite\Excel\Facades\Excel;

class SubscriberController extends Controller
{

    protected $entityAlias;
    protected $entityDependence;

    /**
     * SubscriberController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'subscribers';
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
        $this->authorize(getmethod(__FUNCTION__), Subscriber::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $subscribers = Subscriber::with([
            'sendedDispatches',
            'subscriberable',
            'client',
            'author',
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->filter()
//            ->orderBy('moderation', 'desc')
            ->oldest('created_at')
            ->paginate(30);
//        dd($subscribers);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.subscribers.index', compact('subscribers', 'pageInfo'));
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
        $this->authorize(getmethod(__FUNCTION__), Subscriber::class);

        $subscriber = Subscriber::make();

        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.subscribers.create', compact('subscriber', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SubscriberRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(SubscriberRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Subscriber::class);

        $data = $request->validated();
        if ($data['deny'] == 1) {
            $data['denied_at'] = now();
        }
        $subscriber = Subscriber::create($data);

        if ($subscriber) {
            return redirect()->route('subscribers.index');
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

        $subscriber = Subscriber::moderatorLimit($answer)
            ->find($id);
//        dd($subscriber);

        if (empty($subscriber)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $subscriber);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.subscribers.edit', compact('subscriber', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SubscriberRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(SubscriberRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $subscriber = Subscriber::moderatorLimit($answer)
            ->find($id);
        //        dd($subscriber);

        if (empty($subscriber)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $subscriber);

        $data = $request->validated();

        if ($data['deny'] == 1 && empty($subscriber->denied_at)) {
            $data['denied_at'] = now();
        }

        if ($data['deny'] == 0 && isset($subscriber->denied_at)) {
            $data['denied_at'] = null;
        }

        $result = $subscriber->update($data);

        if ($result) {
            return redirect()->route('subscribers.index');
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
        $subscriber = Subscriber::moderatorLimit($answer)
            ->find($id);

        if (empty($subscriber)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod('destroy'), $subscriber);

        $subscriber->archive();
        return redirect()->route('subscribers.index');
    }

    /**
     * Поиск
     *
     * @param $search
     * @return \Illuminate\Http\JsonResponse
     */
    public function search($search)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('index'));

        $results = Subscriber::where('email', 'LIKE', '%' . $search . '%')
            ->companiesLimit($answer)
            ->oldest('created_at')
            ->get();

        return response()->json($results);
    }

    /**
     * Импорт базы подписчиков из excel
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function excelImport()
    {
        Excel::import(new SubscribersImport(), request()->file('subscribers'));

        return redirect()
            ->route('subscribers.index');
    }

    public  function addToMailingList()
    {
        $mailingList = MailingList::with([
            'subscribers'
        ])
        ->find(request()->mailing_list_id);
//        dd($mailingList);

        $subscribersIds = $mailingList->subscribers->pluck('id');
//        dd($subscribersIds);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $subscribersIds = Subscriber::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->whereNotIn('id', $subscribersIds)
            ->valid()
            ->active()
            ->allow()
            ->filter()
//            ->orderBy('moderation', 'desc')
            ->oldest('created_at')
            ->get([
                'id'
            ])
            ->pluck('id');
//        dd($subscribersIds);

        if (count($subscribersIds) > 0) {
            $mailingList->subscribers()->attach($subscribersIds);
            $msg = 'Подписчики успешно добавлены в список рассылки';
        } else {
            $msg = null;
//            $msg = 'Подписчики уже добавлены в список рассылки';
        }

        return redirect()
            ->route('subscribers.index')
            ->with(['success' => $msg]);
    }
}
