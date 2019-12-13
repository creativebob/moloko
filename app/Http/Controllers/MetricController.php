<?php

namespace App\Http\Controllers;

use App\Http\Requests\MetricStoreRequest;
use App\Metric;
use Illuminate\Http\Request;

class MetricController extends Controller
{
    // Настройки сконтроллера
    public function __construct(Metric $metric)
    {
//        $this->middleware('auth');
        $this->metric = $metric;
        $this->class = Metric::class;
        $this->model = 'App\Metric';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'edit';
    }

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
    public function store(MetricStoreRequest $request)
    {
        $data = $request->input();
        $metric = Metric::create($data);

        if ($metric) {

            $metric->load('values');

            return response()->json($metric);

//            return view('products.common.metrics.metric', compact('metric'));

        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при добавлении свойства!'
            ];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $metric = Metric::with('values')->findOrFail($id);
        // dd($metric);
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



    // --------------------------------------------- Ajax -------------------------------------------------

    public function ajax_get_metric(Request $request)
    {
        $metric = Metric::findOrFail($request->id);

        if ($metric) {
            return view('products.common.metrics.metric', [
                'metric' => $metric,
            ]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при добавлении свойства!'
            ];
        }
    }

    public function ajax_get_metric_value(Request $request)
    {
        // Переадресовываем на получение метрики
        return view('products.common.metrics.value', ['value' => $request->value]);
    }

}
