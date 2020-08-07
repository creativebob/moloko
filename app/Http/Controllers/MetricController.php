<?php

namespace App\Http\Controllers;

use App\Http\Requests\System\MetricStoreRequest;
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
        $this->type = 'page';
    }

    public function index()
    {
        //
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
     * Отображение указанного ресурса.
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
