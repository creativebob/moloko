<?php

namespace App\Http\Controllers\Traits;

use App\Vector;
use Illuminate\Support\Facades\File;
use App\Photo;
use App\PhotoSetting;
use App\Entity;
use Illuminate\Support\Facades\Storage;
use Image;

trait Photable
{

    /**
     * Сохраняем загруженную фотографию (и удаляем прошлую) и получаем ее id.
     *
     * @param  $request
     * @param  $item
     * @return int
     */
    public function getPhotoId($item)
    {
        $request = request();

        if ($request->hasFile('photo')) {

            $image = $request->file('photo');

            $params = getimagesize($image);
            $width = $params[0];
            $height = $params[1];

            $size = filesize($image) / 1024;
            // dd($size);

            $settings = $this->getPhotoSettings($item->getTable());
//            dd($settings);

            if ($width < $settings['img_min_width']) {
                abort(403, 'Ширина изображения должна быть не менее ' . $settings['img_min_width'] . 'px.');
//                return back()
//                    ->withErrors(['msg' => 'Ширина фотографии мала!'])
//                    ->withInput();
            }

            if ($height < $settings['img_min_height']) {
                abort(403, 'Высота изображения должна быть не менее ' . $settings['img_min_height'] . 'px.');
//                return back()
//                    ->withErrors(['msg' => 'Высота фотографии мала!'])
//                    ->withInput();
            }

            if ($size > ($settings['img_max_size'] * 1024)) {
                abort(403, 'Размер изображения не должен превышать ' . $settings['img_max_size'] . 'mb.');
//                return back()
//                    ->withErrors(['msg' => 'Размер (Mb) фотографии высок!'])
//                    ->withInput();
            }

            if ($item->getTable() == 'companies') {
                $directory = $item->id . '/media/' . $item->getTable() . '/' . $item->id . '/img';
            } else {
                $directory = $item->company_id . '/media/' . $item->getTable() . '/' . $item->id . '/img';
            }

            if (isset($item->photo_id)) {
                $photo = Photo::find($item->photo_id);

                if ($photo) {
                    foreach (['small', 'medium', 'large', 'original'] as $value) {
                        Storage::disk('public')->delete($directory . '/' . $value . '/' . $photo->name);
                    }
                    Storage::disk('public')->delete($directory . '/' . $photo->name);
                }
            } else {
                $photo = Photo::make();
            }

            $extension = $image->getClientOriginalExtension();
            $need_extension = $settings['store_format'] ?? $extension;

            $photo->extension = $need_extension;

            $photo->width = $width;
            $photo->height = $height;

            $photo->size = number_format($size, 2, '.', '');

            $image_name = 'photo_' . time() . '.' . $need_extension;
            $photo->name = $image_name;

            $photo->path = "/storage/{$directory}/{$image_name}";

            $user = $request->user();
            $photo->company_id = $user->company_id;
            $photo->author_id = hideGod($user);

            $photo->save();
            // dd('Функция маслает!');

            // Сохранияем оригинал
            $upload_success = $image->storeAs($directory . '/original', $image_name, 'public');

            $res = Storage::disk('public')->putFileAs(
                $directory, $image, $image_name
            );
            // dd($upload_success);

            // Сохраняем small, medium и large
            foreach (['small', 'medium', 'large'] as $value) {
                switch ($crop_mode = $settings['crop_mode']) {

                    // Пропорциональное уменьшение
                    case 1:
                        $folder = Image::make($request->photo)
                            ->widen($settings['img_' . $value . '_width'])
                            ->crop($settings['img_' . $value . '_width'], $settings['img_' . $value . '_height']);
                        break;

                    // Пропорциональная обрезка
                    case 2:
                        $folder = Image::make($request->photo)
                            ->fit($settings['img_' . $value . '_width'], $settings['img_' . $value . '_height']);
                        break;
                }

                $save_path = storage_path('app/public/' . $directory . '/' . $value);
                if (!file_exists($save_path)) {
                    mkdir($save_path, 0755);
                }
                $folder->save(storage_path('app/public/' . $directory . '/' . $value . '/' . $image_name), $settings['quality'], $settings['store_format']);
            }

            // $item->photo_id = $photo->id;
            // $item->save();
            return $photo->id;
        } else {
            return $item->photo_id;
        }
    }

