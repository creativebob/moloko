<?php

namespace App\Http\Controllers;

use App\BusinessCase;
use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\BusinessCaseRequest;
use App\Portfolio;

class BusinessCaseController extends Controller
{

    private $portfolio;
    private $business_case;
    private $entity_alias;
    private $entity_dependence;

    /**
     * BusinessCaseController constructor.
     * @param BusinessCase $business_case
     */
    public function __construct(BusinessCase $business_case)
    {
        $this->middleware('auth');

        $this->portfolio_id = request()->portfolio_id;
        $this->portfolio = Portfolio::findOrFail(request()->portfolio_id);
        $this->business_case = $business_case;
        $this->class = BusinessCase::class;
        $this->model = 'App\BusinessCase';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    use Photable;

    /**
     * Display a listing of the resource.
     *
     * @param $portfolio_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence,  getmethod(__FUNCTION__));

        $business_cases = BusinessCase::with([
            'author',
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->where('portfolio_id', $this->portfolio_id)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($navigations);

        return view('system.pages.business_cases.index', [
            'business_cases' => $business_cases,
            'page_info' => pageInfo($this->entity_alias),
            'portfolio' => $this->portfolio
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $portfolio_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('system.pages.business_cases.create', [
            'business_case' => BusinessCase::make(),
            'page_info' => pageInfo($this->entity_alias),
            'portfolio' => $this->portfolio
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BusinessCaseRequest $request
     * @param $portfolio_id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(BusinessCaseRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $data['portfolio_id'] = $this->portfolio->id;
        $business_case = BusinessCase::create($data);

        // Cохраняем / обновляем фото
        $photo_id = $this->getPhotoId($request, $business_case);
        $business_case->photo_id = $photo_id;
        $business_case->save();

        if ($business_case) {

            // Переадресовываем на index
            return redirect()->route('business_cases.index', $this->portfolio->id);
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
     * @param $portfolio_id
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($portfolio_id, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $business_case = BusinessCase::moderatorLimit($answer)
            ->findOrFail($id);
        // dd($business_case);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $business_case);

        return view('system.pages.business_cases.edit', [
            'business_case' => $business_case,
            'page_info' => pageInfo($this->entity_alias),
            'portfolio' => $this->portfolio
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BusinessCaseRequest $request
     * @param $portfolio_id
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(BusinessCaseRequest $request, $portfolio_id, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $business_case = BusinessCase::moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $business_case);

        $data = $request->input();
        $business_case->update($data);

        // Cохраняем / обновляем фото
        $photo_id = $this->getPhotoId($request, $business_case);
        $business_case->photo_id = $photo_id;
        $business_case->save();

        if ($business_case) {

            // Переадресовываем на index
            return redirect()->route('business_cases.index', $portfolio_id);
        } else {
            abort(403, 'Ошибка при обновлении');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $portfolio_id
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($portfolio_id, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $business_case = BusinessCase::moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $business_case);

        $business_case->delete();

        if ($business_case) {

            // Переадресовываем на index
            return redirect()->route('business_cases.index', ['portfolio_id' => $portfolio_id]);
        } else {
            abort(403, 'Ошибка при удалении');
        }
    }
}
