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

			return view('products.common.create.create_modes.mode_default');

			break;

			case 'mode-select':

			$entity = Entity::whereAlias($request->category_entity)->first(['model']);
			$model = 'App\\'.$entity->model;

			$category = $model::with(['groups' => function ($q) {
				$q->with('unit');
			}])
			->find($request->category_id);

			$groups = $category->groups;

			return view('products.common.create.create_modes.mode_select', compact('groups'));

			break;

			case 'mode-add':

			return view('products.common.create.create_modes.mode_add');

			break;

		}
	}

}