    // Сохраняем / обновляем фотографию
    public function savePhotoInAlbum($album)
    {
        $request = request();

        $image = $request->file('photo');

        $params = getimagesize($image);
        $width = $params[0];
        $height = $params[1];

        $size = filesize($image) / 1024;
        // dd($size);

        $settings = $this->getPhotoSettingsFromAlbum($album);

        if ($width < $settings['img_min_width']) {
            abort(403, 'Ширина изображения должна быть не менее ' . $settings['img_min_width'] . 'px.');
//                return back()
//                    ->withErrors(['msg' => 'Ширина фотографии мала!'])
//                    ->withInput();
        }

        if ($height < $settings['img_min_height']) {
            abort(403, 'Высота изображения должна быть не менее ' . $settings['img_min_height'] . 'px.');
//                return back()
//                    ->withErrors(['msg' => 'Высота фотографии мала!'])
//                    ->withInput();
        }

        if ($size > ($settings['img_max_size'] * 1024)) {
            abort(403, 'Размер изображения не должен превышать ' . $settings['img_max_size'] . 'mb.');
//                return back()
//                    ->withErrors(['msg' => 'Размер (Mb) фотографии высок!'])
//                    ->withInput();
        }

        $directory = "{$album->company_id}/media/albums/{$album->id}/img";

        $photo = Photo::make();

        $extension = $image->getClientOriginalExtension();
        $need_extension = $settings['store_format'] ?? $extension;

        $photo->extension = $need_extension;

        $photo->width = $width;
        $photo->height = $height;

        $photo->size = number_format($size, 2, '.', '');

        $image_name = 'photo_' . time() . '.' . $need_extension;
        $photo->name = $image_name;

        $photo->path = "/storage/{$directory}/{$image_name}";

        $photo->album_id = $album->id;

        $user = $request->user();
        $photo->company_id = $user->company_id;
        $photo->author_id = hideGod($user);

        $photo->save();
        // dd('Функция маслает!');

        // Сохранияем оригинал
        $upload_success = $image->storeAs($directory . '/original', $image_name, 'public');

        $upload_success = Storage::disk('public')
            ->putFileAs(
                $directory, $image, $image_name
            );
        // dd($upload_success);

        // Сохраняем small, medium и large
        foreach (['small', 'medium', 'large'] as $value) {
            switch ($crop_mode = $settings['crop_mode']) {

                // Пропорциональное уменьшение
                case 1:
                    $folder = Image::make($request->photo)
                        ->widen($settings['img_' . $value . '_width'])
                        ->crop($settings['img_' . $value . '_width'], $settings['img_' . $value . '_height']);
                    break;

                // Пропорциональная обрезка
                case 2:
                    $folder = Image::make($request->photo)
                        ->fit($settings['img_' . $value . '_width'], $settings['img_' . $value . '_height']);
                    break;
            }

            $save_path = storage_path('app/public/' . $directory . '/' . $value);
            if (!file_exists($save_path)) {
                mkdir($save_path, 0755);
            }
            $folder->save(storage_path('app/public/' . $directory . '/' . $value . '/' . $image_name), $settings['quality'], $settings['store_format']);
        }

//        foreach (['small', 'medium', 'large'] as $value) {
//
//            $res = Storage::disk('public')->putFileAs(
//                $directory, $image, $image_name
//            );
//            // $item = Image::make($request->photo)->grab(1200, 795);
//            $folder = Image::make($request->photo)->widen($settings['img_' . $value . '_width']);
//            $save_path = storage_path('app/public/' . $directory . '/' . $value);
//            if (!file_exists($save_path)) {
//                mkdir($save_path, 0755);
//            }
//            $folder->save(storage_path('app/public/' . $directory . '/' . $value . '/' . $image_name));
//        }


        if (!isset($album->photo_id)) {
            $album->update([
                'photo_id' => $photo->id
            ]);
        }

        $result = [
            'photo' => $photo,
            'upload_success' => $upload_success,
        ];
        return $result;
    }

    /**
     * Копируем фото из папки и получаем id.
     *
     * @param  $item
     * @param  $new_item
     * @return int
     */
    public function replicatePhoto($item, $new_item)
    {

        if (isset($item->photo_id)) {

            $photo = $item->photo;
            $new_photo = $photo->replicate();

            $user = request()->user();
            $new_photo->author_id = hideGod($user);

            $new_photo->save();
            // dd('Функция маслает!');

            $directory = $item->company_id . '/media/' . $item->getTable() . '/' . $item->id . '/img';
            $new_directory = $new_item->company_id . '/media/' . $new_item->getTable() . '/' . $new_item->id . '/img';

            foreach ([
                         'original',
                         'small',
                         'medium',
                         'large'
                     ] as $value) {

                Storage::disk('public')
                    ->copy($directory . '/' . $value . '/' . $photo->name, $new_directory . '/' . $value . '/' . $new_photo->name);

            }

            Storage::disk('public')
                ->copy($directory . '/' . $photo->name, $new_directory . '/' . $new_photo->name);

            return $new_photo->id;
        } else {
            return $item->photo_id;
        }
    }

