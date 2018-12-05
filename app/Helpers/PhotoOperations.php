<?php
use App\Photo;

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
function getSettings($get_settings) {
    // Вытаскиваем настройки из конфига
    $settings = config()->get('settings');
    // dd($settings);
    // dd($get_settings);
    foreach ($settings as $key => $value) {
        // Если есть ключ в пришедших настройках, то переписываем значение
        if(isset($get_settings->$key)) {
            $settings[$key] = $get_settings->$key;
        }
    }
    return $settings;
}

?>