<?php

namespace App\Http\Controllers\Project;

use App\CatalogsService;
use App\Http\Controllers\Project\Traits\Commonable;
use App\Models\Project\PricesService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CatalogsServiceController extends BaseController
{
    /**
     * CatalogsServiceController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
    public function show(Request $request, $url)
    {

         // TODO - 30.10.19 - Костыль по парсингу роута для вложенных пунктов каталога, нужно нормальное решение

//        dd(__METHOD__, $url);
        $arr = explode('/', $url);

        $catalog_slug = $arr[0];
        $main_slug = $arr[1] ?? null;
        $sub_menu_ids = null;

        if (count($arr) > 1) {
            $sliced = array_slice($arr, 1);
            $slug = '';
            foreach($sliced as $slice) {
                $slug .= $slice . '/';
            }

            $catalog_item_slug = substr($slug, 0, -1);

        } else {

            $catalog_item_slug = null;
        }

        $site = $this->site;

        $page = $site->pages_public->where('alias', 'catalogs-services')->first();

        // Получаем полный прайс со всеми доступными разделами
        $catalog_services = CatalogsService::with([
            'items_public' => function ($q) {
                $q->with([
                    'display_mode',
                    'filters.values'
                ]);
            },
        ])
            ->whereHas('filials', function ($q) use ($site) {
                $q->where('id', $site->filial->id);
            })
            ->where('slug', $catalog_slug)
            ->where(['display' => true])
            ->first();
//        dd($catalog_services);

        if (empty($catalog_services)) {
            abort(404);
        }

            // Проверим, а доступен ли каталог товаров. Если нет, то кидаем ошибку
            if (! $catalog_services) {
                abort(403, 'Доступ к прайсу товаров компании ограничен. Согласен, это довольно странно...');
            }
            if($catalog_item_slug){

            // Получаем разделы прайса ограниченный slug'ом
            $catalog_services_items = $catalog_services->items_public->where('slug', $catalog_item_slug)->first();
            // dd($catalog_services_items);

            if($catalog_services_items){

                $page->title = $catalog_services_items->title;
                $page->description = $catalog_services_items->seo_description;

                $catalog_services_items->load('childs');
                //dd($catalog_services_items);

                $sub_menu = $catalog_services->items_public->where('slug', getFirstSlug($catalog_item_slug))->first();
                $sub_menu->load('childs');
                $sub_menu_ids = $sub_menu->childs->pluck('id');

                // Получаем id всех доступных на сайте разделов прайса,
                // чтобы далее не заниматься повторным перебором при получении товаров
                $catalog_services_items_ids = $catalog_services->items_public->where('slug', $catalog_item_slug)->pluck('id');


            } else {

                abort(404, 'Страница не найдена');
            }


        } else {

            // Получаем все доступные разделы прайса
            $catalog_services_items = $catalog_services->items_public;
            $page->title = 'Все товары';
            $catalog_services_items_ids = $catalog_services->items_public->pluck('id');
        }


        if($sub_menu_ids){
            if(getFirstSlug($catalog_item_slug) == $catalog_item_slug){
                $catalog_services_items_ids = $catalog_services_items_ids->merge($sub_menu_ids);
            }
        }

        $prices_services = PricesService::with([
            'service_public' => function ($q) {
                $q->with([
                    'process' => function ($q) {
                        $q->with([
                            'photo',
                        ]);
                    },
                    'metrics',
                ]);
            },
            'currency',
            'catalogs_item.directive_category:id,alias'
        ])
            ->whereIn('catalogs_services_item_id', $catalog_services_items_ids)
            ->has('service_public')
            ->where([
                'display' => true,
                'archive' => false,
                'filial_id' => $this->site->filial->id
            ])
//            ->filter(request())
            ->orderBy('sort', 'asc')
            ->paginate(16);

        return view($site->alias.'.pages.catalogs_services.index', compact('site',  'page', 'request', 'catalog_services_items', 'prices_services', 'catalog_services', 'main_slug', 'sub_menu'));
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