    /**
     * Копируем фото из папки и получаем id.
     *
     * @param  $item
     * @param  $new_item
     * @return int
     */
    public function replicateAlbumWithPhotos($item, $new_item)
    {

        if (isset($item->album_id)) {

            $album = $item->album;
            $new_album = $album->replicate();

            $new_album->name = $new_item->name;

            $user = request()->user();
            $new_album->author_id = hideGod($user);

            $new_album->save();
            // dd('Функция маслает!');

            $photos_insert = [];
            foreach ($album->photos as $photo) {

                $new_photo = $photo->replicate();

                $new_photo->album_id = $new_album->id;

                $user = request()->user();
                $new_photo->author_id = hideGod($user);

                $new_photo->save();

                $photos_insert[] = $new_photo->id;
                // dd('Функция маслает!');

                $directory = $album->company_id . '/media/albums/' . $album->id . '/img';
                $new_directory = $new_album->company_id . '/media/albums/' . $new_album->id . '/img';

                foreach ([
                             'original',
                             'small',
                             'medium',
                             'large'
                         ] as $value) {

                    Storage::disk('public')
                        ->copy($directory . '/' . $value . '/' . $photo->name, $new_directory . '/' . $value . '/' . $new_photo->name);

                }

                Storage::disk('public')
                    ->copy($directory . '/' . $photo->name, $new_directory . '/' . $new_photo->name);

            }
            $new_album->photos()->attach($photos_insert);

            return $new_album->id;
        } else {
            return $item->album_id;
        }
    }

    /**
     * Получаем настройки для фото
     *
     * @param $entityAlias
     * @return array
     */
    public function getPhotoSettings($entityAlias)
    {
        $entity = Entity::with('photo_settings')
            ->where('alias', $entityAlias)
            ->first();
//        dd($entity);

        $settings = $entity->photo_settings;

        if (empty($settings)) {
            $settings = PhotoSetting::whereNull('company_id')
                ->first();
        }

        return $settings;
    }

    public function getPhotoSettingsFromAlbum($item)
    {
        $item->load('photo_settings');
        $settings = $item->photo_settings;

        if (empty($settings)) {
            $entity = Entity::with('photo_settings')
                ->where('alias', $item->getTable())
                ->first();
//        dd($entity);

            $settings = $entity->photo_settings;

            if (empty($settings)) {
                $settings = PhotoSetting::whereNull('company_id')
                    ->first();
            }
        }
//         dd($settings);

        return $settings;
    }

