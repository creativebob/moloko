<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Project\Traits\Commonable;
use App\Http\Controllers\Traits\EstimateControllerTrait;
use App\Http\Controllers\Traits\LeadControllerTrait;
use App\Http\Controllers\Traits\UserControllerTrait;
use App\Lead;
use App\Models\Project\Estimate;
use App\Stock;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Telegram;
use Telegram\Bot\Exceptions\TelegramResponseException;

class FormController extends Controller
{

    use Commonable;
    use UserControllerTrait;
    use LeadControllerTrait;
    use EstimateControllerTrait;

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

        if (isset($request->main_phone)) {
            // Готовим необходимые данные ======================================================================
            // Получаем сайт
            $site = $this->site;
            $company = $site->company;

            // Если не пришел филиал, берем первый у компании
            $filial_id = $this->site->filial->id;

            $lead_name = isset($request->first_name) ? $request->first_name : 'Клиент не указал имя';

            // ------------------------------------------------------------------------------

            $phone_number = cleanPhone($request->main_phone);


            // Работаем с ПОЛЬЗОВАТЕЛЕМ для лида ================================================================

            // Если пользователь АВТОРИЗОВАН
            $user = \Auth::user();

            if ($user){

                // Формируем имя записи в лида
                if(empty($lead_name)){
                    $lead_name = $user->first_name . ' ' . $user->second_name;
                }

                $phone = $user->main_phone->phone;

                // Если пользователь НЕ авторизован
            } else {

                if(!isset($request->main_phone)){abort(403, 'Не указан номер телефона!');}

                // Получаем юзера если такой пользователь есть в базе по указанному номеру
                $user = checkPhoneUserForSite($request->main_phone, $site);

                // Если нет, то создадим нового
                if (empty($user)) {

                    // Если нет: создаем нового пользователя по номеру телефона
                    // используя трейт экспресс создание пользователя
                    $user = $this->createUserByPhone($request->main_phone, null, $site->company);

                    // sendSms('79041248598', 'Данные для входа: ' . $user->access_code);

                    $user->location_id = create_location($request, $country_id = 1, $city_id = 1);

                    $user->first_name = $lead_name;
                    $user->site_id = $site->id;

                    // Компания и филиал
                    $user->company_id = $company->id;
                    $user->filial_id = $filial_id;
                    $user->save();

                    $phone = $user->main_phone->phone;

                    // Конец апдейта юзеара
                };
            }

            // Конец работы с ПОЛЬЗОВАТЕЛЕМ для лида


            // Создание ЛИДА ======================================================================
            $lead = new Lead;
            $lead->company_id = $company->id;
            $lead->filial_id = $filial_id;
            $lead->user_id = $user->id;
            $lead->email = $request->email ?? '';
            $lead->name = $lead_name;

            $lead->location_id = create_location($request, $country_id = 1, $site->filial->location->city_id);

            $lead->stage_id = $request->stage_id ?? 2; // Этап: "обращение"" по умолчанию
            $lead->badget = $badget ?? 0;
            $lead->lead_method_id = 2; // Способ обращения: "звонок"" по умолчанию
            $lead->draft = null;

            $lead->author_id = 1;
//        $lead->editor_id = 1;

            // if($request->choice_tag){
            //     $choiceFromTag = getChoiceFromTag($request->choice_tag);
            //     $lead->choice_type = $choiceFromTag['type'];
            //     $lead->choice_id = $choiceFromTag['id'];
            // } else {
            //     dd('Хм, нет цели обращения');
            // }

            $lead->save();

            logs('leads_from_project')->info("============== Создан лид с сайта с id :[{$lead->id}], сайт:[{$this->site->domain->domain}]  ===============================");
            // ------------------------------------------- Конец создаем лида ---------------------------------------------

            // Телефон
//        $request->main_phone = $phone;
            $phones = add_phones($request, $lead);
            // $lead = update_location($request, $lead);

            // TODO - 15.11.19 - Склад должен браться из настроек, пока берем первый по филиалу
            $stock_id = Stock::where('filial_id', $lead->filial_id)->value('id');

            // Находим или создаем заказ для лида
            $estimate = Estimate::create([
                'lead_id' => $lead->id,
                'filial_id' => $lead->filial_id,
                'company_id' => $lead->company_id,
                'stock_id' => $stock_id,
                'date' => now()->format('Y-m-d'),
                'number' => $lead->case_number,
                'author_id' => $lead->author_id,

            ]);

            logs('leads_from_project')->info("Создана смета с id: [{$estimate->id}]");

            // TODO - 15.11.19 - Скидка должна браться из ценовой политики
            $discount_percent = 0;

            // Пока статично вписываем скидку и размер суммы со скидкой
            $total = $lead->badget - ($lead->badget * $discount_percent / 100);
            $discount = $lead->badget * $discount_percent / 100;

            $estimate->amount = $lead->badget;
            $estimate->total = $total;
            $estimate->discount = $discount;
            $estimate->discount_percent = $discount_percent;
            $estimate->save();

            // TODO - 23.10.19 - Сделать адекватное сохранение в корзине
            $lead->badget = $total;
            $lead->save();

            // Формируем сообщение
            $message = "Заказ с сайта: №" . $lead->id . "\r\n";

            if ($site->domains->count() > 1) {
                $message .= "Город: " . $site->filial->location->city->name . "\r\n";
            }

            $message .= "Имя клиента: " . $lead->name . "\r\n";
            $message .= "Тел: " . decorPhone($phone) . "\r\n";



            $message .= "\r\n";

            $message .= $request->message;

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
                ->get([
                    'telegram'
                ]);

            if (isset($destinations)) {

                // Отправляем на каждый telegram
                foreach ($destinations as $destination) {

                    if (isset($destination->telegram)) {

                        try {
                            $response = Telegram::sendMessage([
                                'chat_id' => $destination->telegram,
                                'text' => $message
                            ]);
                        } catch (TelegramResponseException $exception) {
                            // Юзера нет в боте, не отправляем ему мессагу
                        }
                    }
                }
            }

            logs('leads_from_project')->info("============== Создан лид с сайта ===============================
            
            ");

            $site = $this->site;
            $page = $site->pages_public->firstWhere('alias', 'success');

            return redirect()->route('project.success');
//            return view($site->alias.'.pages.success.index', compact('site', 'page'));
        } else {
            return redirect()->route('project.start');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
