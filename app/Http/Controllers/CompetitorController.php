<?php

namespace App\Http\Controllers;

use App\Company;
use App\Competitor;
use App\Http\Controllers\System\Traits\Companable;
use App\Http\Controllers\System\Traits\Directorable;
use App\Http\Controllers\System\Traits\Locationable;
use App\Http\Controllers\System\Traits\Phonable;
use App\Http\Controllers\Traits\Photable;
use Illuminate\Http\Request;

class CompetitorController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * CompetitorController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'competitors';
        $this->entityDependence = false;
    }

    use Locationable,
        Phonable,
        Photable,
        Companable,
        Directorable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Competitor::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $competitors = Competitor::with([
            'author',
            'company' => function ($q) {
                $q->with([
                    'location.city',
                    'sector',
                    'legal_form'
                ]);
            },
        ])
            ->companiesLimit($answer)
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->oldest('sort')
            ->paginate(30);
//        dd($competitors);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.competitors.index', compact('competitors', 'pageInfo'));
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
        $this->authorize(getmethod(__FUNCTION__), Competitor::class);
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        $competitor = Competitor::make();
        $company = Company::make();

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.competitors.create', compact('competitor', 'company', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Competitor::class);
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        logs('companies')->info('============ НАЧАЛО СОЗДАНИЯ КОНКУРЕНТА ===============');

        $company = $this->storeCompany();

        if ($request->set_user == 1) {
            $this->getDirector($company, $this->entityAlias);
        }

        $data = $request->input();
        $data['competitor_id'] = $company->id;
        $data['description'] = $request->competitor_description;

        $competitor = Competitor::create($data);

        $this->setStatuses($company);

        logs('companies')->info("Создан конкурент. Id: [{$competitor->id}]");
        logs('companies')->info('============ КОНЕЦ СОЗДАНИЯ КОНКУРЕНТА ===============

        ');

        return redirect()->route('competitors.index');
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

        // ГЛАВНЫЙ ЗАПРОС:
        $competitor = Competitor::with([
            'company' => function ($q) {
                $q->with([
                    'location.city',
                    'schedules.worktimes',
                    'sector',
                    'processes_types'
                ]);
            },
        ])
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($competitor);

        if (empty($competitor)) {
            abort(403, __('errors.not_found'));
        }

        $company = $competitor->company;
//        dd($company);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $competitor);
        $this->authorize(getmethod(__FUNCTION__), $company);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.competitors.edit', compact('competitor', 'company', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $competitor = Competitor::with([
            'company' => function ($q) {
                $q->with([
                    'location.city',
                    'schedules.worktimes'
                ]);
            }
        ])
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($competitor);

        if (empty($competitor)) {
            abort(403, __('errors.not_found'));
        }

        $company = $competitor->company;

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $competitor);
        $this->authorize(getmethod(__FUNCTION__), $company);

        logs('companies')->info('============ НАЧАЛО ОБНОВЛЕНИЯ КОНКУРЕНТА ===============');

        // TODO - 15.09.20 - Должна быть проерка на внешний контроль, так же на шаблоне не должны давать провалиться в компанию
        $company = $this->updateCompany($company);

        if ($request->set_user == 1) {
            $this->getDirector($company, $this->entityAlias);
        }

        $this->setStatuses($company);

        // Обновление информации по клиенту:
        $data = $request->input();
        $data['description'] = $request->competitor_description;
        $res = $competitor->update($data);

        if (!$res) {
            abort(403, __('errors.update'));
        }

        logs('companies')->info("Обновлен агент. Id: [{$competitor->id}]");
        logs('companies')->info('============ КОНЕЦ ОБНОВЛЕНИЯ КОНКУРЕНТА ===============

        ');

        return redirect()->route('competitors.index');
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
        $competitor = Competitor::moderatorLimit($answer)
            ->find($id);

        if (empty($competitor)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod('destroy'), $competitor);

        $competitor->archive();
        return redirect()->route('competitors.index');
    }

    public function checkcompany(Request $request)
    {
        $company = Company::where('inn', $request->inn)->first();

        if (!isset($company)) {
            return 0;
        } else {
            return $company->name;
        };
    }
}
