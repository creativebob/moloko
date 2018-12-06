<?php

namespace App\Http\Controllers\Traits;

use App\EntitySetting;

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
            if ($request->hasFile('photo')) {

                // Вытаскиваем настройки
                // Вытаскиваем базовые настройки сохранения фото
                $settings = config()->get('settings');

                // Начинаем проверку настроек, от компании до альбома
                // Смотрим общие настройки для сущности
                $get_settings = EntitySetting::where(['entity' => $this->entity_alias])->first();

                if ($get_settings) {

                    if ($get_settings->img_small_width != null) {
                        $settings['img_small_width'] = $get_settings->img_small_width;
                    }

                    if ($get_settings->img_small_height != null) {
                        $settings['img_small_height'] = $get_settings->img_small_height;
                    }

                    if ($get_settings->img_medium_width != null) {
                        $settings['img_medium_width'] = $get_settings->img_medium_width;
                    }

                    if ($get_settings->img_medium_height != null) {
                        $settings['img_medium_height'] = $get_settings->img_medium_height;
                    }

                    if ($get_settings->img_large_width != null) {
                        $settings['img_large_width'] = $get_settings->img_large_width;
                    }

                    if ($get_settings->img_large_height != null) {
                        $settings['img_large_height'] = $get_settings->img_large_height;
                    }

                    if ($get_settings->img_formats != null) {
                        $settings['img_formats'] = $get_settings->img_formats;
                    }

                    if ($get_settings->img_min_width != null) {
                        $settings['img_min_width'] = $get_settings->img_min_width;
                    }

                    if ($get_settings->img_min_height != null) {
                        $settings['img_min_height'] = $get_settings->img_min_height;
                    }

                    if ($get_settings->img_max_size != null) {
                        $settings['img_max_size'] = $get_settings->img_max_size;
                    }
                }

                // Директория
                $directory = $request->user()->company_id.'/media/'.$this->entity_alias.'/'.$category->id.'/img';

                // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id компании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
                $photo_array = save_photo($request, $directory, 'avatar-'.time(), null, $category->photo_id, $settings);

                $photo = $photo_array['photo'];

                $category->photo_id = $photo->id;
            }

            $category->description = $request->description;
            $category->seo_description = $request->seo_description;
            break;
        }

        return $category;
    }


}