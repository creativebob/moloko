<?php

namespace App\Http\Controllers;

// Модели
use App\Note;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\NoteRequest;

// Политика
use App\Policies\NotePolicy;

class NoteController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'notes';
    protected $entity_dependence = false;

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(NoteRequest $request)
    {
      
        // $body = 'sfsdf432';
        // $entity_model = 'App\Lead';
        // $id = 1;      

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Note::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $company_id = $user->company_id;

        $note = new Note;

        $note->body = $request->body;
        $note->company_id = $company_id;
        $note->author_id = $user_id;
        $note->save();

        if ($note) {

            $item = $request->model::findOrFail($request->id);

            // Создание отношений между Car и buyer (Men/Women).
            $item->notes()->save($note);
    
            return view('includes.notes.note', compact('note'));
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $note = Note::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $note);

        if ($request->type == 'back') {
            return view('includes.notes.note', compact('note'));
        } else {
            return view('includes.notes.note-edit', compact('note'));
        }
    }

    public function update(NoteRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $note = Note::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $note);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $note->body = $request->body;
        $note->editor_id = $user_id;
        $note->save();

        if ($note) {
            return view('includes.notes.note', compact('note'));
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $note = Note::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $note);

        // Удаляем ajax
        $note = Note::destroy($id);

        if ($note) {
            $result = [
                'error_status' => 0,
            ];
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении комментария!',
            ];
        }   

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
