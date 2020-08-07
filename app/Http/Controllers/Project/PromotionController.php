<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Project\Traits\Commonable;
use App\Promotion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{

    use Commonable;

    /**
     * Отображение списка ресурсов.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $site = $this->site;
        $promotions = Promotion::with('photo')
            ->whereHas('filials', function ($q) use ($site) {
                $q->where('id', $site->filial->id);
            })
            ->where('display', true)
            ->where('begin_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();
//        dd($promotions);

        $page = $site->pages_public
            ->where('alias', 'promotions')
            ->first();

        return view($site->alias.'.pages.promotions.index', compact('site',  'page', 'promotions'));
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

	    $promotion = Promotion::with('photo')
		    ->findOrFail($id);

        $site = $this->site;

        $page = $site->pages_public
            ->where('alias', 'promotion')
            ->first();

        return view($site->alias.'.pages.promotion.index', compact('site',  'page', 'promotion'));
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
