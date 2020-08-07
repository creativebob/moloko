<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

// Подрубаем трейт перезаписи сессии
use App\Http\Controllers\Traits\RewriteSessionUserSettings;

class SettingController extends Controller
{

    // Подключаем трейт перезаписи списк отделов (филиалов) в сессии пользователя
    use RewriteSessionUserSettings;

    /**
     * Отображение списка ресурсов.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Показать форму для создания нового ресурса.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Сохранение созданного ресурса в хранилище.
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
        $setting = new Setting;
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
     * Отображение указанного ресурса.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Setting $setting)
    {
        //
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
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
            $setting = new Setting;
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
