<?php

namespace App\Http\Controllers;

use App\Http\Controllers\System\Traits\Locationable;
use App\Http\Controllers\System\Traits\Phonable;
use App\Http\Requests\System\OutletRequest;
use App\Outlet;

class OutletController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * OutletController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'outlets';
        $this->entityDependence = true;
    }

    use Phonable,
        Locationable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Outlet::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $outlets = Outlet::with([
            'company',
            'filial',
            'stock',
            'author',
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->filials($answer)
//            ->orderBy('moderation', 'desc')
            ->oldest('sort')
            ->paginate(30);
//        dd($outlets);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.outlets.index', compact('outlets', 'pageInfo'));
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
        $this->authorize(getmethod(__FUNCTION__), Outlet::class);

        $outlet = Outlet::make();

        return view('system.pages.outlets.create', compact('outlet'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OutletRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(OutletRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Outlet::class);

        $data = $request->validated();
        $outlet = Outlet::create($data);

        if ($outlet) {
            return redirect()->route('outlets.edit', $outlet->id);
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

        $outlet = Outlet::moderatorLimit($answer)
            ->find($id);
//        dd($outlet);

        if (empty($outlet)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $outlet);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.outlets.edit', compact('outlet', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OutletRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(OutletRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $outlet = Outlet::moderatorLimit($answer)
            ->find($id);
        //        dd($outlet);

        if (empty($outlet)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $outlet);

        $data = $request->validated();

        $location = $this->getLocation();
        $data['location_id'] = $location->id;

        $result = $outlet->update($data);

        $this->savePhones($outlet);

        $outlet->taxation_types()->sync($request->taxation_types);
        $outlet->catalogs_goods()->sync($request->catalogs_goods);
        $outlet->staff()->sync($request->staff);
        $outlet->settings()->sync($request->settings);
        $outlet->payments_methods()->sync($request->payments_methods);

        if ($result) {
            return redirect()->route('outlets.index');
        } else {
            abort(403, __('errors.update'));
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
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $outlet = Outlet::moderatorLimit($answer)
            ->find($id);
        //        dd($outlet);

        if (empty($outlet)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod('destroy'), $outlet);

        $res = $outlet->delete();

        if ($res) {
            return redirect()->route('outlets.index');
        } else {
            abort(403, __('errors.destroy'));
        }
    }
}
