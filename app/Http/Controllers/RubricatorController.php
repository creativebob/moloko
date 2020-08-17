<?php

namespace App\Http\Controllers;

// Модели
use App\Rubricator;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\System\RubricatorRequest;

class RubricatorController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Rubricator $rubricator)
    {
        $this->middleware('auth');
        $this->rubricator = $rubricator;
        $this->entity_alias = with(new Rubricator)->getTable();;
        $this->entity_dependence = false;
        $this->class = Rubricator::class;
        $this->model = 'App\Rubricator';
    }

    public function index(Request $request)
    {


        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $rubricators = Rubricator::with([
            'author',
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($rubricators);

        return view('rubricators.index',[
            'rubricators' => $rubricators,
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('rubricators.create', [
            'rubricator' => new $this->class,
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }

    public function store(RubricatorRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $rubricator = (new Rubricator())->create($data);

        if ($rubricator) {

            // Сайты
            $rubricator->sites()->attach($request->sites);

            return redirect()->route('rubricators.index');

        } else {
            abort(403, 'Ошибка при записи каталога!');
        }
    }

    public function show(Request $request, $id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $rubricator = Rubricator::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $rubricator);

        // dd($rubricator);
        return view('rubricators.edit', [
            'rubricator' => $rubricator,
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }

    public function update(RubricatorRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $rubricator = Rubricator::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $rubricator);

        $data = $request->input();
        $result = $rubricator->update($data);

        if ($rubricator) {

            // Обновляем сайты
            $rubricator->sites()->sync($request->sites);

            return redirect()->route('rubricators.index');

        } else {
            abort(403, 'Ошибка при обновлении каталога!');
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $rubricator = Rubricator::with(['items'])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $rubricator);

        $rubricator->delete();

        if ($rubricator) {

            return redirect()->route('rubricators.index');

        } else {
            abort(403, 'Ошибка при удалении каталога!');
        }
    }
}
