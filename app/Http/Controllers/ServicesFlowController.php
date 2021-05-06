<?php

namespace App\Http\Controllers;

use App\Http\Controllers\System\Traits\Timestampable;
use App\Http\Requests\System\FlowRequest;
use App\Models\System\Flows\EventsFlow;
use App\Models\System\Flows\ServicesFlow;
use App\Process;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ServicesFlowController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;
    /**
     * @var string
     */
    private $class;

    /**
     * EventsFlowController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'services_flows';
        $this->entityDependence = true;
        $this->class = ServicesFlow::class;
    }

    use Timestampable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));
        // dd($answer);

        $flows = ServicesFlow::with([
            'process' => function ($q) {
                $q->with([
                    'process.unit',
                    'category'
                ]);
            },
            'company',
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->filials($answer)
            ->systemItem($answer)
            ->filter()
            ->paginate(30);
//         dd($flows);

        $class = $this->class;

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.common.flows.index', compact('flows', 'pageInfo', 'class'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('system.common.flows.create', [
            'flow' => ServicesFlow::make(),
            'pageInfo' => pageInfo($this->entityAlias),
            'class' => $this->class,
            'processAlias' => 'services'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FlowRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(FlowRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), ServicesFlow::class);

        $data = $request->input();
        $data['start_at'] = $this->getTimestamp('start', true);
        $data['finish_at'] = $this->getTimestamp('finish', true);

        $flow = ServicesFlow::create($data);

        if ($flow) {
            return redirect()->route('services_flows.index');
        } else {
            abort(403, __('errors.store'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $flow = ServicesFlow::with([
            'process.process',
        ])
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($flow);
        if (empty($flow)) {
            abort(403, __('errors.not_found'));
        }

        $this->authorize(getmethod(__FUNCTION__), $flow);

        return view('system.common.flows.edit', [
            'flow' => $flow,
            'pageInfo' => pageInfo($this->entityAlias),
            'class' => $this->class,
            'processAlias' => 'services'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FlowRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(FlowRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $flow = ServicesFlow::moderatorLimit($answer)
            ->find($id);
        if (empty($flow)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $flow);

        $data = $request->input();
        $data['start_at'] = $this->getTimestamp('start', true);
        $data['finish_at'] = $this->getTimestamp('finish', true);

        $res = $flow->update($data);

        if ($res) {
            return redirect()->route('services_flows.index');
        } else {
            abort(403, __('errors.update'));
        }
    }
}
