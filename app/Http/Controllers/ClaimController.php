<?php

namespace App\Http\Controllers;

// Модели
use App\Claim;
use App\Lead;
use App\User;
use App\Phone;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\ClaimRequest;

// Политики
use App\Policies\ClaimPolicy;

use Illuminate\Support\Facades\Auth;

// Телеграм
use Telegram;

class ClaimController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'claims';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Claim::class);

        // Включение контроля активного фильтра 
        $filter_url = autoFilter($request, $this->entity_name);
        if (($filter_url != null) && ($request->filter != 'active')) {
            return Redirect($filter_url);
        }

        // Получаем авторизованного пользователя
        $user = $request->user();


        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------------------

        $claims = Claim::with('lead', 'manager')
        ->moderatorLimit($answer)
        // ->filter($request, 'places_type_id', 'places_types')
        ->booklistFilter($request)
        ->orderBy('created_at', 'desc')
        ->paginate(30);


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            // 'author',               // Автор записи
            // 'sector',               // Направление деятельности
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------


        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('claims.index', compact('claims', 'page_info', 'filter', 'user'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

        // Проверяем право на создание сущности
        $this->authorize(getmethod(__FUNCTION__), Claim::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        $claim = new Claim;
        $claim->name = $request->name;
        // $claim->alias = $request->alias;

        // Вносим общие данные
        $claim->author_id = $user->id;
        $claim->system_item = $request->system_item;
        $claim->moderation = $request->moderation;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        // if($answer['automoderate'] == false){$entity->moderation = 1;};

        // Раскомментировать если требуется запись ID филиала авторизованного пользователя
        // if($filial_id == null){abort(403, 'Операция невозможна. Вы не являетесь сотрудником!');};
        // $entity->filial_id = $filial_id;

        $claim->save();
        return redirect('/admin/entities');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $user = $request->user();

        // ГЛАВНЫЙ ЗАПРОС:
        $claim = Claim::moderatorLimit($answer)
        ->companiesLimit($answer)
        // ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям 
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $claim);

        // Удаляем пользователя с обновлением
        $claim = Claim::moderatorLimit($answer)->where('id', $id)->delete();

        if($claim) {return redirect('/admin/claims');} else {abort(403,'Что-то пошло не так!');};
    }

    // ----------------------------------------------- Ajax -----------------------------------------------------------------

    public function ajax_store(Request $request)
    {

        // Проверяем право на создание сущности
        $this->authorize('create', Claim::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'create');

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // ГЛАВНЫЙ ЗАПРОС: В начале пишем oбращение
        $lead = Lead::with('location', 'main_phones')->findOrFail($request->lead_id);

        // $filial_id = $user->filial_id;

        // Пишем локацию
        // $location_id = $lead->location->id;

        $new_lead = new Lead;
        $new_lead->name = $lead->name;
        $new_lead->filial_id = $user->filial_id;
        $new_lead->stage_id = 2; // Обращение

        $new_lead->lead_type_id = 3; // Сервисное обращение
        $new_lead->lead_method_id = 1; // Звонок

        $new_lead->display = 1; // Включаем видимость
        $new_lead->company_id = $company_id;
        $new_lead->company_name = $lead->company_name;

        // $new_lead->phone = $lead->phone;
        $new_lead->location_id = $lead->location_id;

        $new_lead->author_id = $user->id;
        $new_lead->manager_id = $user->id;
        $new_lead->save();

        // Формируем номера обращения
        $lead_number = getLeadNumbers($user, $new_lead);

        $new_lead->case_number = $lead_number['case'];
        $new_lead->serial_number = $lead_number['serial'];
        $new_lead->save();

        // Конец формирования номера обращения ----------------------------------


        // Телефонный номер
        $new_lead->phones()->attach($lead->main_phone->id, ['main' => 1]); 

        $claim = new Claim;
        $claim->body = $request->body;
        $claim->status = 1;
        $claim->lead_id = $request->lead_id;

        // Формируем номера обращения
        $claim_number = getClaimNumbers($user);
        $claim->case_number = $claim_number['case'];
        $claim->serial_number = $claim_number['serial'];
        $claim->source_lead_id = $new_lead->id;
        $claim->manager_id = $user->id;

        // Вносим общие данные
        $claim->author_id = $user->id;
        $claim->company_id = $company_id;

        $claim->save();

        if ($claim) {

            $lead = Lead::with(['main_phones', 'location.city', 'stage', 'manager', 'claims' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }])->find($request->lead_id);

            if (isset($lead->location->city->name)) {
                $address = $lead->location->city->name . ', ' . $lead->location->address;
            } else {
                $address = $lead->location->address;
            }

            $telegram_message  = "РЕКЛАМАЦИЯ №" . $claim->source_lead->case_number . "\r\n\r\nСерийный номер: " . $claim->serial_number . "\r\nОписание: " . $claim->body . "\r\n\r\nНомер заказа: " . $lead->case_number . "\r\nКлиент: " . $lead->name . "\r\nТелефон: " . $lead->main_phone->phone . "\r\nАдрес: " . $address . "\r\nЭтап: " . $lead->stage->name. "\r\nМенеджер: " . $lead->manager->first_name . " " . $lead->manager->second_name;
            
            $telegram_destinations = User::whereHas('staff', function ($query) {
                $query->whereHas('position', function ($query) {
                    $query->whereHas('notifications', function ($query) {
                        $query->where('notification_id', 2);
                    });
                });
            })
            ->where('telegram_id', '!=', null)
            ->get(['telegram_id']);

            send_message($telegram_destinations, $telegram_message);

            $claims = $lead->claims;

            return view('leads.claim', compact('claims'));
        }   
    }

    public function ajax_finish(Request $request)
    {

        $claim = Claim::findOrFail($request->id);
        // Проверяем право на создание сущности
        $this->authorize('update', $claim);


        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'update');

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $claim = Claim::where('id', $request->id)->update(['status' => null, 'editor_id' => $user_id]);

        if ($claim) {

            $result = [
                'error_status' => 0,
            ];  
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при выполнении рекламации!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);

    }
}
