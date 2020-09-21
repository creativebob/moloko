<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\OutcomeRequest;
use App\Outcome;
use Illuminate\Http\Request;

class OutcomeController extends Controller
{

    /**
     * @var Outcome
     */
    protected $outcome;
    protected $class;
    protected $model;
    protected $entity_alias;
    protected $entity_dependence;

    /**
     * OutcomeController constructor.
     * @param Outcome $outcome
     */
    public function __construct(Outcome $outcome)
    {
        $this->middleware('auth');
        $this->outcome = $outcome;
        $this->class = Outcome::class;
        $this->model = 'App\Outcome';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    use Photable;

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

        // -----------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------------

        $outcomes = Outcome::with([
            'author',
            'company',
            'category'
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->systemItem($answer)
            ->booklistFilter($request)
            ->filter($request, 'author')
            ->filter($request, 'company')
            ->orderBy('moderation', 'desc')
            ->orderBy('sort')
            ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
            'company',              // Компания
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('system.pages.outcomes.index', compact('outcomes', 'pageInfo', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        // Подключение политики
        $this->authorize(__FUNCTION__, $this->class);

        $outcome = Outcome::make();

        $pageInfo = pageInfo($this->entity_alias);

        return view('system.pages.outcomes.create', compact('outcome', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OutcomeRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(OutcomeRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->validated();
        $outcome = Outcome::create($data);

        $outcome->photo_id = $this->getPhotoId($outcome);
        $outcome->save();

        if ($outcome) {
            return redirect()->route('outcomes.index');

        } else {
            abort(403, 'Ошибка записи');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $id
     */
    public function show(Request $request, $id)
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

        $outcome = Outcome::moderatorLimit($answer)
            ->find($id);
        // dd($outcome);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $outcome);

        $outcome->load([
            'client.clientable'
        ]);

        return view('system.pages.outcomes.edit', [
            'outcome' => $outcome,
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OutcomeRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(OutcomeRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $outcome = Outcome::moderatorLimit($answer)
            ->find($id);
        // dd($outcome);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $outcome);

        $data = $request->validated();
        $data['photo_id'] = $this->getPhotoId($outcome);
        $outcome->update($data);

        if ($outcome) {
            return redirect()->route('outcomes.index');
        } else {
            abort(403, 'Ошибка обновления');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $outcome = Outcome::moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $outcome);

        $outcome->delete();

        if ($outcome) {
            return redirect()->route('outcomes.index');
        } else {
            abort(403, 'Ошибка при удалении');
        }
    }
}
