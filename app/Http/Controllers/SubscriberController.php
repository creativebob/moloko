<?php

namespace App\Http\Controllers;

use App\Imports\SubscribersImport;
use App\Subscriber;
use Illuminate\Http\Request;
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
            'subscriberable',
            'client',
            'dispatches',
            'author',
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->filter()
//            ->orderBy('moderation', 'desc')
            ->oldest('sort')
            ->paginate(30);

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Subscriber  $subscriber
     * @return \Illuminate\Http\Response
     */
    public function edit(Subscriber $subscriber)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Subscriber  $subscriber
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subscriber $subscriber)
    {
        //
    }

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

        return redirect()->route('subscribers.index');
    }
}
