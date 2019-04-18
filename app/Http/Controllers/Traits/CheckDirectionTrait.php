<?php

namespace App\Http\Controllers\Traits;

use App\Direction;

trait DirectionTrait
{

	public function checkDirection($request, $category)
    {
        // dd($request->direction);
        if (isset($request->direction)) {

            $direction = Direction::firstOrCreate([
                'company_id' => $request->user()->company_id,
                'category_id' => $category->id,
                'category_type' => $this->model,
            ], [
                'author_id' => hideGod($request->user())
            ]);

            $direction->archive = false;
            $direction->save();

        } else {

            if (isset($category->direction)) {
               $direction = $category->direction;
               $direction->archive = true;
               $direction->save();
            }
        }
    }

}