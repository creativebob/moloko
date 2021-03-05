<?php

namespace App\Http\View\Composers\System;

use App\Entity;
use Illuminate\View\View;

class CmvCategoriesTreeComposer
{
    /**
     * Дерево категорий
     */
    protected $categoriesTree;

    /**
     * Отдаем на шаблон
     *
     * @param View $view
     * @return View
     */
	public function compose(View $view)
	{
	    $entityAlias = $view->entity;

	    $entity = Entity::with('ancestor:id,alias,model')
        ->where('alias', $entityAlias)
            ->first();
//	    dd($entity);

        $categoriesEntity = $entity->ancestor;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($categoriesEntity->alias, false, 'index');

        $categories = $categoriesEntity->model::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->get();

        $this->categoriesTree = buildTree($categories);
        $this->categoriesTree = array_values($this->categoriesTree->toArray());
//        dd($this->categoriesTree);

        return $view->with('categoriesTree', $this->categoriesTree);
    }
}
