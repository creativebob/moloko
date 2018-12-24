<?php
use App\Photo;
use App\PhotoSetting;
use App\Entity;

use Illuminate\Support\Facades\Storage;

use Intervention\Image\ImageManagerStatic as Image;

// Сохраняем фотографию
function save_photo($request, $directory, $name, $album_id = null, $id = null, $settings){

    $user = $request->user();

    // Скрываем бога
    $user_id = hideGod($user);
    $company_id = $user->company_id;

    $image = $request->file('photo');

    $extension = $image->getClientOriginalExtension();

    $params = getimagesize($request->file('photo'));

    $width = $params[0];
    $height = $params[1];

    $size = filesize($request->file('photo'))/1024;

    // dd($size);

    if ($width < $settings['img_min_width']) {
        abort(403, 'Ширина фотографии мала!');
    }

    if ($height < $settings['img_min_height']) {
        abort(403, 'Высота фотографии мала!');
    }

    if ($size > ($settings['img_max_size'] * 1024)) {
        abort(403, 'Размер фотографии высок!');
    }

    // dd($width);

    if ($id) {
        $photo = Photo::findOrFail($id);

        if ($photo) {
            foreach (['small', 'medium', 'large', 'original'] as $value) {
                Storage::disk('public')->delete($directory.'/'.$value.'/'.$photo->name);
            }
            // $original = Storage::disk('public')->delete($directory.'original/'.$photo->name);
        }
    } else {
        $photo = new Photo;
    }

    $photo->extension = $extension;
    $image_name = $name.'.'.$extension;

    $photo->width = $params[0];
    $photo->height = $params[1];

    $photo->size = number_format($size, 2, '.', '');

    $photo->album_id = $album_id;
    $photo->name = $image_name;
    $photo->company_id = $company_id;
    $photo->author_id = $user_id;
    $photo->save();
    // dd('Функция маслает!');

    // Сохранияем оригинал
    $upload_success = $image->storeAs($directory.'/original', $image_name, 'public');

    // Сохраняем small, medium и large
    $array = [];
    foreach (['small', 'medium', 'large'] as $value) {
       // $item = Image::make($request->photo)->grab(1200, 795);
        $item = Image::make($request->photo)->widen($settings['img_'.$value.'_width']);
        $save_path = storage_path('app/public/'.$directory.'/'.$value);
        if (!file_exists($save_path)) {
            mkdir($save_path, 0755);
        }
        $item->save(storage_path('app/public/'.$directory.'/'.$value.'/'.$image_name));
    }

    $result = [
        'photo' => $photo,
        'upload_success' => $upload_success,
    ];

    return $result;
}

// Настройки для фоток
function getSettings($entity_alias, $ambum_id = null) {

    // Вытаскиваем настройки из конфига
    $settings = config('photo_settings');

    // dd($settings);

    $entity = Entity::with('photo_settings')
    ->whereAlias($entity_alias)
    ->first();

    $get_settings = $entity->photo_settings;
    if (isset($get_settings)) {

        foreach ($settings as $key => $value) {
            // Если есть ключ в пришедших настройках, то переписываем значение
            if(isset($get_settings->$key)) {
                $settings[$key] = $get_settings->$key;
            }
        }
    }
    // dd($get_settings);

    return $settings;
}

// Пишем / удаляем настройки для фоток, принимаем пришедшие данные, и запись, к которой нужно создать / удалить настройки
function setSettings($request, $item) {


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
            $photo_settings->$setting = isset($request->$setting) ? $request->$setting : $config['$setting'];
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

?>