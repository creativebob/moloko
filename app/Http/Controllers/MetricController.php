<?php

namespace App\Http\Controllers;

// Модели
use App\Metric;
use App\MetricValue;

use DB;

use Illuminate\Http\Request;

class MetricController extends Controller
{
    // Сущность над которой производит операции контроллер
    protected $entity_name = 'metrics';
    protected $entity_dependence = false;

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
        // echo $request->values;
        // $values = '';
        // foreach ($request->values as $value) {
        //     $values .= $value . 'n';
        // }
        // echo $values;

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        $metric = new Metric;
        $metric->company_id = $company_id;
        $metric->property_id = $request->property_id;
        $metric->name = $request->name;
        $metric->description = $request->description;
        $metric->author_id = $user_id;

        if ($request->type == 'numeric' || $request->type == 'percent') {
            $metric->min = $request->min;
            $metric->max = $request->max;
            $metric->unit_id = $request->unit_id;
            $metric->save();
        }

        if ($request->type == 'list') {
            $metric->list_type = $request->list_type;
            $metric->save();
            $values = [];

            foreach ($request->values as $value) {

                $values[] = [
                    'metric_id' => $metric->id,
                    'value' => $value,
                    'author_id' => $user_id,
                    'company_id' => $company_id,
                ];     
            } 

            $metric_values = MetricValue::insert($values);
        }

        
        if ($metric) {

            // echo $metric;
            // Переадресовываем на получение метрики
            return redirect()->action('MetricController@ajax_add_relation', ['id' => $metric->id, 'entity_id' => $request->entity_id, 'entity' => $request->entity]);
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
        $metric = Metric::with('values')->findOrFail($id);
        dd($metric);
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

    public function ajax_store(Request $request)
    {
        // echo $request->values;
        // $values = '';
        // foreach ($request->values as $value) {
        //     $values .= $value . 'n';
        // }
        // echo $values;

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        $metric = new Metric;
        $metric->company_id = $company_id;
        $metric->property_id = $request->property_id;
        $metric->name = $request->name;
        $metric->description = $request->description;
        $metric->author_id = $user_id;

        if ($request->type == 'numeric' || $request->type == 'percent') {
            $metric->min = $request->min;
            $metric->max = $request->max;
            $metric->unit_id = $request->unit_id;
            $metric->save();
        }

        if ($request->type == 'list') {
            $metric->list_type = $request->list_type;
            $metric->save();
            $values = [];

            foreach ($request->values as $value) {

                $values[] = [
                    'metric_id' => $metric->id,
                    'value' => $value,
                    'author_id' => $user_id,
                    'company_id' => $company_id,
                ];     
            } 

            $metric_values = MetricValue::insert($values);
        }

        
        if ($metric) {

            // echo $metric;
            // Переадресовываем на получение метрики
            return redirect()->action('MetricController@ajax_add_relation', ['id' => $metric->id, 'entity_id' => $request->product_id, 'entity' => $request->entity]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при добавлении свойства!'
            ];
        }
    }
    
    public function ajax_add_relation(Request $request)
    {

        $metric = Metric::with('unit')->findOrFail($request->id);

        $entity = $request->entity;
        // Связываем сущность с метрикой
        $metric->$entity()->attach($request->entity_id);

        // switch ($request->entity) {
        //     case 'goods_categories':
        //     $metric->goods_categories()->toggle([$request->entity_id => ['entity' => $request->entity]]);
        //     break;
        // }

        return view($request->entity.'.metrics.metric', ['metric' => $metric]);
    }

    public function ajax_delete_relation(Request $request)
    {

        $metric = Metric::findOrFail($request->id);

        $entity = $request->entity;
        // Отвязываем сущность от метрики
        $res = $metric->$entity()->detach($request->entity_id);

        // switch ($request->entity) {
        //     case 'goods_categories':
        //     $res = $metric->goods_categories()->toggle([$request->entity_id => ['entity' => $request->entity]]);
        //     break;
        // }
       
        if ($res) {
            $result = [
                'error_status' => 0,
            ];
        } else {
            $result = [
                'error_message' => 'Не удалось удалить метрику!',
                'error_status' => 1,
            ];
        }
        
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function add_metric_value(Request $request)
    {   
        // Переадресовываем на получение метрики
        return view($request->entity.'.metrics.value', ['value' => $request->value]);
    }

}
