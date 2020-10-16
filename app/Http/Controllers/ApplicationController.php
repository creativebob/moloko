<?php

namespace App\Http\Controllers;

// Модели
use App\Application;
// use App\ApplicationComposition;
use App\Entity;

use Illuminate\Http\Request;
use Carbon\Carbon;

// Политика
// use App\Policies\NotePolicy;

class ApplicationController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'applications';
    protected $entity_dependence = false;

    public function index(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Application::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $applications = Application::with('author', 'stage')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        // ->whereNull('draft')
        ->booklistFilter($request)  // Фильтр по спискам
        ->filter($request, 'supplier_id')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($applications);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            'supplier',             // Поставщики
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------


        // Инфо о странице
        $pageInfo = pageInfo($this->entity_name);

        return view('applications.index', compact('applications', 'pageInfo', 'filter'));
    }


    public function create()
    {

        //Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Application::class);

        // Подключение политики
        $application = new Application;

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_name);

        return view('applications.create', compact('application', 'pageInfo'));
    }


    public function store(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Application::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $application = new Application;
        $application->company_id = $user->company->id;
        $application->name = $request->name;
        $application->description = $request->description;
        $application->supplier_id = $request->supplier_id;
        $application->number = $request->number;
        $application->stage_id = $request->stage_id;
        $application->amount = $request->amount;
        $application->draft = $request->draft;

        if($application->draft == null){
            $application->send_date = Carbon::now();
        } else {
            $application->send_date = null;
        };

        $application->author_id = $user_id;
        $application->save();

        return redirect('/admin/applications');
    }

    public function show(Request $request, $id)
    {
        //
    }

    public function edit(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $application = Application::with(
            'stage',
            'author')
        ->moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->find($id);

        $this->authorize(getmethod(__FUNCTION__), $application);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_name);

        return view('applications.edit', compact('application', 'pageInfo'));
    }


    public function update(Request $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Компания пользователя
        $user_company = $user->company;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $application = Application::with(
            'stage',
            'author')
        ->moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $application);

        $application->company_id = $user->company->id;
        $application->name = $request->name;
        $application->description = $request->description;
        $application->supplier_id = $request->supplier_id;
        $application->number = $request->number;
        $application->stage_id = $request->stage_id;
        $application->amount = $request->amount;
        $application->draft = $request->draft;

        if($application->draft == null){
            $application->send_date = Carbon::now();
        } else {
            $application->send_date = null;
        };

        $application->author_id = $user_id;
        $application->save();

        return redirect('/admin/applications');
    }


    public function destroy(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $application = Application::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $application);

        if($application) {

            $user = $request->user();
            $user_id = hideGod($user);

            $application->editor_id = $user_id;
            $application->save();

            $application = Application::destroy($id);

            // Удаляем компанию с обновлением
            if($application) {

                return redirect('/admin/applications');

            } else {
                abort(403, 'Ошибка при удалении компании');
            }

        } else {
            abort(403, 'Компания не найдена');
        }
    }

    // public function ajax_check(Request $request)
    // {

    //     // Получаем авторизованного пользователя
    //     $user = $request->user();

    //     // Скрываем бога
    //     $user_id = hideGod($user);

    //     // Получаем компанию
    //     $company_id = $user->company_id;

    //     // Находим или создаем заказ для лида
    //     $application = Application::firstOrCreate(['lead_id' => $request->lead_id, 'draft' => 1, 'company_id' => $company_id], ['author_id' => $user_id]);
    //     // $application = Application::firstOrCreate(['lead_id' => 9443, 'draft' => null, 'company_id' => $company_id], ['author_id' => $user_id]);

    //     // Находим сущность, чтоб опрелделить модель
    //     $entity = Entity::where('alias', $request->entity)->first();
    //     // $entity = Entity::where('alias', 'goods')->first();

    //     $type = $request->entity;
    //     // $type = 'goods';

    //     // Формируем позицию заказа
    //     $composition = new ApplicationComposition;

    //     $composition->product_id = $request->item_id;
    //     // $composition->order_compositions_id = 1;
    //     $composition->product_type = $entity->model;

    //     $composition->application_id = $application->id;
    //     $composition->company_id = $company_id;
    //     $composition->author_id = $user_id;
    //     $composition->count = 1;
    //     $composition->save();

    //     // dd($composition->product);

    //     // $composition->notes()->save($note);

    //     // $application->compositions()->associate($composition)->save();

    //     return view('leads.' . $type, compact('composition'));

    // }

    // public function ajax_destroy_composition(Request $request, $id)
    // {


    //     // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    //     // $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    //     // ГЛАВНЫЙ ЗАПРОС:
    //     // $note = Note::moderatorLimit($answer)->find($id);

    //     // Подключение политики
    //     // $this->authorize(getmethod(__FUNCTION__), $note);

    //     $application_composition = ApplicationComposition::find($id);

    //     // Удаляем ajax
    //     $application_composition->delete();

    //     if ($application_composition) {
    //         $result = [
    //             'error_status' => 0,
    //         ];
    //     } else {

    //         $result = [
    //             'error_status' => 1,
    //             'error_message' => 'Ошибка при удалении состава заказа!',
    //         ];
    //     }

    //     return json_encode($result, JSON_UNESCAPED_UNICODE);
    // }
}
