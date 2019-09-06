<?php

namespace App\Http\Controllers;

// Модели
use App\User;
use App\Lead;
use App\LeadMethod;
use App\LeadType;
use App\Note;
use App\Challenge;

use App\GoodsCategory;
use App\ServicesCategory;
use App\RawsCategory;

use App\CatalogsService;
use App\CatalogsGoods;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\LeadRequest;
use App\Http\Requests\MyStageRequest;


// Специфические классы

use Carbon\Carbon;

// На удаление
use App\Http\Controllers\Session;

// Подрубаем трейт записи и обновления компании
use App\Http\Controllers\Traits\UserControllerTrait;
use App\Http\Controllers\Traits\LeadControllerTrait;


use App\Exports\LeadsExport;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{

    use UserControllerTrait;
    use LeadControllerTrait;

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'leads';
    protected $entity_dependence = true;

    public function index(Request $request)
    {

        $result = extra_right('lead-service');
        $lead_all_managers = extra_right('lead-all-managers');

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_name);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        $user = $request->user();

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Lead::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------

        $leads = Lead::with(
            // 'location.city',
            'choice',
            'lead_type',
            'lead_method',
            'stage',
            // 'challenges.challenge_type',
            // 'challenges.appointed',
            'main_phones'
        )

        ->when($lead_all_managers, function($q){
            return $q->with(['manager' => function($query){
                $query->select('id', 'first_name', 'second_name');
            }]);
        })

        ->withCount(['challenges' => function ($query) {
            $query->whereNull('status');
        }])
        // ->withCount('claims')
        ->manager($user)
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        // ->authors($answer)
        ->whereNull('draft')
        ->systemItem($answer) // Фильтр по системным записям
        ->filter($request, 'city_id', 'location')
        ->filter($request, 'stage_id')
        ->filter($request, 'manager_id')
        ->filter($request, 'lead_type_id')
        ->filter($request, 'lead_method_id')
        ->booleanArrayFilter($request, 'challenges_active_count')
        ->dateIntervalFilter($request, 'created_at')
        ->booklistFilter($request)
        ->orderBy('created_at', 'desc')
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            'city',                 // Город
            'stage',                // Этап
            'lead_method',          // Способ обращения
            'lead_type',            // Тип обращения
            'manager',              // Менеджер
            'date_interval',        // Дата обращения
            'booklist',               // Списки пользователя
            'challenges_active_count' // Активные и не активные задачи
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------


        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Задачи пользователя
        $list_challenges = challenges($request);
        return view('leads.index', compact('leads', 'page_info', 'user', 'filter', 'list_challenges', 'lead_all_managers'));
    }


    public function create(Request $request, $lead_type = 1)
    {

        // Подключение политики
        $this->authorize(__FUNCTION__, Lead::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Отдаем работу по созданию нового лида трейту
        $lead = $this->createLead($request);

        return Redirect('/admin/leads/' . $lead->id . '/edit');
    }


    public function store(LeadRequest $request)
    {

        // Не используется.

    }


    public function show(Request $request, $id)
    {

        dd('Это show - Тупиковая ветка');

    }


    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:

        $lead = Lead::with([
            'location.city',
            'main_phones',
            'extra_phones',
            'medium',
            'campaign',
            'source',
            'site',
            'claims',
            'estimate.items.product',
            'lead_method',
            'choice' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }, 'notes' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }, 'challenges' => function ($query) {
                $query->with('challenge_type')
                ->whereNull('status')
                ->orderBy('deadline_date', 'asc');
            } // Вырезан фрагмент: ,'estimates.workflows.product'
        ])
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        // ->where('manager_id', '!=', 1)
        // ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->moderatorLimit($answer)
        ->findOrFail($id);
        // dd($lead);

        // Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), $lead);

        $lead_methods_list = LeadMethod::whereIn('mode', [1, 2, 3])->get()->pluck('name', 'id');

        // // $all_categories_list = null;
        $goods_categories_list = GoodsCategory::whereNull('parent_id')->get()->mapWithKeys(function ($item) {
            return ['goods-' . $item->id => $item->name];
        })->toArray();


        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_sc = operator_right('services_category', false, getmethod('index'));

        $services_categories_list = ServicesCategory::moderatorLimit($answer_sc)
        ->companiesLimit($answer_sc)
        ->authors($answer_sc)
        ->where('is_direction', true)
        ->get()
        ->mapWithKeys(function ($item) {
            return ['service-' . $item->id => $item->name];
        })->toArray();

        $raws_categories_list = RawsCategory::whereNull('parent_id')->get()->mapWithKeys(function ($item) {
            return ['raw-' . $item->id => $item->name];
        })->toArray();

        $choices = [
            'Товары' => $goods_categories_list,
            'Услуги' => $services_categories_list,
            'Сырье' => $raws_categories_list,
        ];

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Задачи пользователя
        $list_challenges = challenges($request);

        $filial_id = $request->user()->filial_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_cs = operator_right('catalogs_services', false, getmethod('index'));

        $catalogs_services = CatalogsService::with([
            'items' => function ($q) use ($filial_id) {
            $q->with([
                'prices' => function ($q) use ($filial_id) {
                    $q->where('filial_id', $filial_id);
                },
                'childs'
            ]);
            }
        ])
        ->moderatorLimit($answer_cs)
        ->companiesLimit($answer_cs)
        ->authors($answer_cs)
        ->whereHas('sites', function ($q) {
            $q->whereId(1);
        })
        ->get();
