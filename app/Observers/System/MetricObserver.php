<?php

namespace App\Observers\System;

use App\Metric;
use App\MetricValue;
use App\Observers\System\Traits\Commonable;

class MetricObserver
{

    use Commonable;

    public function creating(Metric $metric)
    {
        $this->store($metric);
        $this->setAlias($metric);

        $request = request();

        if (
            $request->type == 'numeric' ||
            $request->type == 'percent'
        ) {
            $metric->decimal_place = $request->decimal_place;
            $metric->min = round($request->min , $request->decimal_place, PHP_ROUND_HALF_UP);
            $metric->max = round($request->max , $request->decimal_place, PHP_ROUND_HALF_UP);
            $metric->unit_id = $request->unit_id;
        }

        if ($request->type == 'list') {
            $metric->list_type = $request->list_type;
        }
    }

    public function created(Metric $metric)
    {
        $request = request();

        if ($request->type == 'list') {

            $user = $request->user();

            $values = [];
            foreach ($request->metric_values as $value) {

                $values[] = [
                    'metric_id' => $metric->id,
                    'value' => $value,
                    'author_id' => hideGod($user),
                    'company_id' => $user->company_id,
                ];
            }

            $metric_values = MetricValue::insert($values);
        }

        $this->syncEntities($metric);
    }

    protected function syncEntities(Metric $metric)
    {
        $request = request();
        $metric->entities()->sync($request->entity_id);
    }

    protected function setAlias(Metric $metric)
    {
        if (is_null($metric->alias)) {
            $metric->alias = \Str::slug($metric->name, '');
        }
    }
}
