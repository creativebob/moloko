<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\PromotionRequest;
use App\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{

    protected $entityAlias;
    protected $entityDependence;

    /**
     * PromotionController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'promotions';
        $this->entityDependence = false;
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
        $this->authorize(getmethod(__FUNCTION__), Promotion::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $promotions = Promotion::with([
            'author',
            'company',
            'photo'
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

        $filter = setFilter($this->entityAlias, $request, [
            // 'author',               // Автор записи
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.promotions.index', [
            'promotions' => $promotions,
            'pageInfo' => $pageInfo,
            'filter' => $filter,
            'nested' => 'pages_count'
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
        $this->authorize(getmethod(__FUNCTION__), Promotion::class);

        $promotion = Promotion::make();

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.promotions.create', compact('promotion', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PromotionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(PromotionRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Promotion::class);

        $data = $request->input();
        $promotion = Promotion::create($data);

        $promotion->photo_id = $this->getPhotoId($promotion);

        $names = [
            'tiny',
            'small',
            'medium',
            'large',
            'large_x',
        ];

        foreach ($names as $name) {
            $column = $name . '_id';
            $promotion->$column = $this->savePhoto($request, $promotion, $name);
        }
        $promotion->save();

        $access = session('access.all_rights.index-departments-allow');
        if ($access) {
            $promotion->filials()->sync($request->filials);
        }

        $promotion->goods()->sync($request->goods);
        $promotion->prices_goods()->sync($request->prices_goods);

        return redirect()->route('promotions.index');
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
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $promotion = Promotion::with([
            'tiny:id,name,path',
            'small:id,name,path',
            'medium:id,name,path',
            'large:id,name,path',
            'large_x:id,name,path',
            'site',
//            => function ($q) {
//                $q->with([
//                    'catalogs_goods.items_public.prices_public' => function ($q) {
//                        $q->with([
//                            'goods.article',
//                            'filial'
//                        ]);
//                    },
//                    'filials'
//                ]);
//            }
            'prices_goods.goods.article',
            'goods.article'
        ])
            ->moderatorLimit($answer)
            ->find($id);
//         dd($promotion);

        if (empty($promotion)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $promotion);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.promotions.edit', compact('promotion', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PromotionRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(PromotionRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $promotion = Promotion::with([
            'photo',
            'tiny',
            'small',
            'medium',
            'large',
            'large_x',
        ])
            ->moderatorLimit($answer)
            ->find($id);
//        dd($promotion);

        if (empty($promotion)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $promotion);

        $data = $request->input();
        $data['photo_id'] = $this->getPhotoId($promotion);;

        switch ($data['mode']) {
            case 'photo':
                $names = [
                    'tiny',
                    'small',
                    'medium',
                    'large',
                    'large_x',
                ];

                foreach ($names as $name) {
                    $column = $name . '_id';
                    $data[$column] = $this->savePhoto($request, $promotion, $name);
                }
                break;

            case 'video':

                break;
        }

        $res = $promotion->update($data);

        if (!$res) {
            abort(403, __('errors.update'));
        }

        $filials = session('access.all_rights.index-departments-allow');
        if ($filials) {
            $promotion->filials()->sync($request->filials);
        }

        $promotion->goods()->sync($request->goods);
        $promotion->prices_goods()->sync($request->prices_goods);

        return redirect()->route('promotions.index');
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
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $promotion = Promotion::moderatorLimit($answer)
            ->find($id);

        if (empty($promotion)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $promotion);

        $res = $promotion->delete();

        if (!$res) {
            abort(403, __('errors.destroy'));
        }

        return redirect()->route('promotions.index');
    }
}