//         dd($catalogs_services);

        $catalog_services = $catalogs_services->first();
        // dd($catalog_service);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_cg = operator_right('catalogs_goods', false, getmethod('index'));

        $catalogs_goods = CatalogsGoods::with([
            'items' => function ($q) use ($filial_id) {
                $q->with([
                    'prices' => function ($q) use ($filial_id) {
                        $q->where('filial_id', $filial_id);
                    },
                    'childs'
                ]);
            }
        ])
            ->moderatorLimit($answer_cg)
            ->companiesLimit($answer_cg)
            ->authors($answer_cg)
            ->whereHas('sites', function ($q) {
                $q->whereId(1);
            })
            ->get();
//         dd($catalogs_goods);

        $сatalog_goods = $catalogs_goods->first();
//         dd($atalog_goods);



        return view('leads.edit', compact('lead', 'page_info', 'list_challenges', 'lead_methods_list', 'choices', 'catalog_services', 'сatalog_goods'));
    }

    public function update(LeadRequest $request, MyStageRequest $my_request,  $id)
    {
//        dd($request);
        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $lead = Lead::with('location', 'company')
        ->companiesLimit($answer)
        ->filials($answer)
        ->systemItem($answer)
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $lead);

        // Отдаем работу по редактировнию лида трейту
        $this->updateLead($request, $lead);

        return redirect('/admin/leads');
    }

    public function leads_calls(Request $request)
    {

        Carbon::setLocale('en');
        // dd(Carbon::getLocale());

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_name);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        $user = $request->user();

        // Подключение политики
        $this->authorize(getmethod('index'), Lead::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod('index'));
        // dd($answer);

        // --------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------

        // Запрос с выбором лидов по дате задачи == сегодняшней дате или меньше, не получается отсортировать по дате задачи, т.к. задач может быть много на одном лиде
        $leads = Lead::with(
            'location.city',
            'choice',
            'manager',
            'stage',
            'challenges.challenge_type'
        )
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        // ->authors($answer)
        ->manager($user)
        ->whereNull('draft')
        ->whereHas('challenges', function ($query) {
            $query->whereHas('challenge_type', function ($query) {
                $query->where('id', 2);
            })->whereNull('status')->whereDate('deadline_date', '<=', Carbon::now()->format('Y-m-d'));
        })
        ->systemItem($answer) // Фильтр по системным записям
        ->filter($request, 'city_id', 'location')
        ->filter($request, 'stage_id')
        ->filter($request, 'manager_id')
        ->dateIntervalFilter($request, 'created_at')
        ->booklistFilter($request)
        // ->orderBy('challenges.deadline_date', 'desc')
        ->orderBy('manager_id', 'asc')
        ->orderBy('created_at', 'desc')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // ---------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ----------------------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------

        $filter_query = Lead::with('location.city', 'manager', 'stage')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->manager($user)
        // ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->get();

        $filter['status'] = null;
        $filter['entity_name'] = $this->entity_name;
        $filter['inputs'] = $request->input();

        // Перечень подключаемых фильтров:
        $filter = addFilter($filter, $filter_query, $request, 'Выберите город:', 'city', 'city_id', 'location', 'external-id-one');
        $filter = addFilter($filter, $filter_query, $request, 'Выберите этап:', 'stage', 'stage_id', null, 'internal-id-one');
        $filter = addFilter($filter, $filter_query, $request, 'Менеджер:', 'manager', 'manager_id', null, 'internal-id-one');


        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);


        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Задачи пользователя
        $list_challenges = challenges($request);
        // dd($challenges);

        return view('leads.index', compact('leads', 'page_info', 'filter', 'user', 'list_challenges'));
    }

    public function search(Request $request)
    {

        // Подключение политики
        $this->authorize('index', Lead::class);

        $entity_name = $this->entity_name;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $text_fragment = $request->text_fragment;
        $fragment_phone = NULL;
        $crop_phone = NULL;

        $len_text = strlen($text_fragment);

        if((strlen($text_fragment) == 11)&&(is_numeric($text_fragment))){
            $fragment_phone = $text_fragment;
        }

        if((strlen($text_fragment) == 4)&&(is_numeric($text_fragment))){
            $crop_phone = $text_fragment;
        }

        if(strlen($text_fragment) == 17){
            $fragment_phone = cleanPhone($text_fragment);
        }

        if(strlen($text_fragment) > 6){
            $fragment_case_number = $text_fragment;
        } else {
            $fragment_case_number = '';
        }


        if($len_text > 3){

            // ------------------------------------------------------------------------------------------------------------
            // ГЛАВНЫЙ ЗАПРОС
            // ------------------------------------------------------------------------------------------------------------

            $result_search = Lead::with(
                'location.city',
                'choice',
                'manager',
                'stage',
                'challenges.challenge_type',
                'phones')
            ->companiesLimit($answer)
            ->whereNull('draft')
            ->where(function ($query) use ($fragment_case_number, $text_fragment, $len_text, $fragment_phone, $crop_phone) {

                if($len_text > 5){
                    $query->where('name', $text_fragment);
                };

                if(($len_text > 6)||($len_text < 14)){
                    $query->orWhere('case_number', 'LIKE', $fragment_case_number);
                };

                if(isset($fragment_phone)){
                    $query->orWhereHas('phones', function($query) use ($fragment_phone){
                       $query->where('phone', $fragment_phone);
                   });
                };

                if(isset($crop_phone)){
                    $query->orWhereHas('phones', function($query) use ($crop_phone){
                       $query->where('crop', $crop_phone);
                   });
                };

            })
            ->orderBy('created_at', 'asc')
            ->get();

        } else {
            return '';
        };

        if($result_search->count()){

            return view('includes.search_lead', compact('result_search', 'entity_name'));
        } else {

            return '';
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $user = $request->user();

        // ГЛАВНЫЙ ЗАПРОС:
        $lead = Lead::withCount(['challenges' => function ($query) {
            $query->whereNull('status');
        }])
        ->withCount('claims')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->manager($user)
        // ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $lead);

        // Удаляем комментарии
        $lead->notes()->delete();
        $lead->challenges()->delete();

        // $lead->challenges()->delete();

        // Удаляем пользователя с обновлением
        $lead->destroy($id);

        if($lead) {
            return redirect('/admin/leads');
        } else {
            abort(403,'Что-то пошло не так!');
        };
    }


    // --------------------------------------- Ajax ----------------------------------------------------------

    // Добавление комментария
    public function ajax_add_note(Request $request)
    {

        $lead = Lead::findOrFail($request->id);

        if ($lead) {

            // Получаем данные для авторизованного пользователя
            $user = $request->user();

            $note = new Note;
            $note->body = $request->body;
            $note->company_id = $user->company_id;
            $note->author_id = hideGod($user);

            $lead->notes()->save($note);

            return view($request->entity.'.note', compact('note'));
        }
    }

    public function ajax_autofind_phone(Request $request)
    {



        // Подключение политики
        // $this->authorize('index', Lead::class);

        $phone = $request->phone;
        $lead_id = $request->lead_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_lead = operator_right('leads', true, 'index');

        // --------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------------

        $finded_leads = Lead::with(
            'location.city',
            'choice',
            'manager',
            'stage',
            'challenges.challenge_type',
            'phones')
        ->companiesLimit($answer_lead)
        // ->authors($answer_lead) // Не фильтруем по авторам
        ->systemItem($answer_lead) // Фильтр по системным записям
        // ->whereNull('archive')
        ->whereNull('draft')
        ->whereHas('phones', function($query) use ($phone){
            $query->where('phone', $phone);
        })
        ->where('id', '!=', $lead_id)
        ->orderBy('sort', 'asc')
        ->get();

        $count_finded_leads = $finded_leads->count();

        if($count_finded_leads > 0){
            return view('leads.autofind', compact('finded_leads'));
        } else {
            return '';
        }
    }

    // Освобождение лида
    public function ajax_lead_free(Request $request)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $lead = Lead::findOrFail($request->id);

        if ($user->sex == 1) {
            $phrase_sex = 'освободил';
        } else {
            $phrase_sex = 'освободила';
        }
        $note = add_note($lead, 'Менеджер: '. $user->first_name.' '.$user->second_name.' '.$phrase_sex.' лида.');

        $lead->manager_id = 1;
        $lead->save();

        return response()->json(isset($lead) ?? 'Ошибка при освобождении лида!');
    }

    // Назначение лида
    public function ajax_appointed_check(Request $request)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $user = User::with('staff.position.charges')->findOrFail($user->id);

        foreach ($user->staff as $staffer) {
            // $staffer = $user->staff->first();

            $direction = null;

            foreach ($staffer->position->charges as $charge) {
                if ($charge->alias == 'lead-appointment') {
                    $direction = 1;
                    // break;
                }

                if (isset($request->manager_id)) {
                    if (($charge->alias == 'lead-appointment-self') && ($user->id == $request->manager_id)) {
                        $direction = 1;
                    // break;
                    }
                }
            }
        }
        echo $direction;
    }

    // Прием лида менеджером
    public function ajax_lead_take(Request $request)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $lead = Lead::findOrFail($request->id);

        if ($lead->manager_id == 1) {

            // dd($direction);
            $lead->manager_id = $user->id;

            if($lead->case_number == NULL){

                // Формируем номера обращения
                $lead_number = getLeadNumbers($user, $lead);
                $lead->case_number = $lead_number['case'];
                $lead->serial_number = $lead_number['serial'];
            }

            $lead->editor_id = $user->id;
            $lead->save();

            // Ставим задачу
            $challenge = new Challenge;
            $challenge->company_id = $user->company_id;
            $challenge->appointed_id = $user->id;
            $challenge->challenges_type_id = 2;
            $challenge->author_id = $user->id;

            if ($lead->created_at->format('Y-m-d') == today()->format('Y-m-d')) {
                $challenge->description = "Перезвонить через 15 минут!\r\n";
                $challenge->priority_id = 3;
                // Отдаем график работы и время в секундах
                $challenge->deadline_date = getDeadline(null, 60*15);
            } else {
                $description = "Актуализировать информацию по лиду,\r\n";
                $description .= "этап - ".$lead->stage->name;
                $challenge->description = $description;
                $challenge->priority_id = 2;
                // Отдаем график работы и время в секундах (предварительно проверяем юзера на бога)
                $challenge->deadline_date = getDeadline(getSchedule($user), 60*15);
            }

            $lead->challenges()->save($challenge);
            $lead->increment('challenges_active_count');

            if ($user->sex == 1) {
                $phrase_sex = 'принял';
            } else {
                $phrase_sex = 'приняла';
            }

            $note = add_note($lead, 'Менеджер: '. $user->first_name.' '.$user->second_name.' '.$phrase_sex.' лида.');

            $result = [
                'id' => $lead->id,
                'name' => $lead->name,
                'case_number' => $lead->case_number,
                'manager' => $lead->manager->first_name.' '.$lead->manager->second_name,
            ];
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }
    }

    // Назначение лида
    public function ajax_distribute(Request $request)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $lead = Lead::findOrFail($request->lead_id);

        $manager = User::findOrFail($request->appointed_id);
        $lead->manager_id = $manager->id;

        // Если номер пуст и планируется назначение на сотрудника, а не бота - то генерируем номер!
        if(($lead->case_number == NULL)&&($request->appointed_id != 1)){

            // Формируем номера обращения
            $lead_number = getLeadNumbers($manager, $lead);
            $lead->case_number = $lead_number['case'];
            $lead->serial_number = $lead_number['serial'];
        }

        $lead->editor_id = $user->id;
        $lead->save();

        if ($request->appointed_id != 1) {

            // Ставим задачу

            $description = "Актуализировать информацию по лиду,\r\n";
            $description .= "этап - ".$lead->stage->name;

            $challenge = new Challenge;
            $challenge->company_id = $user->company_id;
            $challenge->appointed_id = $request->appointed_id;
            $challenge->challenges_type_id = 2;
            $challenge->description = $description;
            $challenge->priority_id = 2;
            $challenge->author_id = $user->id;

            // Отдаем график работы и время в секундах (предварительно проверяем юзера на бога)
            $challenge->deadline_date = getDeadline(getSchedule($manager), 60*60);

            $lead->challenges()->save($challenge);
            $lead->increment('challenges_active_count');

        }


        if ($user->sex == 1) {
            $phrase_sex = 'назначил';
        } else {
            $phrase_sex = 'назначила';
        }

        // Пишем комментарий
        $note = add_note($lead, $user->first_name.' '.$user->second_name. ' '.$phrase_sex.' лида менеджеру '. $manager->first_name.' '.$manager->second_name);

        // Оповещаем менеджера о назначении
        if (isset($manager->telegram_id)) {
            $message = $user->first_name.' '.$user->second_name. ' '.$phrase_sex.' вам лида: ' . $lead->case_number . "\r\n\r\n";
            $message = lead_info($message, $lead);
            $telegram_destinations[] = $manager;

            send_message($telegram_destinations, $message);

        } else {

            if (isset($user->telegram_id)) {

                // Если у менеджера нет телеграмма, оповещаем руководителя
                $message = 'У ' . $manager->first_name . ' ' . $manager->second_name . " отсутствует Telegram ID, оповестите его другим способом!\r\n\r\n";
                $message = lead_info($message, $lead);

                $telegram_destinations[] = $user;
                send_message($telegram_destinations, $message);
            } else {
                $note = add_note($lead, 'Оповещение никому не выслано, так как ни у кого нет telegram Id. Это просто комон какой-то!');
            }
        }

        $result = [
            'id' => $lead->id,
            'name' => $lead->name,
            'case_number' => $lead->case_number,
            'manager' => $lead->manager->first_name.' '.$lead->manager->second_name,
        ];

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function ajax_lead_appointed(Request $request)
    {

        $users = User::with('staff.position')
        ->whereHas('staff', function ($query) {
            $query->whereNotNull('user_id')->whereHas('position', function ($query) {
                $query->whereHas('charges', function ($query) {
                    $query->whereIn('alias', ['lead-regular', 'lead-service', 'lead-dealer']);
                });
            });
        })
        ->orWhere('id', 1)
        ->orderBy('second_name')
        ->get();
        // ->pluck('name', 'id');
        // dd($users);

        $users_list = [];
        foreach ($users as $user) {
            if (isset($user->staff->first()->position->name)) {
                $position = $user->staff->first()->position->name;
            } else {
                $position = 'Cyberdyne Systems 101 серии 800';
            }

            $users_list[$user->id] = $user->name . ' (' . $position . ')';
        }

        // dd($users_list);
        $lead_id = $request->id;
        // $lead_id = 1;
        return view('leads.modal-appointed', compact('users_list', 'lead_id'));
    }

    public function ajax_open_change_lead_type(Request $request)
    {
        $lead_type_list = LeadType::pluck('name', 'id');
        $lead_type_id = $request->lead_type_id;
        $lead_id = $request->lead_id;

        return view('leads.modal-change-lead-type', compact('lead_type_list', 'lead_type_id', 'lead_id'));
    }

    public function ajax_change_lead_type(Request $request)
    {
        $user = $request->user();
        $lead_id = $request->lead_id;
        $new_lead_type_id = $request->lead_type_id;

        $lead = Lead::findOrFail($lead_id);
        $lead_type_id = $lead->lead_type_id;
        $old_lead_type_name = $lead->lead_type->name;

        $manager_id = $lead->manager_id;
        $manager = User::findOrFail($manager_id);


        if($new_lead_type_id !== $lead_type_id){

            $lead->lead_type_id = $new_lead_type_id;

            // Получаем старый номер, если он существовал
            if(isset($lead->case_number)){$old_case_number = $lead->case_number;};
            if(isset($lead->serial_number)){$old_serial_number = $lead->case_number;};

            // Создаем пустой контейнер для нового номера
            $lead_number = [];
            $lead_number['case'] = null;
            $lead_number['serial'] = null;

            $lead_number = getLeadNumbers($manager, $lead);

            $lead->case_number = $lead_number['case'];
            $lead->serial_number = $lead_number['serial'];

            $lead->save();
            $lead = Lead::findOrFail($lead_id);
            $new_lead_type_name = $lead->lead_type->name;

            $note = add_note($lead, 'Сотрудник '. $user->first_name.' '.$user->second_name.' изменил тип обращения c "' . $old_lead_type_name . '" на "' . $new_lead_type_name . '", в связи с чем был изменен номер с '. $old_case_number . ' на ' . $lead_number['case']);

        }

        $data = [];
        $data['case_number'] = $lead->case_number;
        $data['lead_type_name'] = $lead->lead_type->name;

        return $data;
    }

    public function export()
    {
        return Excel::download(new LeadsExport, 'Воротная компания "Марс".xlsx');
    }

}
