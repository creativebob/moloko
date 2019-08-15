<?php

namespace App\Observers\Traits;

use App\Direction;

trait DirectionTrait
{

    public function checkDirection($category)
    {

        if (is_null($category->parent_id)) {

            $category->load('direction');
            $direction = $category->direction;
            $request = request();

            if ($request->is_direction == 1) {

                if (is_null($direction)) {
                    $direction = Direction::firstOrCreate([
                        'category_id' => $category->id,
                        'category_type' => $this->model,
                        'archive' => false
                    ]);
                } else {
                    $direction->update([
                        'archive' => false
                    ]);
                }

            } else {
                if (isset($direction)) {
                    $direction->update([
                        'archive' => true
                    ]);
                }
            }
        }
    }
}
