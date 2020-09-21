<?php

namespace App\Http\Controllers;

use App\Http\Controllers\System\Traits\Companable;
use App\Http\Controllers\System\Traits\Directorable;
use App\Http\Controllers\System\Traits\Locationable;
use App\Http\Controllers\System\Traits\Phonable;
use App\Http\Controllers\Traits\Photable;
use App\Manufacturer;
use App\Supplier;
use App\Company;
use App\Vendor;
use Illuminate\Http\Request;

class SupplierController extends Controller
{

    protected $entityAlias;
    protected $entityDependence;

    /**
     * SupplierController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'suppliers';
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
        $this->authorize(getmethod(__FUNCTION__), Supplier::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------
        $suppliers = Supplier::with([
            'company' => function ($q) {
                $q->with([
                    'main_phones',
                    'location',
                    'sector',
                    'legal_form'
                ]);
            },
            'author',
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->where('archive', false)
            ->filter($request, 'city_id', 'location')
            ->filter($request, 'sector_id', 'company')
            ->booklistFilter($request)
//            ->orderBy('moderation', 'desc')
            ->oldest('sort')
            ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
            'city',                 // Город
            'sector',               // Направление деятельности
            'booklist'              // Списки пользователя
        ]);
        // dd($filter);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.erp.suppliers.index', compact('suppliers', 'pageInfo', 'filter'));
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
        $this->authorize(getmethod(__FUNCTION__), Supplier::class);
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        $supplier = Supplier::make();
        $company = Company::make();

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.erp.suppliers.create', compact('supplier', 'company', 'pageInfo'));
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
        $this->authorize(getmethod(__FUNCTION__), Supplier::class);
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        logs('companies')->info('============ НАЧАЛО СОЗДАНИЯ ПОСТАВЩИКА ===============');

        $company = $this->storeCompany();

        if ($request->set_user == 1) {
            $this->getDirector($company);
        }

        $data = $request->input();
        $data['supplier_id'] = $company->id;
        $data['description'] = $request->supplier_description;

        $supplier = Supplier::create($data);

        $this->setStatuses($company);

        $supplier->manufacturers()->sync($request->manufacturers);

        logs('companies')->info("Создан поставщик. Id: [{$supplier->id}]");
        logs('companies')->info('============ КОНЕЦ СОЗДАНИЯ ПОСТАВЩИКА ===============
        
        ');

        return redirect()->route('suppliers.index');
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
        $supplier = Supplier::with([
            'company' => function ($q) {
                $q->with([
                    'location.city',
                    'schedules.worktimes',
                    'sector',
                    'processes_types',
                    'manufacturers'
                ]);
            },
            'vendor'
        ])
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($supplier);

        if (empty($supplier)) {
            abort(403, __('errors.not_found'));
        }

        $company = $supplier->company;
//        dd($company);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $supplier);
        $this->authorize(getmethod(__FUNCTION__), $company);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.erp.suppliers.edit', compact('supplier', 'company', 'pageInfo'));
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
        $supplier = Supplier::with([
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
//        dd($supplier);

        if (empty($supplier)) {
            abort(403, __('errors.not_found'));
        }

        $company = $supplier->company;

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $supplier);
        $this->authorize(getmethod(__FUNCTION__), $company);

        logs('companies')->info('============ НАЧАЛО ОБНОВЛЕНИЯ ПОСТАВЩИКА ===============');

        // TODO - 15.09.20 - Должна быть проерка на внешний контроль, так же на шаблоне не должны давать провалиться в компанию
        $company = $this->updateCompany($company);

        if ($request->set_user == 1) {
            $this->getDirector($company);
        }

        $data = $request->input();
        $data['description'] = $request->supplier_description;
        $res = $supplier->update($data);

        if (!$res) {
            abort(403, __('errors.update'));
        }

        logs('companies')->info("Обновлен поставщик. Id: [{$supplier->id}]");
        logs('companies')->info('============ КОНЕЦ ОБНОВЛЕНИЯ ПОСТАВЩИКА ===============
        
        ');

        return redirect()->route('suppliers.index');
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
        $supplier = Supplier::moderatorLimit($answer)
            ->find($id);
//        dd($supplier);

        if (empty($supplier)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod('destroy'), $supplier);

        $supplier->archive = true;
        $supplier->editor_id = hideGod(auth()->user());
        $supplier->save();

        if (!$supplier) {
            abort(403, __('errors.archive'));
        }

        return redirect()->route('suppliers.index');
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
