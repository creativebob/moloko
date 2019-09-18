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
	            'parent_id',
	            'level'
            ]);
//        dd($categories);

        $groups = [];
        foreach($categories as $category) {
        	if (isset($category->groups)) {
		        foreach ($category->groups as $group) {
			        $group->category_id = $category->id;
			        $groups[] = $group;
		        }
	        }
        }
//        dd($groups);

        $res['categories'] = buildTree($categories);
        $res['groups'] = $groups;
        return response()->json($res);
    }
	
	public function categories_select($entity_alias)
	{
		$alias = $entity_alias.'_categories';
		
		$entity = Entity::whereAlias($alias)->first(['model']);
		$model = 'App\\'.$entity->model;
		
		$categories = $model::with([
			$entity_alias.'.article:id,name'
		])
			->get([
				'id',
				'name',
				'parent_id',
			]);
//		dd($categories);
		
		$items = [];
		foreach($categories as $category) {
			
			if (isset($category->$entity_alias)) {
				foreach ($category->$entity_alias as $item) {
					$item->article->category_id = $category->id;
					$items[] = $item->article;
				}
			}
			
			if (isset($category->childCategories)) {
				if (isset($category->$entity_alias)) {
					foreach ($category->childCategories as $childCategory) {
						foreach ($childCategory->$entity_alias as $item) {
							$item->article->category_id = $childCategory->id;
							$items[] = $item->article;
						}
					}
				}
			}
		}
//        dd($items);
		
		$res['categories'] = buildTree($categories);
		$res['items'] = $items;
		return response()->json($res);
	}
}
