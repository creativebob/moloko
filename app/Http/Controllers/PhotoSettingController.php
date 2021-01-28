<?php

namespace App\Http\Controllers;

use App\Http\Requests\System\PhotoSettingRequest;
use App\PhotoSetting;

class PhotoSettingController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * PhotoSettingController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'photo_settings';
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
        $this->authorize(getmethod(__FUNCTION__), PhotoSetting::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $photoSettings = PhotoSetting::with([
            'entity',
            'author',
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)

            // TODO - 28.01.21 - Пока вытаскиваем настройки для сущностей
            ->where('photo_settings_type', 'App\Entity')
            ->oldest('sort')
            ->paginate(30);
//        dd($photoSettings);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.settings.photo_settings.index', compact('photoSettings', 'pageInfo'));
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
        $this->authorize(getmethod(__FUNCTION__), PhotoSetting::class);

        $photoSetting = PhotoSetting::make();

        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.settings.photo_settings.create', compact('photoSetting', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PhotoSettingRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(PhotoSettingRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), PhotoSetting::class);

        $data = $request->input();

        if ($request->has('entity_id')) {
            // Вытаскиваем настройки
            $settings = PhotoSetting::whereNull('company_id')
                ->first();

            $data['photo_settings_id'] = $request->entity_id;
            $data['photo_settings_type'] = 'App\Entity';
        }
        $photoSetting = PhotoSetting::create($data);

        if ($photoSetting) {
            return redirect()->route('photo_settings.index');
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

        $photoSetting = PhotoSetting::moderatorLimit($answer)
            ->find($id);
//        dd($photoSetting);
        if (empty($photoSetting)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $photoSetting);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.settings.photo_settings.edit', compact('photoSetting', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PhotoSettingRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(PhotoSettingRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $photoSetting = PhotoSetting::moderatorLimit($answer)
            ->find($id);
        //        dd($photoSetting);
        if (empty($photoSetting)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $photoSetting);

        $data = $request->input();
        if ($request->has('entity_id')) {
            $data['photo_settings_id'] = $request->entity_id;
            $data['photo_settings_type'] = 'App\Entity';
        }
        $result = $photoSetting->update($data);

        if ($result) {
            return redirect()->route('photo_settings.index');
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

        $photoSetting = PhotoSetting::moderatorLimit($answer)
            ->find($id);
        if (empty($photoSetting)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $photoSetting);
        $res = $photoSetting->delete();

        if ($res) {
            return redirect()->route('photo_settings.index');
        } else {
            abort(403, __('errors.update'));
        }
    }
}
