<?php

namespace App\Http\Controllers;

use App\Company;
use App\Http\Controllers\System\Traits\Companable;
use App\Http\Controllers\System\Traits\Directorable;
use App\Http\Controllers\System\Traits\Locationable;
use App\Http\Controllers\System\Traits\Phonable;
use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\CompanyRequest;
use App\Http\Requests\System\VendorRequest;
use App\Supplier;
use App\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{

    protected $entityAlias;
    protected $entityDependence;

    /**
     * VendorController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'vendors';
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
        if (($filter_url != null) && ($request->filter != 'active')) {
            return Redirect($filter_url);
        };

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Vendor::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------
        $vendors = Vendor::with([
            'supplier' => function ($q) {
                $q->with([
                    'company' => function ($q) {
                        $q->with([
                            'main_phones',
                            'location',
                            'sector',
                            'legal_form'
                        ]);
                    }
                ]);
            },
            'author',
        ])
            ->companiesLimit($answer)
            ->where('archive', false)
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->booklistFilter($request)
//            ->orderBy('moderation', 'desc')
            ->oldest('sort')
            ->paginate(30);
//        dd($vendors);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
//            'vendor_country', // Страна производителя
//            'sector',               // Направление деятельности
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.erp.vendors.index', compact('vendors', 'pageInfo', 'filter'));
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
        $this->authorize(getmethod(__FUNCTION__), Vendor::class);
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        $vendor = Vendor::make();
        $company = Company::make();

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.erp.vendors.create', compact('vendor', 'company', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CompanyRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Vendor::class);
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        logs('companies')->info('============ НАЧАЛО СОЗДАНИЯ ПРОДАВЦА ===============');

        $company = $this->storeCompany();

        if ($request->set_user == 1) {
//            $this->getDirector($company);
        }

        // Создаем связь
        $supplier = Supplier::create([
            'supplier_id' => $company->id
        ]);

        $data = $request->input();
        $data['supplier_id'] = $supplier->id;
        $data['description'] = $request->vendor_description;

        $vendor = Vendor::create($data);

        logs('companies')->info("Создан продавец. Id: [{$vendor->id}]");
        logs('companies')->info('============ КОНЕЦ СОЗДАНИЯ ПРОДАВЦА ===============
        
        ');

        return redirect()->route('vendors.index');
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
        $vendor = Vendor::with([
            'supplier' => function ($q) {
                $q->with([
                    'company' => function ($q) {
                        $q->with([
                            'location.city',
                            'schedules.worktimes',
                            'sector',
                            'processes_types',
                            'manufacturers'
                        ]);
                    }
                ]);
            }
        ])
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($vendor);

        if (empty($vendor)) {
            abort(403, __('errors.not_found'));
        }

        $company = $vendor->supplier->company;
//        dd($company);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $vendor);
        $this->authorize(getmethod(__FUNCTION__), $company);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.erp.vendors.edit', compact('vendor', 'company', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param VendorRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(VendorRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $vendor = Vendor::with([
            'supplier' => function ($q) {
                $q->with([
                    'company' => function ($q) {
                        $q->with([
                            'location.city',
                            'schedules.worktimes',
                            'sector',
                            'processes_types',
                            'manufacturers'
                        ]);
                    }
                ]);
            }
        ])
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($vendor);

        if (empty($vendor)) {
            abort(403, __('errors.not_found'));
        }

        $company = $vendor->supplier->company;
//        dd($company);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $vendor);
        $this->authorize(getmethod(__FUNCTION__), $company);

        logs('companies')->info('============ НАЧАЛО ОБНОВЛЕНИЯ ПРОДАВЦА ===============');

        // TODO - 15.09.20 - Должна быть проерка на внешний контроль, так же на шаблоне не должны давать провалиться в компанию
        $company = $this->updateCompany($company);

        $data = $request->input();
        $data['description'] = $request->vendor_description;
        $res = $vendor->update($data);

        if (!$res) {
            abort(403, __('errors.update'));
        }

        logs('companies')->info("Обновлен продавец. Id: [{$vendor->id}]");
        logs('companies')->info('============ КОНЕЦ ОБНОВЛЕНИЯ ПРОДАВЦА ===============
        
        ');

        return redirect()->route('vendors.index');
    }

    /**
     * Архивирование указанного ресурса
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function archive($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('delete'));

        // ГЛАВНЫЙ ЗАПРОС:
        $vendor = Vendor::moderatorLimit($answer)
            ->find($id);
//        dd($vendor);

        if (empty($vendor)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod('destroy'), $vendor);

        $vendor->archive = true;
        $vendor->editor_id = hideGod(auth()->user());
        $vendor->save();

        if (!$vendor) {
            abort(403, __('errors.archive'));
        }

        return redirect()->route('vendors.index');
    }
}
