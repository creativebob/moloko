<?php

namespace App\Http\Controllers\Traits;

use App\Photo;
use App\PhotoSetting;
use App\Entity;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

trait Photable
{

    /**
     * Get photo id.
     *
     * @param  $request
     * @param  $item
     * @return int
     */

    function getPhotoId($request, $item)
    {

        if ($request->hasFile('photo'))  {

            $image = $request->file('photo');

            $params = getimagesize($image);
            $width = $params[0];
            $height = $params[1];

            $size = filesize($image) / 1024;
            // dd($size);

            $settings = $this->getSettings($item->getTable());

            if ($width < $settings['img_min_width']) {
                return back()
                    ->withErrors(['msg' => 'Ширина фотографии мала!'])
                    ->withInput();
            }

            if ($height < $settings['img_min_height']) {
                return back()
                    ->withErrors(['msg' => 'Высота фотографии мала!'])
                    ->withInput();
            }

            if ($size > ($settings['img_max_size'] * 1024)) {
                return back()
                    ->withErrors(['msg' => 'Размер (Mb) фотографии высок!'])
                    ->withInput();
            }

            $directory = $item->company_id . '/media/' . $item->getTable() . '/' . $item->id . '/img';

            if (isset($item->photo_id)) {
                $photo = Photo::findOrFail($item->photo_id);

                if ($photo) {
                    foreach (['small', 'medium', 'large', 'original'] as $value) {
                        Storage::disk('public')->delete($directory . '/' . $value . '/' . $photo->name);
                    }
                }
            } else {
                $photo = new Photo;
            }

            $extension = $image->getClientOriginalExtension();

            $photo->extension = $extension;

            $photo->width = $width;
            $photo->height = $height;

            $photo->size = number_format($size, 2, '.', '');

            $image_name = 'photo_' . time() . '.' . $extension;
            $photo->name = $image_name;

            $user = $request->user();
            $photo->company_id = $user->company_id;
            $photo->author_id = hideGod($user);

            $photo->save();
            // dd('Функция маслает!');

            // Сохранияем оригинал
            $upload_success = $image->storeAs($directory . '/original', $image_name, 'public');
            // dd($upload_success);

            // Сохраняем small, medium и large
            foreach (['small', 'medium', 'large'] as $value) {
                // $item = Image::make($request->photo)->grab(1200, 795);
                $folder = Image::make($request->photo)->widen($settings['img_' . $value . '_width']);
                $save_path = storage_path('app/public/' . $directory . '/' . $value);
                if (!file_exists($save_path)) {
                    mkdir($save_path, 0755);
                }
                $folder->save(storage_path('app/public/' . $directory . '/' . $value . '/' . $image_name));
            }

            // $item->photo_id = $photo->id;
            // $item->save();
            return $photo->id;
        } else {
            return $item->photo_id;
        }
    }

    // Сохраняем / обновляем фотографию
    function savePhotoInAlbum($request, $album)
    {


        $image = $request->file('photo');

        $params = getimagesize($image);
        $width = $params[0];
        $height = $params[1];

        $size = filesize($image) / 1024;
        // dd($size);

        $settings = getSettings('albums');

        if ($width < $settings['img_min_width']) {
            abort(403, 'Ширина фотографии мала!');
        }

        if ($height < $settings['img_min_height']) {
            abort(403, 'Высота фотографии мала!');
        }

        if ($size > ($settings['img_max_size'] * 1024)) {
            abort(403, 'Размер (Mb) фотографии высок!');
        }

        $photo = new Photo;

        $extension = $image->getClientOriginalExtension();

        $photo->extension = $extension;

        $photo->width = $width;
        $photo->height = $height;

        $photo->size = number_format($size, 2, '.', '');

        // $photo->album_id = $album_id;
        $image_name = 'photo_' . time() . '.' . $extension;
        $photo->name = $image_name;

        $user = $request->user();
        $photo->company_id = $user->company_id;
        $photo->author_id = hideGod($user);

        $photo->album_id = $album->id;

        $photo->save();
        // dd('Функция маслает!');

        $directory = $album->company_id . '/media/albums/' . $album->id . '/img';

        // Сохранияем оригинал
        $upload_success = $image->storeAs($directory . '/original', $image_name, 'public');
        // dd($upload_success);

        // Сохраняем small, medium и large
        foreach (['small', 'medium', 'large'] as $value) {
            // $item = Image::make($request->photo)->grab(1200, 795);
            $folder = Image::make($request->photo)->widen($settings['img_' . $value . '_width']);
            $save_path = storage_path('app/public/' . $directory . '/' . $value);
            if (!file_exists($save_path)) {
                mkdir($save_path, 0755);
            }
            $folder->save(storage_path('app/public/' . $directory . '/' . $value . '/' . $image_name));
        }

        if (!isset($album->photo_id)) {
            $album->photo_id = $photo->id;
            $album->save();
        }

        $result = [
            'photo' => $photo,
            'upload_success' => $upload_success,
        ];
        return $result;
    }

