<?php

namespace App\Http\Controllers;

use App\Http\Controllers\System\Traits\Companable;
use App\Http\Controllers\System\Traits\Directorable;
use App\Http\Controllers\System\Traits\Locationable;
use App\Http\Controllers\System\Traits\Phonable;
use App\Http\Controllers\Traits\Photable;
use App\Company;
use Illuminate\Http\Request;
use App\Http\Requests\System\CompanyRequest;

class CompanyController extends Controller
{

    protected $entityAlias;
    protected $entityDependence;

    /**
     * CompanyController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'companies';
        $this->entityDependence = false;
    }

    use Locationable;
    use Phonable;
    use Photable;
    use Companable;
    use Directorable;

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
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ------------------------------------------------------------------------------------------------------------
        $companies = Company::with([
            'author',
            'location.city',
            'sector',
            'we_suppliers',
            'we_manufacturers',
            'we_clients',
            'main_phones',
            'legal_form',
            'director'
        ])
        ->companiesLimit($answer)
        ->moderatorLimit($answer)
        ->authors($answer)
//        ->systemItem($answer)
//        ->filter($request, 'city_id', 'location')
//        ->filter($request, 'sector_id')
//        ->booklistFilter($request)
        ->oldest('sort')
        ->paginate(30);
//        dd($companies);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
            'author',               // Автор записи
            'sector',               // Направление деятельности
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.companies.index', compact('companies', 'pageInfo', 'filter'));
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
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        $company = Company::make();

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.companies.create', compact('company', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CompanyRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CompanyRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        logs('companies')->info('============ НАЧАЛО СОЗДАНИЯ КОМПАНИИ ===============');

        $company = $this->storeCompany();

        if ($request->set_user == 1) {
            $this->getDirector($company);
        }

        $this->setStatuses($company);

        logs('companies')->info('============ КОНЕЦ СОЗДАНИЯ КОМПАНИИ ===============');

        return redirect()->route('companies.index');
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->find($id);

        // Подключение политики
        $this->authorize('view', $company);

        return view('system.pages.companies.show', compact('company'));
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

        $company = Company::with([
            'main_phones',
            'extra_phones',
            'location.city',
            'director.user.main_phone',
            'photo',
            'white',
            'black',
            'color',
            'settings',
            'bank_accounts.bank',
            'schedules.worktimes',

            'client',
            'supplier',
            'manufacturer'
        ])
        ->moderatorLimit($answer)
        ->authors($answer)
//        ->systemItem($answer)
        ->find($id);
//        dd($company);

        if (empty($company)) {
            abort(403,__('errors.not_found'));
        }

        $this->authorize(getmethod(__FUNCTION__), $company);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.companies.edit', compact('company', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CompanyRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(CompanyRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::with([
            'location',
            'schedules.worktimes',
            'white',
            'black',
            'color'
        ])
        ->moderatorLimit($answer)
        ->authors($answer)
//        ->systemItem($answer)
        ->find($id);

        if (empty($company)) {
            abort(403,__('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $company);

        // TODO - 15.09.20 - Должна быть проерка на внешний контроль, так же на шаблоне не должны давать провалиться в компанию
        $company = $this->updateCompany($company);

        if ($request->set_user == 1) {
            $this->getDirector($company);
        }

        return redirect()->route('companies.index');
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

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->find($id);

        if (empty($company)) {
            abort(403,__('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $company);

        $res = $company->delete();

        if (!$res) {
            abort(403,__('errors.destroy'));
        }

        return redirect()->route('companies.index');
    }

    // ------------------------------------------- Ajax ---------------------------------------------

    public function checkcompany(Request $request)
    {
        $company = Company::where('inn', $request->inn)->first();

        if(!isset($company)) {
            return 0;
        } else {
            return $company->name;
        }
    }

}

