<?php

namespace App\Http\Controllers\Traits;

use App\PhotoSetting;

trait CategoryControllerTrait
{

	public function storeCategory($request)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $category = new $this->class;

        $category->company_id = $user->company_id;
        $category->author_id = hideGod($user);

        // Системная запись
        $category->system_item = $request->system_item;
        $category->display = $request->display;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, 'store');

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $category->moderation = 1;
        }

        $category->parent_id = $request->parent_id;
        $category->category_id = $request->category_id;

        // Делаем заглавной первую букву
        $category->name = get_first_letter($request->name);

        return $category;
    }


    public function updateCategory($request, $category)
    {

        // Модерация и системная запись
        $category->system_item = $request->system_item;
        $category->moderation = $request->moderation;
        $category->display = $request->display;

        $category->parent_id = $request->parent_id;
        $category->editor_id = hideGod($request->user());

        // Делаем заглавной первую букву
        $category->name = get_first_letter($request->name);

        switch ($this->type) {
            case 'modal':

            break;

            case 'edit':
            // Если прикрепили фото
            savePhoto($request, $category);

            $category->description = $request->description;
            $category->seo_description = $request->seo_description;
            break;
        }

        return $category;
    }

}