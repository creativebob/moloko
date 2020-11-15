<?php

namespace App\Http\Controllers;

use App\Http\Controllers\System\Traits\Cancelable;
use App\Http\Controllers\Traits\Receiptable;
use App\Models\System\Documents\Consignment;
use App\Entity;
use App\Http\Requests\System\ConsignmentUpdateRequest;
use Illuminate\Http\Request;

class ConsignmentController extends Controller
{

    protected $entityAlias;
    protected $entityDependence;

    /**
     * ConsignmentController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'consignments';
        $this->entityDependence = true;
    }

    use Receiptable,
        Cancelable;

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
        $this->authorize(getmethod(__FUNCTION__), Consignment::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $consignments = Consignment::with([
            'author',
            'items'
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->filials($answer)
            ->authors($answer)
            ->systemItem($answer)
            // ->whereNull('draft')
            ->booklistFilter($request)
//            ->filter($request, 'supplier_id')
            ->orderBy('moderation', 'desc')
            ->latest('created_at')
            ->paginate(30);
//         dd($consignments);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
            'supplier',             // Поставщики
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.documents.consignments.index', compact('consignments', 'pageInfo', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        // Подключение политики
        $this->authorize(getmethod('store'), Consignment::class);

        if (auth()->user()->company->suppliers->isEmpty()) {
            return back()
                ->withErrors(['msg' => 'Для начала необходимо добавить поставщиков!']);
        }

        $consignment = Consignment::create();

        return redirect()->route('consignments.edit', $consignment->id);
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

        $consignment = Consignment::with([
            'items' => function ($q) {
                $q->with([
                    'cmv' => function ($q) {
                        $q->with([
                            'article.unit'
                        ]);
                    },
                    'entity:id,name,alias',
                    'manufacturer.company',
                    'currency'
                ]);
            },
        ])
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($consignment);

        if (empty($consignment)) {
            abort(403, __('errors.not_found'));
        }

        $this->authorize(getmethod(__FUNCTION__), $consignment);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.documents.consignments.edit', compact('consignment', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ConsignmentUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(ConsignmentUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $consignment = Consignment::moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($consignment);

        if (empty($consignment)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $consignment);

        $data = $request->input();
        $consignment->update($data);

        return redirect()->route('consignments.index');
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

        // ГЛАВНЫЙ ЗАПРОС:
        $consignment = Consignment::with('items')
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($consignment);

        if (empty($consignment)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $consignment);

        $consignment->delete();

        if ($consignment) {
            return redirect()->route('consignments.index');
        } else {
            abort(403, __('errors.destroy'));
        }
    }

    public function categories(Request $request)
    {
        $entity = Entity::find($request->entity_id);

        $entityAlias = $entity->alias;
        $alias = $entityAlias . '_categories';

        $entity_categories = Entity::whereAlias($alias)->first(['model']);
        $model = $entity_categories->model;

        // Получаем из сессии необходимые данные
        $answer = operator_right($entity_categories->alias, false, 'index');

        $categories = $model::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->with([
                $entityAlias => function ($q) {
                    $q->with([
                        'article.unit'
                    ])
                        ->where('archive', false)
                        ->whereHas('article', function ($q) {
                            $q->where('draft', false)//                                ->whereNotNull('manufacturer_id')
                            ;
                        });
                }
            ])
            ->get([
                'id',
                'name',
                'parent_id',
            ]);
//		dd($categories);

        $categories_tree = buildTreeArrayWithEntity($categories, $entity);
//        dd($categories_tree);

        $items = [];
        foreach ($categories as $category) {
            $category->entity_id = $entity->id;

            if (isset($category->$entityAlias)) {
                foreach ($category->$entityAlias as $item) {
                    $item->category_id = $category->id;
                    $item->entity_id = $entity->id;
                    $items[] = $item;
                }
            }

            if (isset($category->childCategories)) {
                if (isset($category->$entityAlias)) {
                    foreach ($category->childCategories as $childCategory) {
                        foreach ($childCategory->$entityAlias as $item) {
                            $item->category_id = $category->id;
                            $item->entity_id = $entity->id;
                            $items[] = $item;
                        }
                    }
                }
            }
        }
//        dd($items);

        $data = [
            'categories' => $categories_tree,
            'items' => $items
        ];
//        dd($data);

        return response()->json($data);
    }

    /**
     * Приходование
     *
     * @param ConsignmentUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function conducting(ConsignmentUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('update'));

        // ГЛАВНЫЙ ЗАПРОС:
        $consignment = Consignment::moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($consignment);

        if (empty($consignment)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod('update'), $consignment);

        if (empty($consignment->conducted_at)) {
            $data = $request->input();
            $consignment->update($data);

            $consignment->items()->update([
                'stock_id' => $consignment->stock_id
            ]);

            $consignment->load([
                'items' => function ($q) {
                    $q->with([
                        'cmv' => function ($q) {
                            $q->with([
                                'article',
                                'stocks',
                                'cost'
                            ]);
                        },
                        'entity',
                    ]);
                },
            ]);
//		dd($consignment);

            if ($consignment->items->isNotEmpty()) {

                $draft = $consignment->items->firstWhere('cmv.article.draft', 1);
                if ($draft) {
                    return back()
                        ->withErrors(['msg' => 'Накладная содержит черновые позиции, оприходование невозможно!']);
                }

                $archive = $consignment->items->firstWhere('cmv.archive', 1);
                if ($archive) {
                    return back()
                        ->withErrors(['msg' => 'Накладная содержит архивные позиции, оприходование невозможно!']);
                }

                set_time_limit(0);

                logs('documents')
                    ->info("========================================== НАЧАЛО ОПРИХОДОВАНИЯ ТОВАРНОЙ НАКЛАДНОЙ ==============================================");

                foreach ($consignment->items as $item) {
                    $this->receipt($item);
                }

                $consignment->update([
                    'conducted_at' => now(),
                ]);

                logs('documents')
                    ->info("Оприходована накладная c id: {$consignment->id}");
                logs('documents')
                    ->info('======================================== КОНЕЦ ОПРИХОДОВАНИЯ ТОВАРНОЙ НАКЛАДНОЙ ==============================================

				');

            } else {
                abort(403, __('errors.not_items'));
            }
        }

        return redirect()->route('consignments.index');
    }

    /**
     * Отмена приходования
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function cancel($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('update'));

        // ГЛАВНЫЙ ЗАПРОС:
        $consignment = Consignment::moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);

        // Подключение политики
        $this->authorize(getmethod('update'), $consignment);

        if (isset($consignment->conducted_at)) {
            $consignment->load([
                'items' => function ($q) {
                    $q->with([
                        'cmv' => function ($q) {
                            $q->with([
                                'article',
                                'cost'
                            ]);
                        },
                        'receipt.storage',
                        'document'
                    ]);
                },
                'receipts.storage'
            ]);
//		dd($consignment);

            if ($consignment->items->isNotEmpty()) {

                foreach ($consignment->receipts as $receipt) {

                    if ($receipt->storage->free < $receipt->count) {
//                        return back()
//                            ->withErrors(['msg' => 'Накладная содержит позиции, в которых на остатках нет нужного количества для возврата!']);
                    }
                }

                logs('documents')
                    ->info('========================================== НАЧАЛО ОТМЕНЫ ОПРИХОДОВАНИЯ ТОВАРНОЙ НАКЛАДНОЙ ==============================================');

                foreach ($consignment->items as $item) {
//                    dd($item);
                        $this->cancelReceipt($item);
                }

                $consignment->update([
                    'conducted_at' => null,
                ]);

                logs('documents')
                    ->info('Отменена накладная c id: ' . $consignment->id);
                logs('documents')
                    ->info('========================================== КОНЕЦ ОТМЕНЫ ОПРИХОДОВАНИЯ ТОВАРНОЙ НАКЛАДНОЙ ==============================================

				');

                return redirect()->route('consignments.index');
            } else {
                abort(403, 'Накладная пуста');
            }
        }
    }

    public function reposting(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod('index'), Consignment::class);

        set_time_limit(60 * 10);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('index'));

        // ГЛАВНЫЙ ЗАПРОС:
        $consignments = Consignment::with([
            'items' => function ($q) {
                $q->with([
                    'cmv' => function ($q) {
                        $q->with([
                            'article',
                            'cost',
                            'stocks'
                        ]);
                    },
                    'entity'
                ]);
            },
        ])
            ->companiesLimit($answer)
            ->whereNotNull('conducted_at')
            ->chunk(5, function ($consignments) {
                foreach ($consignments as $consignment) {
                    if ($consignment->is_posted == 1) {
                        if ($consignment->items->isNotEmpty()) {

                            logs('documents')
                                ->info('========================================== НАЧАЛО ОПРИХОДОВАНИЯ ТОВАРНОЙ НАКЛАДНОЙ, ID: ' . $consignment->id . ' ==============================================');

                            foreach ($consignment->items as $item) {
                                $item->cmv->load([
                                    'article',
                                    'cost',
                                    'stocks'
                                ]);
                                $this->receipt($item);
                            }

                            $consignment->update([
                                'is_posted' => true,
                            ]);

                            logs('documents')
                                ->info('Оприходована накладная c id: ' . $consignment->id);
                            logs('documents')
                                ->info('========================================== КОНЕЦ ОПРИХОДОВАНИЯ ТОВАРНОЙ НАКЛАДНОЙ ==============================================

				            ');
                        }
                    }
                }
            });

        return redirect()->route('consignments.index');
    }

}
