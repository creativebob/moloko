<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Http\Controllers\System\Traits\Timestampable;
use App\Http\Requests\System\CampaignStoreRequest;
use App\Http\Requests\System\CampaignUpdateRequest;
use App\Notifications\System\Notifications;
use Illuminate\Http\Request;

class CampaignController extends Controller
{

    protected $entityAlias;
    protected $entityDependence;

    /**
     * CampaignController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'campaigns';
        $this->entityDependence = true;
    }

    use Timestampable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Campaign::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // dd($answer);

        $campaigns = Campaign::with([
            'author'
        ])
            ->where('archive', false)
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->filials($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->orderByDesc('moderation')
            ->orderBy('sort')
            ->paginate(30);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.campaigns.index', compact('campaigns', 'pageInfo'));
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
        $this->authorize(getmethod(__FUNCTION__), Campaign::class);

        $campaign = Campaign::make();

        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.campaigns.create', compact('campaign', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CampaignRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CampaignStoreRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Campaign::class);

        $data = $request->input();

        $campaign = Campaign::create($data);

        if ($campaign) {
            return redirect()->route('campaigns.index');
        } else {
            abort(403, __('errors.store'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     */
    public function show($id)
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
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $campaign = Campaign::moderatorLimit($answer)
            ->find($id);

        if (empty($campaign)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $campaign);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.campaigns.edit', compact('campaign', 'pageInfo'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CampaignRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(CampaignUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $campaign = Campaign::moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $campaign);

        $data = $request->input();

        $result = $campaign->update($data);

        if ($result) {
            return redirect()->route('campaigns.index');
        } else {
            abort(403, 'Ошибка обновления');
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
        //
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
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $campaign = Campaign::with('entity')
        ->moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod('destroy'), $campaign);

        $campaign->update([
            'archive' => true,
            'is_actual' => false
        ]);

        if ($campaign) {
            return redirect()->route('campaigns.index');
        } else {
            abort(403, 'Ошибка при архивации');
        }
    }
}
