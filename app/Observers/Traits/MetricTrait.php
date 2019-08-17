<?php

namespace App\Observers\Traits;

trait MetricTrait
{
    public function syncMetrics($item)
    {
        $request = request();
        $item->metrics()->sync($request->metrics);
    }
}
