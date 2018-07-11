<?php
use App\Photo;
use App\AlbumsSetting;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

   // Сохраняем фотографию
function save_photo($request, $user_id, $company_id, $directory, $name, $album_id = null, $id = null){

    if ($id) {
        $photo = Photo::findOrFail($id);

        if ($photo) {
            $small = Storage::disk('public')->delete($directory.'small/'.$photo->name);
            $medium = Storage::disk('public')->delete($directory.'medium/'.$photo->name);
            $large = Storage::disk('public')->delete($directory.'large/'.$photo->name);
            $original = Storage::disk('public')->delete($directory.'original/'.$photo->name);
        }
    } else {
        $photo = new Photo; 
    }
    
    $image = $request->file('photo');

    $extension = $image->getClientOriginalExtension();
    $photo->extension = $extension;
    $image_name = $name.'.'.$extension;

    $params = getimagesize($request->file('photo'));
    $photo->width = $params[0];
    $photo->height = $params[1];

    $size = filesize($request->file('photo'))/1024;
    $photo->size = number_format($size, 2, '.', '');

    $photo->album_id = $album_id;
    $photo->name = $image_name;
    $photo->company_id = $company_id;
    $photo->author_id = $user_id;
    $photo->save();

    // dd('Функция маслает!');

    // Сохранияем оригинал
    $upload_success = $image->storeAs($directory.'original', $image_name, 'public');

        if($album_id != null){

        // Смотрим, есть ли настройки на конкретный альбом
        $get_settings = AlbumsSetting::where('album_id', $album_id)->first();

        $settings['img_small_width'] = $get_settings->img_small_width;
        $settings['img_small_height'] = $get_settings->img_small_height;
        $settings['img_medium_width'] = $get_settings->img_medium_width;
        $settings['img_medium_height'] = $get_settings->img_medium_height;
        $settings['img_large_width'] = $get_settings->img_large_width;
        $settings['img_large_height'] = $get_settings->img_large_height;   
        $settings['img_formats'] = $get_settings->img_formats;
        $settings['img_min_width'] = $get_settings->img_min_width;
        $settings['img_min_height'] = $get_settings->img_min_height;   
        $settings['img_max_size'] = $get_settings->img_max_size;

    } else {

        // Вытаскиваем настройки сохранения фото
        $settings = config()->get('settings');
    };

    // Сохраняем small, medium и large
    // $small = Image::make($request->photo)->grab(150, 99);
    $small = Image::make($request->photo)->widen($settings['img_small_width']);
    $save_path = storage_path('app/public/'.$directory.'small');
    if (!file_exists($save_path)) {
        mkdir($save_path, 755, true);
    }
    $small->save(storage_path('app/public/'.$directory.'small/'.$image_name));

    // $medium = Image::make($request->photo)->grab(900, 596);
    $medium = Image::make($request->photo)->widen($settings['img_medium_width']);
    $save_path = storage_path('app/public/'.$directory.'medium');
    if (!file_exists($save_path)) {
        mkdir($save_path, 755, true);
    }
    $medium->save(storage_path('app/public/'.$directory.'medium/'.$image_name));

    // $large = Image::make($request->photo)->grab(1200, 795);
    $large = Image::make($request->photo)->widen($settings['img_large_width']);
    $save_path = storage_path('app/public/'.$directory.'large');
    if (!file_exists($save_path)) {
        mkdir($save_path, 755, true);
    }
    $large->save(storage_path('app/public/'.$directory.'large/'.$image_name));

    $array = [
        'photo' => $photo,
        'upload_success' => $upload_success,
    ];

    return $array;
}

?>