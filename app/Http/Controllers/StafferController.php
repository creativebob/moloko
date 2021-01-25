<?php

namespace App\Http\Controllers;


use App\User;
use App\Http\Requests\System\StafferRequest;
use App\Staffer;
use App\Http\Requests\System\EmployeeRequest;
use Illuminate\Http\Request;

class StafferController extends Controller
{

    /**
     * StafferController constructor
     * @param Staffer $staffer
     */
    public function __construct(Staffer $staffer)
    {
        $this->middleware('auth');
        $this->staffer = $staffer;
        $this->class = Staffer::class;
        $this->model = 'App\Staffer';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = true;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
//        $staff = $this->staffer->getIndex($request, $answer);
        $staff = Staffer::with([
            'filial',
            'department',
            'user.main_phones',
            'position',
            'employee',
            'company.filials',
            'actual_employees'
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->filials($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->booklistFilter($request)
            ->where('archive', false)
            ->filter($request, 'position_id')
            ->filter($request, 'department_id')
            ->dateIntervalFilter($request, 'date_employment')
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc')
            ->paginate(30);
//         dd($staff);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'position',             // Должность
            'department',           // Отдел
            'date_interval',        // Дата
            'booklist'              // Списки пользователя
        ]);
        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('system.pages.hr.staff.index', compact('staff', 'pageInfo', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        return redirect()->route('departments.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StafferRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StafferRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $staffer = Staffer::create($data);

        if ($staffer) {

        // Переадресовываем на index
//            return redirect()->route('staff.index');
            return redirect()->route('departments.index', [
                'id' => $staffer->id,
                'item' => $this->entity_alias
            ]);
        } else {
            abort(403, 'Ошибка при записи');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     */
    public function show($id)
    {
        //
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
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $staffer = Staffer::with([
            'position',
            'schedules.worktimes',
            'employee'
        ])
        ->moderatorLimit($answer)
        ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $staffer);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('system.pages.hr.staff.edit', compact('staffer', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(EmployeeRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, true, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $staffer = Staffer::with([
            'schedules.worktimes',
            'position' => function($q) {
                $q->with([
                   'roles',
                   'notifications'
                ]);
            },
            'employee'
        ])
            ->moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $staffer);

        // TODO - 26.03.20 - Возмножно этот функционал останется только в employees

        logs('hr') ->info("============== ОБНОВЛЕНИЕ {$staffer->getTable()} с id: {$staffer->id} ========");

        // Если на должность назначен сотрудник
        if (isset($staffer->employee)) {

            $employee = $staffer->employee;
            $employment_date = $request->employment_date;

            if ($employee->employment_date != $employment_date) {
                $employee->employment_date = $employment_date;
                $employee->save();
            }

            logs('hr')->info("На staffer: {$staffer->id} назнанчен сотрудник {$staffer->user->name}");

        } else {

            $staffer->employees()->create([
                'user_id' => $request->user_id,
                'employment_date' => $request->employment_date,
                'dismissal_date' => null,
            ]);

            $staffer->load('employee');
            $employee = $staffer->employee;

            $staffer->user_id = $request->user_id;

            $roles = [];
            foreach ($staffer->position->roles as $role) {
                $roles[$role->id] = [
                    'department_id' => $staffer->filial_id,
                    'position_id' => $staffer->position_id,
                ];
            }

            $user = User::find($request->user_id);

            $user->roles()->attach($roles);

            $notifications = [];
            foreach ($staffer->position->notifications as $notification) {
                $notifications[] = $notification->id;
            }
//            dd($notifications);
            $user->notifications()->attach($notifications);

            logs('hr')->info("На staffer: {$staffer->id} назнанчен сотрудник {$user->name}");

        }


        // dd($employee);
        // dd($staffer);

        // Если пришла дата увольнения, то снимаем с должности в штате и удаляем роли
        if (isset($request->dismissal_date)) {


            $employee->dismissal_date = $request->dismissal_date;
            $employee->dismissal_description = $request->dismissal_description;
            $employee->save();


            // Удаляем должность и права данного юзера
            // TODO - 05.12.18 - решали что при увольнении сносить все права (включая спецправа), т.к. должность одна пока что
            $user = User::find($staffer->user_id);
            $res = $user->roles()->detach();
            $res = $user->notifications()->detach();

            // Освобождаем штат
            $staffer->user_id = null;

            logs('hr')->info("C staffer: {$staffer->id} уволен сотрудник {$user->name}");

        }

        // Расписание для штата
        setSchedule($request, $staffer);

        $staffer->save();

        logs('hr')->info("============== ЗАВЕРШЕНО ОБНОВЛЕНИЕ {$staffer->getTable()} с id: {$staffer->id} ========

            ");

        if ($staffer) {
            return redirect()->route('staff.index');
        } else {
            abort(403, 'Ошибка при обновлении штата!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Архивация указанного ресурса
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function archive($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, 'delete');

        // ГЛАВНЫЙ ЗАПРОС:
        $staffer = Staffer::with([
            'actual_employees'
        ])
        ->moderatorLimit($answer)
            ->find($id);

        if ($staffer) {
            // Подключение политики
            $this->authorize('delete', $staffer);

            $staffer->archive = true;
            $staffer->editor_id = hideGod(auth()->user());
            $staffer->save();

            if ($staffer) {
                return redirect()->route('staff.index');
            } else {
                abort(403, 'Ошибка при архивации');
            }
        } else {
            abort(403, 'Не найдено');
        }
    }
}
