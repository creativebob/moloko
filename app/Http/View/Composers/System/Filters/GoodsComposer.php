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




    }

    /**
     * Отдаем товары на шаблон
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {

        $archive = isset($view->archive);

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
            ->when(!$archive, function ($q) {
                $q->where('archive', false);
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

        return $view->with('goods', $this->goods);
    }
}
