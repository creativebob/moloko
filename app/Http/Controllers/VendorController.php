<?php

namespace App\Http\Controllers;

use App\Company;
use App\Http\Controllers\Traits\CompanyControllerTrait;
use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\CompanyRequest;
use App\Http\Requests\VendorRequest;
use App\Supplier;
use App\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{

    /**
     * VendorController constructor.
     * @param Vendor $vendor
     */
    public function __construct(Vendor $vendor)
    {
        $this->middleware('auth');
        $this->vendor = $vendor;
        $this->class = Vendor::class;
        $this->model = 'App\Vendor';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    // Подключаем трейт записи и обновления компании
    use CompanyControllerTrait;
    use Photable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $filter_url = autoFilter($request, $this->entity_alias);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Vendor::class);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------


        // $vendors = vendor::with('author', 'company')
        // ->companiesLimit($answer)
        // ->where('company_id', '!=', null)


        $vendors = Vendor::with([
            'supplier' => function ($q) {
                $q->with([
                    'company' => function ($q) {
                        $q->with([
                           'location.country',
                            'sector',
                            'legal_form'
                        ]);
                    }
                ]);
            },
            'author',
        ])
            ->companiesLimit($answer)
            ->where('archive', 0)
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->whereHas('supplier', function ($q) use ($request) {
                $q->whereHas('company', function($q) use ($request){
                    $q->filter($request, 'country_id', 'location');
                });
            })
            ->booklistFilter($request)
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc')
            ->paginate(30);
//        dd($vendors);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
//            'vendor_country', // Страна производителя
//            'sector',               // Направление деятельности
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('vendors.index', compact('vendors', 'page_info', 'filter', 'user'));
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

        $page_info = pageInfo($this->entity_alias);

        return view('vendors.create', compact('company', 'vendor', 'page_info'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CompanyRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CompanyRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Vendor::class);
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);


        // Отдаем работу по созданию новой компании трейту
        $company = $this->createCompany($request);

        $company->currencies()->sync($request->currencies);

        // Создаем связь
        $supplier = Supplier::create([
            'company_id' => auth()->user()->company->id,
            'supplier_id' => $company->id
        ]);

        $vendor = Vendor::create([
            'company_id' => auth()->user()->company->id,
            'supplier_id' => $supplier->id,
            'description' => $request->description,
            'status' => $request->status
        ]);

        return redirect()->route('vendors.index');
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
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $vendor = Vendor::moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $vendor);

        // ПОЛУЧАЕМ КОМПАНИЮ ------------------------------------------------------------------------------------------------
        $company_id = $vendor->company->id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_company = operator_right('company', false, getmethod(__FUNCTION__));

        $company = Company::with('location.city', 'schedules.worktimes', 'sector', 'processes_types')
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->findOrFail($company_id);

        $this->authorize(getmethod(__FUNCTION__), $company);



        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('vendors.edit', compact('vendor', 'page_info'));
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
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $vendor = Vendor::moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->findOrFail($id);
//        dd($vendor);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $vendor);

        $company_id = $vendor->supplier->company->id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_company = operator_right('companies', false, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::with('location', 'schedules.worktimes')->moderatorLimit($answer_company)->findOrFail($company_id);
//        dd($company);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $company);

        // Отдаем работу по редактировнию компании трейту
        $this->updateCompany($request, $vendor->supplier->company, $vendor);

        $data = $request->input();
        $vendor->update($data);

        $vendor->supplier->company->currencies()->sync($request->currencies);

        return redirect()->route('vendors.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $vendor = Vendor::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $vendor);

        if ($vendor) {

            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);

            $vendor->editor_id = $user_id;

            // Архивируем связь
            $vendor->archive = 1;

            $vendor->save();

            // Удаляем наглухо: мягко
            // $vendor = vendor::destroy($id);

            // Удаляем компанию с обновлением
            if($vendor) {
                return redirect()->route('vendors.index');

            } else {
                abort(403, 'Ошибка при удалении поставщика');
            }

        } else {
            abort(403, 'Поставщик не найдена');
        }
    }
}
