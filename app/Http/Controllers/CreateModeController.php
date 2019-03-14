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

			return view('includes.create_modes.mode_default');

			break;

			case 'mode-select':

			$entity = Entity::whereAlias($request->category_entity)->first(['model']);
			$model = 'App\\'.$entity->model;

			$set_status = $request->set_status == 'true' ? 1 : 0;
			$category = $model::with(['groups' => function ($q) use ($set_status) {
				$q->with('unit')
				->where('set_status', $set_status);
			}])
			->find($request->category_id);

			$articles_groups = $category->groups;

			return view('includes.create_modes.mode_select', compact('articles_groups'));

			break;

			case 'mode-add':

			return view('includes.create_modes.mode_add');

			break;

		}
	}

}