    /**
     * Пишем / удаляем настройки для фоток, принимаем пришедшие данные, и запись, к которой нужно создать / удалить настройки
     *
     * @param $item
     */
    function setPhotoSettings($item)
    {
        // TODO - 29.11.19 - Настройки фоток более не берутся из конфига, сделать запрос

        // Смотрим есть ли отношение
        if (isset($item->photo_settings)) {
            $photo_settings = $item->photo_settings;
        } else {
            $photo_settings = PhotoSetting::make();
        }

        // Определяем список проверяемых значений
        $settings = [
            'store_format',
            'quality',

            'img_min_width',
            'img_min_height',

            'img_small_width',
            'img_small_height',

            'img_medium_width',
            'img_medium_height',

            'img_large_width',
            'img_large_height',

            'img_formats',
            'img_max_size',

            'strict_mode',
            'crop_mode',
        ];

        $request = request();

        // Проверяем пришедшие данные
        $count = 0;
        foreach ($settings as $setting) {
            $count += isset($request->$setting) ? 1 : 0;
        }

        // Если пришло хотя бы одно поле
        if ($count > 0) {

            // Вытаскиваем умолчания из конфига
            $config = PhotoSetting::first();
            // dd($config);

            // Заполняем значения
            foreach ($settings as $setting) {
                $photo_settings->$setting = isset($request->$setting) ? $request->$setting : $config->$setting;
            }

            $item->photo_settings()->save($photo_settings);
        } else {
            $photo_settings->delete();
        }
    }

// Путь до аватарки
    function getPhotoPath($item, $size = 'medium')
    {

        if (isset($item->photo_id)) {

            if ($item->getTable() == 'companies') {
                $path = "/storage/" . $item->id . "/media/" . $item->getTable() . "/" . $item->id . "/img/" . $size . "/" . $item->photo->name;
            } else {
                $path = "/storage/" . $item->company_id . "/media/" . $item->getTable() . "/" . $item->id . "/img/" . $size . "/" . $item->photo->name;
            }

            return $path;
        } else {

            if ($item->getTable() == 'users') {

                if (isset($item->gender)) {
                    $sex = ($item->gender == 1) ? 'man' : 'woman';
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

    /**
     * Сохранение одной фотографии
     *
     * @param $request
     * @param $item
     * @param string $name
     * @return integer $photo_id
     */
    public
    function savePhoto($request, $item, $name = 'photo')
    {
        if ($request->hasFile($name)) {

            $image = $request->file($name);

            $params = getimagesize($image);
            $width = $params[0];
            $height = $params[1];

            $size = filesize($image) / 1024;
            // dd($size);

//                $settings = getPhotoSettings($item->getTable());
//
//                if ($width < $settings['img_min_width']) {
//                    abort(403, 'Ширина фотографии мала!');
//                }
//
//                if ($height < $settings['img_min_height']) {
//                    abort(403, 'Высота фотографии мала!');
//                }
//
//                if ($size > ($settings['img_max_size'] * 1024)) {
//                    abort(403, 'Размер (Mb) фотографии высок!');
//                }

            $directory = $item->company_id . '/media/' . $item->getTable() . '/' . $item->id . '/img';

            if ($item->$name) {
                $photo = $item->$name;
                Storage::disk('public')->delete("{$directory}/{$photo->name}");
            } else {
                $photo = Photo::make();
            }

            $extension = $image->getClientOriginalExtension();

            $photo->extension = $extension;


            $photo->width = $width;
            $photo->height = $height;

            $photo->size = number_format($size, 2, '.', '');

            $image_name = $name . '_' . time() . '.' . $extension;
            $photo->name = $image_name;

            $photo->path = "/storage/{$directory}/{$image_name}";

            $user = $request->user();
            $photo->company_id = $user->company_id;
            $photo->author_id = hideGod($user);

            $photo->save();
//             dd('Функция маслает!');

            // Сохранияем
            $res = Storage::disk('public')->putFileAs(
                $directory, $image, $image_name
            );
//            dd($res);

            return $photo->id;
        } else {
            $column = $name . '_id';
            return $item->$column;
        }

    }

    /**
     * Сохранение векторной фотографии
     *
     * @param $request
     * @param $item
     * @param string $name
     * @return integer $vector_id
     */
    public
    function saveVector($item, $name = 'vector')
    {

        $request = request();

        if ($request->hasFile($name)) {

            $image = $request->file($name);

            $params = getimagesize($image);
//            $width = $params[0];
//            $height = $params[1];

            $size = filesize($image) / 1024;
            // dd($size);

//                $settings = getPhotoSettings($item->getTable());
//
//                if ($width < $settings['img_min_width']) {
//                    abort(403, 'Ширина фотографии мала!');
//                }
//
//                if ($height < $settings['img_min_height']) {
//                    abort(403, 'Высота фотографии мала!');
//                }
//
//                if ($size > ($settings['img_max_size'] * 1024)) {
//                    abort(403, 'Размер (Mb) фотографии высок!');
//                }

            if ($item->getTable() == 'companies') {
                $directory = $item->id . '/media/' . $item->getTable() . '/' . $item->id . '/svg';
            } else {
                $directory = $item->company_id . '/media/' . $item->getTable() . '/' . $item->id . '/svg';
            }

            if ($item->$name) {
                $vector = $item->$name;
                Storage::disk('public')->delete("{$directory}/{$vector->name}");
            } else {
                $vector = Vector::make();
            }

            $extension = $image->getClientOriginalExtension();

            $vector->extension = $extension;

//            $vector->width = $width;
//            $vector->height = $height;

            $vector->size = number_format($size, 2, '.', '');

            $image_name = $name . '_' . time() . '.' . $extension;
            $vector->name = $image_name;

            $vector->path = "/storage/{$directory}/{$image_name}";

            $user = $request->user();
            $vector->company_id = $user->company_id;
            $vector->author_id = hideGod($user);

            $vector->save();
//             dd('Функция маслает!');

            // Сохранияем
            $res = Storage::disk('public')->putFileAs(
                $directory, $image, $image_name
            );
//            dd($res);

            return $vector->id;
        } else {
            $column = $name . '_id';
            return $item->$column;
        }

    }
}
