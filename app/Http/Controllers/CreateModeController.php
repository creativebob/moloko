<?php

namespace App\Http\Controllers;

use App\Entity;
use Illuminate\Http\Request;

class CreateModeController extends Controller
{

    // ----------------------------------- Ajax -----------------------------------------

    // Режим создания артикула
	public function ajax_change_create_mode(Request $request)
	{
        // $mode = 'mode-add';
        // $entity = 'service_categories';

		switch ($request->mode) {

			case 'mode-default':

			return view('includes.tmc.create.mode_default');

			break;

			case 'mode-select':

			$entity = Entity::whereAlias($request->category_entity)->first(['model']);
			$model = 'App\\'.$entity->model;

			$category = $model::with(['groups' => function ($q) {
				$q->with('unit');
			}])
			->find($request->category_id);

			$articles_groups = $category->groups;

			return view('includes.tmc.create.mode_select', compact('articles_groups'));

			break;

			case 'mode-add':

			return view('includes.tmc.create.mode_add');

			break;

		}
	}

}
