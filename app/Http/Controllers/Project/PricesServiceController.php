<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Project\Traits\Commonable;
use App\Models\Project\PricesService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PricesServiceController extends Controller
{

    use Commonable;

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
    public function show($id)
    {

        $site = $this->site;

        $page = $site->pages_public->where('alias', 'prices-services')->first();

        $price_service = PricesService::with([
            'service_public.article.raws',
            'currency'
        ])
            ->where([
                'display' => true
            ])
            ->find($id);

        // dd($price_service->service_public->article->containers);

        $page->title = $price_service->service_public->process->name;

        return view($site->alias.'.pages.prices_services.index', compact('site',  'page', 'price_service'));
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

    public function search($search)
    {

        $items = PricesService::with([
            'service_public' => function ($q) {
                $q->with([
                    'process.photo',
                    'metrics.values'
                ]);

            },
            'currency',
            'catalogs_item.directive_category:id,alias',
            'catalogs_item.parent'
        ])
            ->where([
                'archive' => false,
                'company_id' => $this->site->company_id,
                'filial_id' => $this->site->filial->id,
                'display' => true,
            ])
            ->whereHas('service_public', function($q) use ($search) {
                $q->whereHas('process', function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                        ->where([
                            'draft' => false,
                            'display' => true,
                        ]);
                })
                ->where([
                    'archive' => false,
                    'display' => true,
                ]);
            })
            ->get();

//        dd($items);

        return response()->json($items);
    }
}
