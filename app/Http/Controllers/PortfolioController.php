<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\PortfolioRequest;
use App\Portfolio;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{

    /**
     * PortfolioController constructor.
     * @param Portfolio $portfolio
     */
    public function __construct(Portfolio $portfolio)
    {
        $this->middleware('auth');
        $this->portfolio = $portfolio;
        $this->class = Portfolio::class;
        $this->model = 'App\Portfolio';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    use Photable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $portfolios = Portfolio::with([
            'author',
            'company',
            'items',
        ])
            // ->withCount('pages')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->template($answer)
            ->booklistFilter($request)
//            ->filter($filters)
            // ->filter($request, 'author_id')
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc')
            ->paginate(30);


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            // 'author',               // Автор записи
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        return view('system.pages.portfolios.index',[
            'portfolios' => $portfolios,
            'pageInfo' => pageInfo($this->entity_alias),
            'filter' => $filter,
            'nested' => 'cases_count'
        ]);
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
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('system.pages.portfolios.create', [
            'portfolio' => Portfolio::make(),
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PortfolioRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(PortfolioRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $portfolio = Portfolio::create($data);

        // Cохраняем / обновляем фото
        $photo_id = $this->getPhotoId($portfolio);
        $portfolio->photo_id = $photo_id;
        $portfolio->save();

        if ($portfolio) {
            return redirect()->route('portfolios.index');
        } else {
            abort(403, 'Ошибка записи');
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

        $portfolio = Portfolio::with([
            'photo'
        ])
        ->moderatorLimit($answer)
            ->find($id);
        // dd($portfolio);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $portfolio);

        return view('system.pages.portfolios.edit', [
            'portfolio' => $portfolio,
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PortfolioRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(PortfolioRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $portfolio = Portfolio::moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $portfolio);

        $data = $request->input();
        $result = $portfolio->update($data);

        // Cохраняем / обновляем фото
        $photo_id = $this->getPhotoId($portfolio);
        $portfolio->photo_id = $photo_id;
        $portfolio->save();

        if ($result) {
            return redirect()->route('portfolios.index');
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

        $portfolio = Portfolio::with([
            'items',
        ])
            ->moderatorLimit($answer)
            ->find($id);

        if ($portfolio) {
            // Подключение политики
            $this->authorize(getmethod(__FUNCTION__), $portfolio);

            $portfolio->delete();

            return redirect()->route('portfolios.index');
        } else {
            abort(403, 'Не найдено');
        }
    }
}
