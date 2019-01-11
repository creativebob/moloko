<?php

namespace App\Http\Controllers;

// Модели
use App\Indicator;

use App\Direction;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\IndicatorRequest;

class IndicatorController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Indicator $indicator)
    {
        $this->middleware('auth');
        $this->indicator = $indicator;
        $this->class = Indicator::class;
        $this->model = 'App\Indicator';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index()
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $indicators = Indicator::with([
            'category',
            'entity',
            'unit'
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->systemItem($answer)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        return view('indicators.index',
            [
                'indicators' => $indicators,
                'page_info' => pageInfo($this->entity_alias),
            ]
        );
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('indicators.create', [
            'indicator' => new $this->class,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function store(IndicatorRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Наполняем сущность данными
        $indicator = new Indicator;

        $indicator->name = $request->name;
        $indicator->description = $request->description;

        $indicator->indicators_category_id = $request->indicators_category_id;
        $indicator->entity_id = $request->entity_id;
        $indicator->unit_id = $request->unit_id;
        $indicator->period_id = $request->period_id;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
        if($answer['automoderate'] == false){
            $indicator->moderation = 1;
        }

        // Системная запись
        $indicator->system_item = $request->system_item;
        $indicator->display = $request->display;

        if (isset($request->direction_id)) {

            $direction = Direction::findOrFail($request->direction_id);
            $indicator->category_id = $direction->category_id;
            $indicator->category_type = $direction->category_type;

        }


        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $indicator->company_id = $user->company_id;
        $indicator->author_id = hideGod($user);

        $indicator->save();

        if ($indicator) {
            // Переадресовываем на index
            return redirect()->route('indicators.index');
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи показателя!'
            ];
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $indicator = Indicator::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $indicator);

        // $indicator = $indicator->load('category', 'entity', 'indicators_category');
        // dd($indicator);

        return view('indicators.edit', [
            'indicator' => $indicator->load('category', 'entity', 'indicators_category'),
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function update(IndicatorRequest $request, $id)
    {

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $indicator = Indicator::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $indicator);

        // dd($request);

        $indicator->name = $request->name;
        $indicator->description = $request->description;

        // $indicator->indicators_category_id = $request->indicators_category_id;
        // $indicator->entity_id = $request->entity_id;
        // $indicator->unit_id = $request->unit_id;
        // $indicator->period_id = $request->period_id;

        // Системная запись
        $indicator->system_item = $request->system_item;
        $indicator->display = $request->display;
        $indicator->moderation = $request->moderation;

        $indicator->editor_id = hideGod($request->user());

        $indicator->save();

        if ($indicator) {
            // Переадресовываем на index
            return redirect()->route('indicators.index');
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении показателя!'
            ];
        }
    }

    public function destroy(Request $request, $id)
    {

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $indicator = Indicator::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $indicator);

        $indicator->editor_id = hideGod($request->user());
        $indicator->save();

        // Удаляем альбом с обновлением
        $indicator->delete();

        if ($indicator) {
            // Переадресовываем на index
            return redirect()->route('indicators.index');
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении показателя!'
            ];
        }
    }
}
