<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\System\PortfoliosItemStoreRequest;
use App\Http\Requests\System\PortfoliosItemUpdateRequest;
use App\Portfolio;
use App\PortfoliosItem;
use Illuminate\Http\Request;

class PortfoliosItemController extends Controller
{

    /**
     * CatalogsGoodsItemController constructor.
     * @param PortfoliosItem $portfolios_item
     */
    public function __construct(PortfoliosItem $portfolios_item)
    {
        $this->middleware('auth');
        $this->portfolios_item = $portfolios_item;
        $this->class = PortfoliosItem::class;
        $this->model = 'App\PortfoliosItem';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'page';
    }

    use Photable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $portfolio_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $portfolio_id)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $columns = [
            'id',
            'portfolio_id',
            'name',
            'parent_id',
            'company_id',
            'sort',
            'display',
            'system',
            'moderation',
            'author_id'
        ];

        $portfolios_items = PortfoliosItem::with('childs')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->where('portfolio_id', $portfolio_id)
            ->orderBy('sort')
            ->get();
        // dd($portfolios_items);

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('system.common.categories.index.categories_list',
                [
                    'items' => $portfolios_items,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $portfolios_items->count(),
                    'id' => $request->id,
                    'nested' => 'childs_count',
                ]
            );
        }

        $portfolio = Portfolio::find($portfolio_id);

        // Стандартный шаблон для отображения

        // Если передан аттрибут seo, то отдаем на другой шаблон
        if($request->seo == 'true'){$view_name = 'portfolios_items.seo';}

        // Отдаем на шаблон
        return view('system.pages.portfolios_items.index', [
            'portfolios_items' => $portfolios_items,
            'pageInfo' => pageInfo($this->entity_alias),
            'id' => $request->id,
            'portfolio_id' => $portfolio_id,
            'portfolio' => $portfolio,
            'user' => auth()->user(),
            'type' => $this->type,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @param $portfolio_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Request $request, $portfolio_id)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('system.common.categories.create.modal.create', [
            'item' => Portfolio::make(),
            'entity' => $this->entity_alias,
            'title' => 'Добавление пункта портфолио',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id,
            'portfolio_id' => $portfolio_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PortfoliosItemStoreRequest $request
     * @param $portfolio_id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(PortfoliosItemStoreRequest $request, $portfolio_id)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->validated();
        $data['portfolio_id'] = $portfolio_id;
        $portfolios_item = PortfoliosItem::create($data);

        if ($portfolios_item) {

            // Переадресовываем на index
            return redirect()->route('portfolios_items.index', ['portfolio_id' => $portfolio_id, 'id' => $portfolios_item->id]);

        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи'
            ];
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

        $portfolios_item = PortfoliosItem::moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $portfolios_item);

        $portfolio = Portfolio::find($portfolio_id);

        return view('system.pages.portfolios_items.edit', [
            'portfolios_item' => $portfolios_item,
            'pageInfo' => pageInfo($this->entity_alias),
            'portfolio' => $portfolio
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PortfoliosItemUpdateRequest $request
     * @param $portfolio_id
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(PortfoliosItemUpdateRequest $request, $portfolio_id, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $portfolios_item = PortfoliosItem::moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $portfolios_item);

        $data = $request->validated();
        $data['photo_id'] = $this->getPhotoId($portfolios_item);
        $result = $portfolios_item->update($data);

        if ($result) {

            // Переадресовываем на index
            return redirect()->route('portfolios_items.index', ['portfolio_id' => $portfolio_id, 'id' => $portfolios_item->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлени'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $portfolio_id
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, $portfolio_id, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $portfolios_item = PortfoliosItem::moderatorLimit($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $portfolios_item);

        $parent_id = $portfolios_item->parent_id;

        $portfolios_item->delete();

        if ($portfolios_item) {

            // Переадресовываем на index
            return redirect()->route('portfolios_items.index', ['portfolio_id' => $portfolio_id, 'id' => $parent_id]);

        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении!'
            ];
        }

    }

    // ------------------------------------------------ Ajax -------------------------------------------------

    public function get_prices(Request $request)
    {

        $filial_id = $request->user()->filial_id;

        $portfolios_item = PortfoliosItem::with([
            'prices_goods' => function ($q) use ($filial_id) {
                $q->where('archive', false)
                    ->where('filial_id', $filial_id)
                    ->whereHas('goods', function ($q) {
                        $q->where('archive', false)
                            ->whereHas('article', function ($q) {
                                $q->where('draft', false);
                            });
                    });
            }
        ])
            ->find($request->id);
//         dd($portfolios_item);

        return view('leads.catalogs.prices_goods', compact('portfolios_item'));
    }

    public function ajax_get(Request $request, $portfolio_id)
    {
        return view('products.articles.goods.prices.catalogs_items', compact('portfolio_id'));
    }
}
