<?php

namespace App\Http\Controllers;

// Модели
use App\Entity;
use App\Note;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\System\NoteRequest;


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

        $model = Entity::where('alias', $request->entity)
            ->value('model');

        $item = $model::find($request->id);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $note = new Note;
        $note->body = $request->body;
        $note->company_id = $user->company_id;
        $note->author_id = hideGod($user);

        $item->notes()->save($note);

        return view('includes.notes.note', compact('note'));
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
        $note = Note::moderatorLimit($answer)
            ->find($id);

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

        $note = Note::moderatorLimit(operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__)))->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $note);

        $note->body = $request->body;
        $note->editor_id = hideGod($request->user());
        $note->save();

        if ($note) {
            return view('includes.notes.note', compact('note'));
        }
    }

    public function destroy(Request $request, $id)
    {

        $note = Note::moderatorLimit(operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__)))->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $note);

        return response()->json($note->delete());
    }
}
