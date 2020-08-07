<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Project\Traits\Commonable;
use App\Models\Project\Estimate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EstimateController extends Controller
{

    use Commonable;

    /**
     * Отображение списка ресурсов.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $estimates = Estimate::with([
            'goods_items' => function ($q) {
                $q->with([
                    'goods.article.photo',
                    'price_goods' => function ($q) {
                        $q->with([
                            'currency',
                            'catalogs_item.directive_category:id,alias'
                        ]);
                    }
                ]);
            },
            'lead'
        ])
            ->whereHas('lead', function ($q) {
                $q->where('user_id', auth()->user()->id);
            })
            ->where('is_dismissed', false)
            ->orderBy('id', 'desc')
            ->get();
//        dd($estimates);

        $site = $this->site;

        $page = $site->pages_public
            ->where('alias', 'estimates')
            ->first();

        return view($site->alias.'.pages.estimates.index', compact('site',  'page', 'estimates'));
    }

    /**
     * Показать форму для создания нового ресурса.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Сохранение созданного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Отображение указанного ресурса.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

	    $estimate = Estimate::with('lead')
		    ->whereHas('lead', function($q){
			    $q->where('user_id', Auth::user()->id);
		    })
		    ->findOrFail($id);

        $site = $this->site;

        $page = $site->pages_public
            ->where('alias', 'estimates-items')
            ->first();

        return view($site->alias.'.pages.estimates_items.index', compact('site',  'page', 'estimate'));
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
