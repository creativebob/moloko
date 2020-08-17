<?php

namespace App\Http\Controllers;

use App\BusinessCase;
use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\BusinessCaseRequest;
use App\Portfolio;

class BusinessCaseController extends Controller
{

    private $portfolio;
    private $business_case;
    private $entity_alias;
    private $entity_dependence;

    /**
     * BusinessCaseController constructor.
     *
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
     * Отображение списка ресурсов.
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
            'pageInfo' => pageInfo($this->entity_alias),
            'portfolio' => $this->portfolio
        ]);
    }

    /**
     * Показать форму для создания нового ресурса.
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
            'pageInfo' => pageInfo($this->entity_alias),
            'portfolio' => $this->portfolio
        ]);
    }

    /**
     * Сохранение созданного ресурса в хранилище.
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
     * Отображение указанного ресурса.
     *
     * @param $id
     */
    public function show($id)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
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
            'pageInfo' => pageInfo($this->entity_alias),
            'portfolio' => $this->portfolio
        ]);
    }

    /**
     * Обновление указанного ресурса в хранилище.
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
     * Удаление указанного ресурса из хранилища.
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
