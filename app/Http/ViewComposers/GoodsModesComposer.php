<?php

namespace App\Http\ViewComposers;

use App\GoodsMode;

use Illuminate\View\View;

class GoodsModesComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer = operator_right('raws_modes', false, 'index');

        // moderatorLimit($answer)
        // ->systemItem($answer)
        // ->

        $goods_modes = GoodsMode::orderBy('sort', 'asc')
        ->get(['id', 'name']);

        return $view->with('goods_modes', $goods_modes);
    }

}