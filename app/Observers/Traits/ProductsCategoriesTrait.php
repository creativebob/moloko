<?php

namespace App\Observers\Traits;

trait ProductsCategoriesTrait
{

    public function syncManufacturers($category)
    {
        $request = request();
        $category->manufacturers()->sync($request->manufacturers);
    }

    public function syncMetrics($category)
    {
        $request = request();
        $category->metrics()->sync($request->metrics);
    }
}