    // Настройки для фоток
    function getSettings($album_id = null)
    {

        // Вытаскиваем настройки из конфига
        $settings = config('photo_settings');

        // dd($settings);

        $entity = Entity::with('photo_settings')
            ->whereAlias($this->entity_alias)
            ->first();

        // dd($entity);

        $get_settings = $entity->photo_settings;

        if (isset($get_settings)) {

            foreach ($settings as $key => $value) {
                // Если есть ключ в пришедших настройках, то переписываем значение
                if (isset($get_settings->$key)) {
                    $settings[$key] = $get_settings->$key;
                }
            }
        }
        // dd($get_settings);

        return $settings;
    }

    // Пишем / удаляем настройки для фоток, принимаем пришедшие данные, и запись, к которой нужно создать / удалить настройки
    function setSettings($request, $item)
    {


        // Смотрим есть ли отношение
        if (isset($item->photo_settings)) {
            $photo_settings = $item->photo_settings;
        } else {
            $photo_settings = new PhotoSetting;
        }

        // Определяем список проверяемых значений
        $settings = [
            'img_small_width',
            'img_small_height',
            'img_medium_width',
            'img_medium_height',
            'img_large_width',
            'img_large_height',
            'img_formats',
            'img_min_width',
            'img_min_height',
            'img_max_size',
        ];

        // Проверяем пришедшие данные
        $count = 0;
        foreach ($settings as $setting) {
            $count += isset($request->$setting) ? 1 : 0;
        }

        // Если пришло хотя бы одно поле
        if ($count > 0) {

            // Вытаскиваем умолчания из конфига
            $config = config('photo_settings');
            // dd($config);

            // Заполняем значения
            foreach ($settings as $setting) {
                $photo_settings->$setting = isset($request->$setting) ? $request->$setting : $config[$setting];
            }

            // Ставим умолчания
            $photo_settings->strict_mode = isset($request->strict_mode) ? $request->strict_mode : $config['strict_mode'];

            $user = $request->user();
            $photo_settings->company_id = $user->company_id;
            $photo_settings->author_id = hideGod($user);

            $item->photo_settings()->save($photo_settings);
        } else {
            $photo_settings->delete();
        }
    }

    // Путь до аватарки
    function getPhotoPath($item, $size = 'medium')
    {

        if (isset($item->photo_id)) {

            $path = "/storage/" . $item->company_id . "/media/" . $item->getTable() . "/" . $item->id . "/img/" . $size . "/" . $item->photo->name;
            return $path;
        } else {

            if ($item->getTable() == 'users') {

                if (isset($item->sex)) {
                    $sex = ($item->sex == 1) ? 'man' : 'woman';
                } else {
                    $sex = 'man';
                }

                return '/img/system/plug/avatar_small_' . $sex . '.png';
            } else {
                return '/img/system/plug/' . $item->getTable() . '_small_default_color.jpg';
            }
        }
    }

    // Путь до картинки-заглушки экземпляра
    function getPhotoPathPlugEntity($item, $size = 'medium')
    {

        if (isset($item->process)) {
            if (isset($item->process->photo_id)) {

                $path = "/storage/" . $item->process->company_id . "/media/" . $item->process->getTable() . "/" . $item->process->id . "/img/" . $size . "/" . $item->process->photo->name;
                return $path;

            } else {

                return '/img/system/plug/' . $item->getTable() . '_small_default_color.jpg';
            }
        }

        if (isset($item->article)) {
            if (isset($item->article->photo_id)) {

                $path = "/storage/" . $item->article->company_id . "/media/" . $item->article->getTable() . "/" . $item->article->id . "/img/" . $size . "/" . $item->article->photo->name;
                return $path;

            } else {

                return '/img/system/plug/' . $item->getTable() . '_small_default_color.jpg';
            }
        }

    }


    // Путь до фотки в альбоме
    function getPhotoInAlbumPath($photo, $size = 'medium')
    {

        return "/storage/" . $photo->company_id . "/media/albums/" . $photo->album_id . "/img/" . $size . "/" . $photo->name;

    }
}