<?php


// Сортировка
function sort($request, $model) {

    $i = 1;

    foreach ($request->menus as $item) {
        $model::where('id', $item)->update(['sort' => $i]);
        $i++;
    }
}



?>
