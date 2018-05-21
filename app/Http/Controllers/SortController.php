<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SortController extends Controller
{
	 // Сортировка
    public function sort(Request $request)
    {
      $result = '';
      $i = 1;
      foreach ($request->items as $item) {

        $item = User::findOrFail($item);
        $item->sort = $i;
        $item->save();
        $i++;
      }
    }
}
