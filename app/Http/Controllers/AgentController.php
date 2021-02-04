<?php

namespace App\Http\Controllers;

use App\Http\Controllers\System\Traits\Companable;
use App\Http\Controllers\System\Traits\Directorable;
use App\Http\Controllers\System\Traits\Locationable;
use App\Http\Controllers\System\Traits\Phonable;
use App\Http\Controllers\Traits\Photable;
use App\Agent;
use App\Company;
use App\Outlet;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * AgentController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'agents';
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
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $filter_url = autoFilter($request, $this->entityAlias);
        if (($filter_url != null) && ($request->filter != 'active')) {
            return Redirect($filter_url);
        };

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Agent::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------
        $agents = Agent::with('author', 'company.location.city', 'company.sector', 'company.legal_form')
            ->companiesLimit($answer)
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            // ->whereHas('company', function ($q) use ($request) {
            //     $q->filter($request, 'country_id', 'location');
            // })
            // ->booklistFilter($request)
//            ->orderBy('moderation', 'desc')
            ->oldest('sort')
            ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        // $filter = setFilter($this->entityAlias, $request, [
        //     'agent_country',        // Страна производителя
        //     'sector',               // Направление деятельности
        //     'booklist'              // Списки пользователя
        // ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.sales.agents.index', compact('agents', 'pageInfo'));
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
        $this->authorize(getmethod(__FUNCTION__), Agent::class);
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        $agent = Agent::make();
        $company = Company::make();

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.sales.agents.create', compact('agent', 'company', 'pageInfo'));
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
        $this->authorize(getmethod(__FUNCTION__), Agent::class);
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        logs('companies')->info('============ НАЧАЛО СОЗДАНИЯ АГЕНТА ===============');

        $company = $this->storeCompany();

        if ($request->set_user == 1) {
            $this->getDirector($company, $this->entityAlias);

            // Создаем торговую точку
            $outlet = new Outlet;

            $outlet->name = 'Первая торговая точка';
            $outlet->filial_id = $company->filials->first()->id;
            $outlet->company_id = $company->id;
            $outlet->author_id = 1;

            $outlet->saveQuietly();
        }

        $data = $request->input();
        $data['agent_id'] = $company->id;
        $data['description'] = $request->agent_description;

        $agent = Agent::create($data);

        $this->setStatuses($company);

        $agent->schemes()->sync($request->schemes);

        logs('companies')->info("Создан агент. Id: [{$agent->id}]");
        logs('companies')->info('============ КОНЕЦ СОЗДАНИЯ АГЕНТА ===============

        ');

        return redirect()->route('agents.index');
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
        $agent = Agent::with([
            'company' => function ($q) {
                $q->with([
                    'location.city',
                    'schedules.worktimes',
                    'sector',
                    'processes_types'
                ]);
            },
            'goodsSchemes.catalog',
            'servicesSchemes.catalog'
        ])
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($agent);

        if (empty($agent)) {
            abort(403, __('errors.not_found'));
        }

        $company = $agent->company;
//        dd($company);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $agent);
        $this->authorize(getmethod(__FUNCTION__), $company);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.sales.agents.edit', compact('agent', 'company', 'pageInfo'));
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
        $agent = Agent::with([
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
//        dd($agent);

        if (empty($agent)) {
            abort(403, __('errors.not_found'));
        }

        $company = $agent->company;

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $agent);
        $this->authorize(getmethod(__FUNCTION__), $company);

        logs('companies')->info('============ НАЧАЛО ОБНОВЛЕНИЯ АГЕНТА ===============');

        // TODO - 15.09.20 - Должна быть проерка на внешний контроль, так же на шаблоне не должны давать провалиться в компанию
        $company = $this->updateCompany($company);

        if ($request->set_user == 1) {
            $this->getDirector($company, $this->entityAlias);
        }

        $this->setStatuses($company);

        // Обновление информации по клиенту:
        $data = $request->input();
        $data['description'] = $request->agent_description;
        $res = $agent->update($data);

        if (!$res) {
            abort(403, __('errors.update'));
        }

        $agent->schemes()->sync($request->schemes);

        logs('companies')->info("Обновлен агент. Id: [{$agent->id}]");
        logs('companies')->info('============ КОНЕЦ ОБНОВЛЕНИЯ АГЕНТА ===============

        ');

        return redirect()->route('agents.index');
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
        $agent = Agent::moderatorLimit($answer)
            ->find($id);

        if (empty($agent)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod('destroy'), $agent);

        $agent->archive();
        return redirect()->route('agents.index');
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

    /**
     * Получаем агентов с схемами, подключенными к каталогам товаров и услуг
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAgentsByCatalogsIds(Request $request)
    {
//        $answer = operator_right('agents', false, 'index');

        $catalogGoodsId = $request->catalog_goods_id;
        $catalogServicesId = $request->catalog_services_id;

        $agents = Agent::with([
            // TODO - 13.12.20 - Неправильное отношение, правильное agent
            'company'
        ])
//            ->moderatorLimit($answer)
//            ->companiesLimit($answer)
//            ->authors($answer)
//            ->systemItem($answer)
            ->whereHas('schemes', function ($q) use ($catalogGoodsId, $catalogServicesId) {
                $q->where(function ($q) use ($catalogGoodsId, $catalogServicesId) {
                    $q->when($catalogGoodsId, function ($q) use ($catalogGoodsId) {
                        $q->where([
                            'catalog_type' => 'App\CatalogsGoods',
                            'catalog_id' => $catalogGoodsId
                        ]);
                    })
                        ->when($catalogServicesId, function ($q) use ($catalogServicesId) {
                        $q->orWhere([
                            'catalog_type' => 'App\CatalogsService',
                            'catalog_id' => $catalogServicesId
                        ]);
                    });
                });
            })
            ->get();

        return response()->json($agents);
    }

}
