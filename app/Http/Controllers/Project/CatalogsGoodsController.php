<?php

namespace App\Http\Controllers\Project;

use App\CatalogsGoods;
use App\Models\Project\PricesGoods;
use App\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CatalogsGoodsController extends Controller
{

    // Настройки сконтроллера
    public function __construct()
    {
//        $this->middleware('auth');
        $domain = request()->getHost();

        $site = Site::where('domain', $domain)
            ->with([
                'pages_public',
                'filials'
            ])
            ->first();
//        dd($site);

        $this->site = $site;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($url)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $url
     * @return \Illuminate\Http\Response
     */
    public function show($url)
    {

        // TODO - 30.10.19 - Костыль по парсингу роута для вложенных пунктов каталога, нужно нормальное решение

//        dd(__METHOD__, $url);
        $arr = explode('/', $url);

        $catalog_slug = $arr[0];

        if (count($arr) > 1) {
            $sliced = array_slice($arr, 1);
            $slug = '';
            foreach($sliced as $lol) {
                $slug .= $lol . '/';
            }
            $catalog_item_slug = substr($slug, 0, -1);
        } else {
            $catalog_item_slug = null;
        }
        $site = $this->site;
        $page = $site->pages_public->where('alias', 'catalogs-goods')->first();

        // Получаем полный прайс со всеми доступными разделами
        $catalog_goods = CatalogsGoods::with('items_public')
            ->whereHas('sites', function ($q) use ($site) {
                $q->where('id', $site->id);
            })
            ->where('slug', $catalog_slug)
            ->where(['display' => true])
            ->first();

        if($catalog_item_slug){

            // Получаем разделы прайса ограниченный slug'ом
            $catalog_goods_items = $catalog_goods->items_public->where('slug', $catalog_item_slug);
            $page->title = $catalog_goods_items->first()->title;

            $catalog_goods_items->load('childs');
            // dd($catalog_goods_items);

        } else {

            // Получаем все доступные разделы прайса
            $catalog_goods_items = $catalog_goods->items_public;
            $page->title = 'Все товары';
        }

        // Получаем id всех доступных на сайте разделов прайса,
        // чтобы далее не заниматься повторным перебором при получении товаров
        $catalog_goods_items_ids = $catalog_goods_items->pluck('id');

        $prices_goods = PricesGoods::with([
            'goods_public' => function ($q) {
                $q->with([
                    'article' => function ($q) {
                        $q->with([
                            'raws' => function ($q) {
                                $q->with([
                                    'article.unit',
                                    'metrics'
                                ]);
                            },
                            'attachments' => function ($q) {
                                $q->with([
                                    'article.unit',
                                ]);
                            },
                            'containers' => function ($q) {
                                $q->with([
                                    'article.unit',
                                ]);
                            },
                        ]);
                    }
                ]);
            }
        ])
            ->whereIn('catalogs_goods_item_id', $catalog_goods_items_ids)
            ->has('goods_public')
            ->where([
                'display' => true,
                'archive' => false
            ])
            ->filter(request())
            ->orderBy('sort', 'asc')
            ->paginate(16);

        // Перебор и дописывание агрегаций
        // Нужен способ проще!
        foreach($prices_goods as $price_goods){
            $price_goods->sweets = $price_goods->goods_public->article->raws->filter(function ($value, $key) {
                if(isset($value->metrics->where('name', 'Тип сырья')->first()->pivot->value)){
                    return $value->metrics->where('name', 'Тип сырья')->first()->pivot->value == 1;
                }
            });

            $price_goods->addition = $price_goods->goods_public->article->raws->filter(function ($value, $key) {
                if(isset($value->metrics->where('name', 'Тип сырья')->first()->pivot->value)){
                    return $value->metrics->where('name', 'Тип сырья')->first()->pivot->value == 2;
                }
            });
        }

        return view($site->alias.'.pages.catalogs_goods.index', compact('site','page', 'request', 'catalog_goods_items', 'prices_goods', 'catalog_goods'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
