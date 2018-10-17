<?php

namespace App\Http\Controllers;

// Модели
use App\Challenge;
use App\ChallengesType;
use App\Staffer;
use App\Lead;
use App\User;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\ChallengeRequest;

// Политика
use App\Policies\ChallengePolicy;

// Карбон
use Carbon\Carbon;

// Телеграм
use Telegram;

class ChallengeController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'challenges';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Lead::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // --------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------

        $challenges = Challenge::with(
            'challenge_type', 
            'author', 
            'appointed', 
            'finisher', 
            'challenges'
        )
        ->companiesLimit($answer)
        // ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        // ->authors($answer)
        // ->filter($request, 'city_id', 'location')
        // ->filter($request, 'stage_id')
        ->filter($request, 'appointed_id')
        ->filter($request, 'author_id')
        // ->statusFilter($request, 'status')
        ->dateIntervalFilter($request, 'deadline_date')
        // ->booklistFilter($request)
        ->orderBy('deadline_date', 'desc')
        ->orderBy('moderation', 'desc')
        // ->orderBy('sort', 'asc')
        ->paginate(30);

        // dd($challenges);
        // --------------------------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ---------------------------------------------------------------------------------------------
        // --------------------------------------------------------------------------------------------------------------------------

        $filter_query = Challenge::with(
            'challenge_type', 
            'author', 
            'appointed', 
            'finisher', 
            'challenges'
        )
        ->companiesLimit($answer)
        // ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->get();



        $filter['status'] = null;
        $filter['entity_name'] = $this->entity_name;
        $filter['inputs'] = $request->input();

        // Перечень подключаемых фильтров:
        // $filter = addFilter($filter, $filter_query, $request, 'Выберите город:', 'city', 'city_id', 'location', 'external-id-one');
        
        // $filter = addFilter($filter, $filter_query, $request, 'Выберите этап:', 'stage', 'stage_id', null, 'internal-id-one');
        
        $filter = addFilter($filter, $filter_query, $request, 'Исполнитель:', 'appointed', 'appointed_id', null, 'internal-id-one');
        $filter = addFilter($filter, $filter_query, $request, 'Автор:', 'author', 'author_id', null, 'internal-id-one');

        // $filter = addFilter($filter, $filter_query, $request, 'Статус:', 'status_result', 'status', null, 'internal-text');

        $filter = addFilterInterval($filter, $this->entity_name, $request, 'date_start', 'date_end');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
        // dd($page_info);

        // Задачи пользователя
        $list_challenges = challenges($request);

        // dd($filter);

        return view('challenges.index', compact('challenges', 'page_info', 'list_challenges', 'filter'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Challenge::class);

        $challenge = new Challenge;

        $challenges_types_list = ChallengesType::get()->pluck('name', 'id');

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_staff = operator_right('staff', false, 'index');

        // Главный запрос
        $staff = Staffer::with('user')
        ->moderatorLimit($answer_staff)
        ->companiesLimit($answer_staff)
        // ->authors($answer_staff)
        ->systemItem($answer_staff) // Фильтр по системным записям
        ->get();
        // dd($staff);

        $staff_list = [];
        foreach ($staff as $staffer) {
            // Исключаем вакансии
            if (isset($staffer->user->id)) {
                $staff_list[$staffer->user->id] = $staffer->user->second_name.' '.$staffer->user->first_name;
            }
        }
        // dd($staff_list);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $user_id = $user->id;

        return view('includes.modals.modal-add-challenge', compact('challenge', 'challenges_types_list', 'staff_list', 'user_id'));
    }

    public function store(Request $request)
    {

        // $body = 'sfsdf432';
        // $entity_model = 'App\Lead';
        // $id = 1;  
        // dd($request);    

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Challenge::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $company_id = $user->company_id;

        $challenge = new Challenge;

        $deadline_date_explode = explode('.', $request->deadline_date);
        $deadline_date = $deadline_date_explode[2].'-'.$deadline_date_explode[1].'-'.$deadline_date_explode[0];
        // dd($deadline_date);

        $deadline_time_explode = explode(':', $request->deadline_time);
        $deadline_time = $deadline_time_explode[0].':'.$deadline_time_explode[1].':00';
        // dd($deadline_time);

        $deadline = $deadline_date.' '.$deadline_time;
        // dd($deadline);

        $challenge->deadline_date = $deadline;

        $challenge->challenges_type_id = $request->challenges_type_id;
        $challenge->appointed_id = $request->appointed_id;
        $challenge->description = $request->description;

        $challenge->company_id = $company_id;
        $challenge->author_id = $user_id;
        $challenge->save();

        if ($challenge) {

            $item = $request->model::findOrFail($request->id);

            // Сохранение отношения
            $item->challenges()->save($challenge);

            // Оповещение в telegram, если автор не является исполнителем
            if ($challenge->appointed_id != $user_id) {
                $telegram_message  = "ПОСТАВЛЕНА ЗАДАЧА\r\n\r\n";

                $telegram_message .= "Действие: " . $challenge->challenge_type->name . "\r\n";
                $telegram_message .= "Автор: " . $user->first_name . " " . $user->second_name . "\r\n";
                $telegram_message .= "Дедлайн: " . $challenge->deadline_date->format('d.m.Y - H:i') . "\r\n";
                if (isset($challenge->description)) {
                    $telegram_message .= "Описание: " . $challenge->description . "\r\n";  
                }

                $telegram_message .= "\r\n";

                // Если задача для лида
                if ($request->model = 'App\Lead') {

                    $telegram_message .= "Информация по лиду:\r\n";
                    $telegram_message .= "Номер: " . $item->case_number . "\r\n";
                    $telegram_message .= "Имя: " . $item->name . "\r\n";
                    $telegram_message .= "Телефон: " . (isset($item->main_phone->phone) ? decorPhone($item->main_phone->phone) : 'Номер не указан') . "\r\n";
                }


                $telegram_destinations = User::where('id', $challenge->appointed_id)
                ->where('telegram_id', '!=', null)
                ->get(['telegram_id']);

                send_message($telegram_destinations, $telegram_message);
            }


            $item = $request->model::with(['challenges' => function ($query) {
                $query->with('challenge_type')->whereNull('status')->orderBy('deadline_date', 'asc');
            }])->findOrFail($request->id);

            $challenges = $item->challenges;

            return view('includes.challenges.challenges', compact('challenges'));
        }
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

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_name, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $challenge = Challenge::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $challenge);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $challenge->status = 1;
        $challenge->finisher_id = $user_id;
        $challenge->completed_date = Carbon::now();
        $challenge->save();

        if ($challenge) {

            // Оповещение в telegram, если исполнитель не является автором
            if ($challenge->finisher_id != $challenge->author_id) {
                $telegram_message = "ЗАДАЧА ВЫПОЛНЕНА\r\n\r\n";

                $telegram_message .= "Действие: " . $challenge->challenge_type->name . "\r\n";
                $telegram_message .= "Дедлайн: " . $challenge->deadline_date->format('d.m.Y - H:i') . "\r\n";
                $telegram_message .= "Дата выполнения: " . Carbon::now()->format('d.m.Y - H:i') . "\r\n";
                $telegram_message .= "Исполнитель: " . $user->first_name . " " . $user->second_name;

                if (isset($challenge->description)) {
                    $telegram_message .= "Описание: " . $challenge->description. "\r\n";  
                }

                $telegram_message .= "\r\n";

                // Если задача для лида
                if (isset($challenge->challenges->lead_method_id)) {

                    $telegram_message .= "Информация по лиду:\r\n";
                    $telegram_message .= "Номер: " . $challenge->challenges->case_number . "\r\n";
                    $telegram_message .= "Имя: " . $challenge->challenges->name . "\r\n";
                    $telegram_message .= "Телефон: " . (isset($challenge->challenges->main_phone->phone) ? decorPhone($challenge->challenges->main_phone->phone) : 'Номер не указан') . "\r\n";
                }

                $telegram_destinations = User::where('id', $challenge->author_id)
                ->where('telegram_id', '!=', null)
                ->get(['telegram_id']);

                send_message($telegram_destinations, $telegram_message);
            }

            $result = [
                'error_status' => 0,
            ];
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при выполнении задачи!',
            ];
        }  

        return json_encode($result, JSON_UNESCAPED_UNICODE); 
    }

    public function destroy(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $user = $request->user();

        // ГЛАВНЫЙ ЗАПРОС:
        $challenge = Challenge::findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $challenge);

        // Оповещение в telegram, если исполнитель не является автором
        if ($challenge->appointed_id != $user->id) {
            $telegram_message  = "ЗАДАЧА СНЯТА\r\n\r\n"; 

            $telegram_message .= "Действие: " . $challenge->challenge_type->name . "\r\n";
            $telegram_message .= "Дедлайн: " . $challenge->deadline_date->format('d.m.Y - H:i') . "\r\n";
            $telegram_message .= "Дата снятия: " . Carbon::now()->format('d.m.Y - H:i') . "\r\n";
            $telegram_message .= "Снял: " . $user->first_name . " " . $user->second_name . "\r\n";

            if (isset($challenge->description)) {
                $telegram_message .= "Описание: " . $challenge->description. "\r\n";  
            }

            $telegram_message .= "\r\n";

            // Если задача для лида
            if (isset($challenge->challenges->lead_method_id)) {

                $telegram_message .= "Информация по лиду:\r\n";
                $telegram_message .= "Номер: " . $challenge->challenges->case_number . "\r\n";
                $telegram_message .= "Имя: " . $challenge->challenges->name . "\r\n";
                $telegram_message .= "Телефон: " . (isset($challenge->challenges->main_phone->phone) ? decorPhone($challenge->challenges->main_phone->phone) : 'Номер не указан') . "\r\n";
            }

            $telegram_destinations = User::where('id', $challenge->appointed_id)
            ->where('telegram_id', '!=', null)
            ->get(['telegram_id']);

            send_message($telegram_destinations, $telegram_message);
        }

        // Удаляем пользователя с обновлением
        $challenge->forceDelete();

        if ($challenge) {



            $result = [
                'error_status' => 0,
            ];  
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении освобождении лида!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function ajax_get_challenges()
    {
        $list_challenges = challenges();
        return view('layouts.challenges_for_me', compact('list_challenges'));
    }

}
