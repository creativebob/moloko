<?php

namespace App\Http\Controllers\Traits;

use App\User;
use App\Lead;
use App\Phone;

use App\Events\onAddLeadEvent;
use Event;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Telegram;

// Специфические классы
use Carbon\Carbon;

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
		
        if (isset($request->delivery_date) || isset($request->delivery_time)) {
	        $date = Carbon::createFromFormat('d.m.Y H:i', $request->delivery_date . ' ' . $request->delivery_time);
	        $lead->delivered_at = $date;
        }
		

        // Работаем с ПОЛЬЗОВАТЕЛЕМ лида ================================================================

        // Проверяем, есть ли в базе телефонов пользователь с таким номером
        $user = check_user_by_phones($request->main_phone);
        if($user != null){

            // Если есть: записываем в лида ID найденного в системе пользователя
            $lead->user_id = $user->id;

        } else {

            // Если нет: создаем нового пользователя по номеру телефона
            // используя трейт экспресс создание пользователя
            $user = $this->createUserByPhone($request->main_phone);

            $user->location_id = create_location($request, $country_id = 1, $city_id = 1, $address = null);

            // Если к пользователю нужно добавить инфы, тут можно апнуть юзера: ----------------------------------

            // Компания и филиал ----------------------------------------------------------
            $user->company_id = $company_id;
            $user->filial_id = $filial_id;
            $user->save();

            $lead->user_id = $user->id;

            // dd($user);

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

    public function createLeadFromSite($request){

        // Готовим необходимые данные ======================================================================
        // Получаем сайт
        $site = getSite();
        $company = $site->company;

        // Если не пришел филиал, берем первый у компании
        $filial_id = $request->filial_id ?? $site->filials->first()->id;

        $name = $request->name;

        $first_name = $request->first_name;
        $second_name = $request->second_name;
        $name = $first_name . ' ' . $second_name;
        $nickname = $first_name . ' ' . $second_name;
        $company_name = $request->company_name;
        $description = $request->description;

        // ------------------------------------------------------------------------------

        // Если пришло имя компании, то укажем, что это компания
        if($company_name){
            $private_status = 1;
        } else {
            $private_status = 0;
        }

        $phone = cleanPhone($request->main_phone);

        // Содержится ли в куках данные корзины
        if(Cookie::get('cart') !== null){

            $count = 0; $badget = 0;

            $cart = json_decode(Cookie::get('cart'), true);
            $badget = $cart['sum'];
            $count = $cart['count'];            
        }


        // Работаем с ПОЛЬЗОВАТЕЛЕМ для лида ================================================================

        // Если пользователь АВТОРИЗОВАН
        $user = Auth::User();
        if($user){

            // Формируем имя записи в лида
            $name = $user->first_name ?? '' . ' ' . $second_name ?? '';
            $phone = $user->main_phone->phone;

        // Если пользователь НЕ авторизован
        } else {

            if(!isset($request->main_phone)){abort(403, 'Не указан номер телефона!');}

            // Получаем юзера если такой пользователь есть в базе по указанному номеру
            $user = check_user_by_phones($request->main_phone);


            // Если нет, то создадим нового
            if (empty($user)) {

                // Если нет: создаем нового пользователя по номеру телефона
                // используя трейт экспресс создание пользователя
                $user = $this->createUserByPhone($request->main_phone, null, $site->company);

                // sendSms('79041248598', 'Данные для входа: ' . $user->access_code);

                $user->location_id = create_location($request, $country_id = 1, $city_id = 1, $address = null);

                $user->first_name = $first_name;
                $user->second_name = $second_name;
                $user->nickname = $nickname;

                // Компания и филиал
                $user->company_id = $company->id;
                $user->filial_id = $filial_id;
                $user->save();

                $phone = $user->main_phone->phone;

                // Конец апдейта юзеара
            } else {

                $user->nickname = $name;
            }
        }

        // Конец работы с ПОЛЬЗОВАТЕЛЕМ для лида
       

        // Создание ЛИДА ======================================================================
        $lead = new Lead;
        $lead->company_id = $company->id;
        $lead->filial_id = $filial_id;
        $lead->user_id = $user->id;
        $lead->email = $request->email ?? '';
        $lead->name = $name;
        $lead->company_name = $company_name;
        $lead->private_status = $private_status;
        $lead->location_id = create_location($request, $country_id = 1, $city_id = 1, $address = null);

        $lead->description = $description;
        $lead->stage_id = $request->stage_id ?? 2; // Этап: "обращение"" по умолчанию
        $lead->badget = $badget ?? 0;
        $lead->lead_method_id = 2; // Способ обращения: "звонок"" по умолчанию
        $lead->draft = null;

        $lead->editor_id = 1;

        if($request->choice_tag){

            $choiceFromTag = getChoiceFromTag($request->choice_tag);
            $lead->choice_type = $choiceFromTag['type'];
            $lead->choice_id = $choiceFromTag['id'];
        } else {

            dd('Хм, нет цели обращения');
        }

        $lead->save();

        // Телефон
        $request->main_phone = $phone;
        $phones = add_phones($request, $lead);
        // $lead = update_location($request, $lead);

        // Формируем сообщение
        $message = "Заказ с сайта:\r\n";
        $message .= "Имя клиента: " . $lead->name . "\r\n";
        $message .= "Тел: " . decorPhone($phone) . "\r\n";
        $message .= "Кол-во товаров: " . $count . "\r\n";
        $message .= "Сумма заказа: " . num_format($lead->badget, 0) . ' руб.' . "\r\n";
        $message .= "Примечание: " . $description;

        $lead->notes()->create([
            'company_id' => $company->id,
            'body' => $message,
            'author_id' => 1,
        ]);

        $destinations = User::whereHas('staff', function ($query) {
            $query->whereHas('position', function ($query) {
                $query->whereHas('notifications', function ($query) {
                    $query->where('notification_id', 1);
                });
            });
        })
            ->whereNotNull('telegram')
            ->get(['telegram']);

        if (isset($destinations)) {

            // Отправляем на каждый telegram
            foreach ($destinations as $destination) {

                if (isset($destination->telegram)) {
                    $response = Telegram::sendMessage([
                        'chat_id' => $destination->telegram,
                        'text' => $message
                    ]);
                }
            }
        }

        return $lead;
    }



}