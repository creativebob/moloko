<?php

namespace App\Http\Controllers\Api\v1;

use App\Entity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppController extends Controller
{

    public function categories_index($category_entity)
    {
        $entity = Entity::whereAlias($category_entity)->first(['model']);
        $model = 'App\\'.$entity->model;

        $categories = $model::with([
            'groups:id,name'
        ])
            ->get([
                'id',
                'name',
            ]);
//         dd($categories);

        $groups = [];
        foreach($categories as $category) {
            foreach ($category->groups as $group) {
                $group->category_id = $category->id;
                $groups[] = $group;
            }
        }
//        dd($groups);

        $res['categories'] = $categories;
        $res['groups'] = $groups;
        return response()->json($res);
    }
}
