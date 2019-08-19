<?php

namespace App\Observers\Traits;

trait ProductsCategoriesTrait
{

    public function syncManufacturers($category)
    {
        $request = request();
        $category->manufacturers()->sync($request->manufacturers);
    }
}
