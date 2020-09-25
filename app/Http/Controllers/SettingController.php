<?php

namespace App\Http\Controllers;

use App\CompaniesSetting;
use Illuminate\Http\Request;

// Подрубаем трейт перезаписи сессии
use App\Http\Controllers\Traits\RewriteSessionUserSettings;

class SettingController extends Controller
{

    // Подключаем трейт перезаписи списк отделов (филиалов) в сессии пользователя
    use RewriteSessionUserSettings;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Пишем в базу
        $setting = new CompaniesSetting;
        $setting->company_id = $company_id;
        $setting->author_id = $user_id;

        $setting->user_id = $user_id;
        $setting->key = $request->key;
        $setting->value = $request->value;

        $setting->save();

        if ($setting) {

            $result = [
                'error_status' => 0,
            ];
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи настройки!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CompaniesSetting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(CompaniesSetting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CompaniesSetting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(CompaniesSetting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CompaniesSetting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompaniesSetting $setting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CompaniesSetting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompaniesSetting $setting)
    {
        //
    }

    public function ajax_set_setting(Request $request)
    {
        // $key = 'sidebar';
        // $value = '';

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        $setting = $user->settings->where('key', $request->key)->first();

        if (isset($setting)) {

                $setting->value = $request->value;
                $setting->save();

        } else {

            // Пишем в базу
            $setting = new CompaniesSetting;
            $setting->company_id = $company_id;
            $setting->author_id = $user_id;

            $setting->user_id = $user_id;
            $setting->key = $request->key;
            $setting->value = $request->value;

            $setting->save();

        }

        if ($setting) {

            // Перезаписываем сессию: меняем список филиалов и отделов на новый
            $this->RSUserSettings($request->key, $request->value);

            $result = [
                'error_status' => 0,
            ];
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи настройки!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
