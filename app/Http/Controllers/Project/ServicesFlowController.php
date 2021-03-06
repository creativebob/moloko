<?php

namespace App\Http\Controllers\Project;

use App\Models\System\Flows\ServicesFlow;
use Illuminate\Http\Request;

class ServicesFlowController extends BaseController
{
    /**
     * ServicesFlowController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display the specified resource.
     *
     * @param $slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, $slug)
    {
        // TODO - 30.04.21 - Костыль для переименования services_flows в tours
        $serviceFlow = ServicesFlow::with([
            'process' => function ($q) {
                $q->with([
                    'process' => function ($q) {
                        $q->with([
                            'photo',
                            'album.photos'
                        ]);
                    },
                    'prices.service.process.positions' => function ($q) {
                        $q->where('display', true);
                    },
                    'actualFlows',
                ]);
            },

        ])
            ->whereHas('process', function ($q) use ($slug) {
                $q->whereHas('process', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                });
            })
            ->when($request->has('flow_id'), function ($q) use ($request) {
                $q->where('id', $request->flow_id);
            })
            ->first();
//        dd($serviceFlow);
        if (empty($serviceFlow)) {
            abort(404);
        }

        $site = $this->site;

        $page = $site->pages_public
            ->where('alias', 'services_flow')
            ->first();

        return view($site->alias . '.pages.services_flow.index', compact('site', 'page', 'serviceFlow'));
    }
}
