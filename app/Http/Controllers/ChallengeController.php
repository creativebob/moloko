<?php

namespace App\Http\Controllers;

// Модели
use App\Challenge;
use App\ChallengesType;
use App\Staffer;

use App\Lead;

use Carbon\Carbon;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\ChallengeRequest;

// Политика
use App\Policies\ChallengePolicy;

class ChallengeController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'challenges';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        $user = $request->user();

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Lead::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // --------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------

        $challenges_page = Challenge::with(
            'challenge_type', 
            'author', 
            'appointed', 
            'finisher', 
            'challenges'
        )
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        // ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        // ->filter($request, 'city_id', 'location')
        // ->filter($request, 'stage_id')
        // ->filter($request, 'manager_id')
        // ->dateIntervalFilter($request, 'created_at')
        // ->booklistFilter($request)
        ->orderBy('deadline_date', 'desc')
        ->orderBy('moderation', 'desc')
        // ->orderBy('sort', 'asc')
        ->paginate(30);


        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Задачи пользователя
        $challenges = challenges($request);

        return view('challenges.index', compact('challenges_page', 'page_info', 'challenges'));
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

            // Создание отношений между Car и buyer (Men/Women).
            $item->challenges()->save($challenge);

            $item = $request->model::with(['challenges' => function ($query) {
                $query->with('challenge_type')->whereNull('status')->orderBy('deadline_date', 'asc');
            }])->findOrFail($request->id);

            $challenges = $item->challenges;

            return view('includes.challenges.challenges', compact('challenges'));
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

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_name, getmethod(__FUNCTION__))
        ;

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $user = $request->user();

        // ГЛАВНЫЙ ЗАПРОС:
        $challenge = Challenge::findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $challenge);

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
        $challenges = challenges();
        return view('layouts.challenges_for_me', compact('challenges'));
    }

}
