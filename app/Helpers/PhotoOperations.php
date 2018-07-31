<?php
use App\Photo;
use App\EntitySetting;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

   // Сохраняем фотографию
function save_photo($request, $directory, $name, $album_id = null, $id = null, $settings){

    $user = $request->user();

    // Скрываем бога
    $user_id = hideGod($user);
    $company_id = $user->company_id;

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

    // Сохраняем small, medium и large
    // $small = Image::make($request->photo)->grab(150, 99);
    $small = Image::make($request->photo)->widen($settings['img_small_width']);
    $save_path = storage_path('app/public/'.$directory.'small');
    if (!file_exists($save_path)) {
        mkdir($save_path, 0755);
    }
    $small->save(storage_path('app/public/'.$directory.'small/'.$image_name));

    // $medium = Image::make($request->photo)->grab(900, 596);
    $medium = Image::make($request->photo)->widen($settings['img_medium_width']);
    $save_path = storage_path('app/public/'.$directory.'medium');
    if (!file_exists($save_path)) {
        mkdir($save_path, 0755);
    }
    $medium->save(storage_path('app/public/'.$directory.'medium/'.$image_name));

    // $large = Image::make($request->photo)->grab(1200, 795);
    $large = Image::make($request->photo)->widen($settings['img_large_width']);
    $save_path = storage_path('app/public/'.$directory.'large');
    if (!file_exists($save_path)) {
        mkdir($save_path, 0755);
    }
    $large->save(storage_path('app/public/'.$directory.'large/'.$image_name));

    $array = [
        'photo' => $photo,
        'upload_success' => $upload_success,
    ];

    return $array;
}

?>