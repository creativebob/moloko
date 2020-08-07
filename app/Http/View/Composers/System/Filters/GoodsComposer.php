<?php

namespace App\Http\View\Composers\System\Filters;

use App\Goods;
use Illuminate\View\View;

class GoodsComposer
{

    /**
     * Товары
     */
    protected $goods;

    /**
     * GoodsComposer constructor.
     */
	public function __construct()
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('goods', false, 'index');

        $this->goods = Goods::with([
            'article' => function ($q) {
                $q->where([
                    'draft' => false,
                ])
                ->select([
                    'id',
                    'name',
                    'draft'
                ]);
            },
        ])
        ->whereHas('article', function ($q) {
            $q->where([
                'draft' => false,
            ]);
        })
        ->moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        ->get([
            'id',
            'article_id',
            'archive'
        ]);
//         dd($goods);

    }

    /**
     * Отдаем товары на шаблон
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {
        return $view->with('goods', $this->goods);
    }
}
