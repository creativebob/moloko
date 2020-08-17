<?php

namespace App\Http\Controllers;

use App\Feedback;
use Illuminate\Http\Request;
use App\Http\Requests\System\FeedbackRequest;
use Carbon\Carbon;

class FeedbackController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'feedback';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_name);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Feedback::class);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------------------

        $feedback = Feedback::moderatorLimit($answer)
        // ->filter($request, 'places_type_id', 'places_types')
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_name);

        return view('feedback.index', compact('feedback', 'pageInfo', 'filter', 'user'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Feedback::class);

        $feedback = new Feedback;

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_name);

        return view('feedback.create', compact('feedback', 'pageInfo'));
    }

    public function store(FeedbackRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Feedback::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем авторизованного пользователя
        $user = $request->user();
        $user_id = $user->id;

        $feedback = new Feedback;
        $feedback->person = $request->person;
        $feedback->job = $request->job;
        $feedback->body = $request->body;
        $feedback->call_date = outPickMeUp($request->call_date);

        if($user->company_id != null){
            $feedback->company_id = $user->company_id;
        } else {
            $feedback->company_id = null;
        };

        $feedback->author_id = $user->id;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if($answer['automoderate'] == false){
            $feedback->moderation = true;
        };

        $feedback->save();
        return redirect('/admin/feedback');
    }


    public function show($id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $place = Place::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('update', $role);

        $role->name = $request->name;
        $role->description = $request->description;

        $role->save();

        return redirect('/admin/roles');

    }


    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $feedback = Feedback::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $feedback);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_name);

        return view('feedback.edit', compact('feedback', 'pageInfo'));
    }


    public function update(FeedbackRequest $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));


        // ГЛАВНЫЙ ЗАПРОС:
        $feedback = Feedback::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('update', $feedback);

        $feedback->person = $request->person;
        $feedback->job = $request->job;
        $feedback->body = $request->body;

        $feedback->display = $request->display;

        $feedback->call_date = outPickMeUp($request->call_date);

        // Модерируем
        if($answer['automoderate']){$feedback->moderation = null;};

        $feedback->save();

        // Если запись удачна - будем записывать связи
        if($feedback){

        } else {
            abort(403, 'Ошибка записи отзыва!');
        };

        return redirect('/admin/feedback');

    }


    public function destroy(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $feedback = Feedback::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $feedback);

        // Удаляем пользователя с обновлением
        $feedback = Feedback::moderatorLimit($answer)->where('id', $id)->delete();

        if($feedback) {return redirect('/admin/feedback');} else {abort(403,'Что-то пошло не так!');};
    }

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;

        foreach ($request->feedback as $item) {
            Feedback::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
    }

    // Системная запись
    public function ajax_system(Request $request)
    {

        if ($request->action == 'lock') {
            $system = 1;
        } else {
            $system = null;
        }

        $item = Feedback::where('id', $request->id)->update(['system' => $system]);

        if ($item) {

            $result = [
                'error_status' => 0,
            ];
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении статуса системной записи!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // Отображение на сайте
    public function ajax_display(Request $request)
    {

        if ($request->action == 'hide') {
            $display = null;
        } else {
            $display = 1;
        }

        $item = Feedback::where('id', $request->id)->update(['display' => $display]);

        if ($item) {

            $result = [
                'error_status' => 0,
            ];
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении отображения на сайте!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
