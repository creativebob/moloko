<?php

namespace App\Http\Controllers\Traits;

use App\User;
use App\Lead;
use App\Phone;

use App\Events\onAddLeadEvent;
use Event;

use Illuminate\Support\Facades\DB;

trait LeadControllerTrait
{

	public function createLead($request){

        // Подготовка: -------------------------------------------------------------------------------------

        // Получаем данные для авторизованного пользователя
        $user_auth = $request->user();

        // Скрываем бога
        $user_auth_id = hideGod($user_auth);

        $company_id = $user_auth->company_id;
        $filial_id = $user_auth->filial_id;

        // СОЗДАЕМ ЛИДА ----------------------------------------------------------------------------

        $lead = new Lead;

        // Добавляем локацию
        $lead->location_id = create_location($request);

        $lead->company_id = $company_id;
        $lead->filial_id = $filial_id;
        $lead->name = NULL;
        $lead->company_name = NULL;


        $lead->draft = 1;
        $lead->author_id = $user_auth->id;
        $lead->manager_id = $user_auth->id;
        $lead->stage_id = 2;

        // Если приходит тип обращения - пишем его!
        // На валидации не пропускает к записи ничего кроме значений 1, 2 и 3
        if(isset($request->lead_type)){
            $lead_type = $request->lead_type;
        } else {
            $lead_type = 1;
        };

        $lead->lead_type_id = $lead_type;

        $lead->lead_method_id = 1;
        $lead->display = 1;
        $lead->save();

        $lead_number = getLeadNumbers($user_auth, $lead);
        $lead->case_number = $lead_number['case'];
        $lead->serial_number = $lead_number['serial'];
        $lead->save();

        if($lead) {

            return $lead;

        } else {

            abort(403, 'Ошибка при создании лида!');
        }

        return $lead;
    }

	public function updateLead($request, $lead, $answer = null){

        // Подготовка: -------------------------------------------------------------------------------------

        // Получаем данные для авторизованного пользователя
        $user_auth = $request->user();

        // Скрываем бога
        $user_auth_id = hideGod($user_auth);

        $company_id = $user_auth->company_id;
        $filial_id = $user_auth->filial_id;


        // ОБНОВЛЯЕМ ЛИДА ----------------------------------------------------------------------------------
        // Обновляем локацию
        $lead = update_location($request, $lead);

        $lead->filial_id = $filial_id;
        $lead->email = $request->email;

        $lead->name = $request->name;
        $lead->company_name = $request->company_name;
        // $lead->private_status = $request->private_status;

        $lead->stage_id = $request->stage_id ?? 2; // Этап: "обращение"" по умолчанию
        $lead->badget = $request->badget;
        $lead->lead_method_id = $request->lead_method ?? 1; // Способ обращения: "звонок"" по умолчанию
        $lead->draft = NULL;

        $lead->editor_id = $user_auth->id;

        $choiceFromTag = getChoiceFromTag($request->choice_tag);
        $lead->choice_type = $choiceFromTag['type'];
        $lead->choice_id = $choiceFromTag['id'];


        // Работаем с ПОЛЬЗОВАТЕЛЕМ лида ================================================================

        // Проверяем, есть ли в базе телефонов пользователь с таким номером
        $user_for_lead = check_user_by_phones($request->main_phone);
        if($user_for_lead != null){


            // Если есть: записываем в лида ID найденного в системе пользователя
            $lead->user_id = $user_for_lead->id;

        } else {

            // Если нет: создаем нового пользователя по номеру телефона
            // используя трейт экспресс создание пользователя
            $user_for_lead = $this->createUserByPhone($request->main_phone);

            // Обработка входящих данных ------------------------------------------
            $mass_names = getNameUser($request->name);

            $user_for_lead->first_name = $mass_names['first_name'] ?? $request->name ?? 'Укажите фамилию';
            $user_for_lead->second_name = $mass_names['second_name'] ?? null;
            $user_for_lead->patronymic = $mass_names['patronymic'] ?? null;
            $user_for_lead->sex = $mass_names['gender'] ?? 1;

            $user_for_lead->location_id = create_location($request, $country_id = 1, $city_id = 1, $address = null);

            // Если к пользователю нужно добавить инфы, тут можно апнуть юзера: ----------------------------------

            $user_for_lead->nickname = $request->name;

            // Компания и филиал ----------------------------------------------------------
            $user_for_lead->company_id = $company_id;
            $user_for_lead->filial_id = $filial_id;
            $user_for_lead->save();

            // dd($user_for_lead);

            // Конец апдейта юзеара -------------------------------------------------

        };

        // Конец работы с ПОЛЬЗОВАТЕЛЕМ лида ==============================================================

        // Телефон
        $phones = add_phones($request, $lead);

        // if(($request->extra_phone != NULL)&&($request->extra_phone != "")){
        //     $lead->extra_phone = cleanPhone($request->extra_phone);
        // } else {$lead->extra_phone = NULL;};

        // $lead->telegram_id = $request->telegram_id;
        // $lead->orgform_status = $request->orgform_status;
        // $lead->user_inn = $request->inn;

        // Модерируем (Временно)
        if($answer['automoderate']){$lead->moderation = null;};

        $lead->save();

        // Event::fire(new onAddLeadEvent($lead, $user_auth)); Метод fire упал после апа на Laravel 5.8

        if ($lead) {

        } else {
            abort(403, 'Ошибка при обновлении лида!');
        }

        $backroute = $request->backroute;

        // if(isset($backroute)){
        //         // return redirect()->back();
        //     return redirect($backroute);
        // };

        return $lead;
    }


    // public function createLeadByPhone($phone){

    //     $user_number = User::all()->last()->id;
    //     $user_number = $user_number + 1;

    //     $user = new User;
    //     $user->login = 'user_'.$user_number;
    //     $user->password = bcrypt(str_random(12));

    //     $user->access_block = 1;
    //     $user->user_type = 0;
    //     $user->save();

    //     if($user) {

    //         // Если номера нет, пишем или ищем новый и создаем связь
    //         $new_phone = Phone::firstOrCreate(
    //             ['phone' => cleanPhone($phone)
    //         ], [
    //             'crop' => substr(cleanPhone($phone), -4),
    //         ]);

    //         $user->phones()->attach($new_phone->id, ['main' => 1]);
    //         return $user;

    //     } else {
    //         abort(403, 'Ошибка при создании пользователя по номеру телефона!');
    //     }
    // }



}