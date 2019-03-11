<?php

namespace App\Http\ViewComposers;

use App\AlbumsCategory;

use Illuminate\View\View;

class AlbumsCategoriesSelectComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('albums_categories', false, 'index');

        $columns = [
            'id',
            'name',
            'parent_id'
        ];

        // Главный запрос
        $albums_categories = AlbumsCategory::moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        ->template($answer)
        // ->whereDisplay(1)
        // ->has('albums')
        ->orderBy('sort', 'asc')
        ->toBase()
        ->get($columns);
        // dd($albums_categories);

        // if ($albums_categories->count() == 0) {

        //     // Описание ошибки
        //     $ajax_error = [];
        //     $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
        //     $ajax_error['text'] = "Для начала необходимо создать категории альбомов. А уже потом будем добавлять. Ок?";
        //     $ajax_error['link'] = "/admin/albums_categories"; // Ссылка на кнопке
        //     $ajax_error['title_link'] = "Идем в раздел категорий"; // Текст на кнопке

        //     return view('ajax_error', compact('ajax_error'));
        // }

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
        $albums_categories_tree = getSelectTree($albums_categories, ($view->parent_id ?? null), ($view->disable ?? null), ($view->id ?? null));
        // dd($albums_categories_tree);

        return $view->with('albums_categories_tree', $albums_categories_tree);
    }

}