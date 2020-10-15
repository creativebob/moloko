<?php

namespace App\Http\Controllers;

use App\Entity;
use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Contracts\Filesystem\Filesystem;
use Image;


class ImageController extends Controller
{
	public function show($item_id, $entity, $size = 'medium')
	{

        $entity = Entity::whereAlias($entity)->first([
            'model',
            'alias'
        ]);
        $model = $entity->model;

        $item = $model::with('photo')
            ->find($item_id);
//        dd($item);

        if (isset($item->photo_id)) {
            $path = storage_path() . '/app/public/' . $item->company_id . '/media/' . $entity->alias . "/" . $item->id . "/img/" . $size . "/" . $item->photo->name;
        } else {
            $path = public_path() . '/img/system/plug/' . $item->getTable() . '_small_default.jpg';
        }
//        dd($path);

//        $img = Image::cache(function($image) {
//            $image->make('public/foo.jpg')->resize(300, 200)->greyscale();
//        });

        $img = Image::cache(function($image) use ($path) {
            $image->make($path)->resize(300, 200)->greyscale();
        });

//        dd($img);

//        $image = Image::make($path)->response();

        return $img;
	}

	public function store(Request $request)
    {
        if ($request->hasFile('photo')) {

            $image = $request->file('photo');

            $params = getimagesize($image);
            $width = $params[0];
            $height = $params[1];

            $size = filesize($image)/1024;
            // dd($size);

            $settings = getPhotoSettings($item->getTable());

            if ($width < $settings['img_min_width']) {
                abort(403, 'Ширина фотографии мала!');
            }

            if ($height < $settings['img_min_height']) {
                abort(403, 'Высота фотографии мала!');
            }

            if ($size > ($settings['img_max_size'] * 1024)) {
                abort(403, 'Размер (Mb) фотографии высок!');
            }

            $directory = $item->company_id . '/media/' . $item->getTable() . '/' . $item->id . '/img';

            if (isset($item->photo_id)) {
                $photo = Photo::find($item->photo_id);

                if ($photo) {
                    foreach (['small', 'medium', 'large', 'original'] as $value) {
                        Storage::disk('public')->delete($directory.'/'.$value.'/'.$photo->name);
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
            $upload_success = $image->storeAs($directory.'/original', $image_name, 'public');
            // dd($upload_success);

            Storage::disk('local')->put($directory . '/' . $image_name, $image->encode('jpg', 100));

            // Сохраняем small, medium и large
//            foreach (['small', 'medium', 'large'] as $value) {
//                // $item = Image::make($request->photo)->grab(1200, 795);
//                $folder = \Intervention\Image\ImageManagerStatic::make($request->photo)->widen($settings['img_'.$value.'_width']);
//                $save_path = storage_path('app/public/'.$directory.'/'.$value);
//                if (!file_exists($save_path)) {
//                    mkdir($save_path, 0755);
//                }
//                $folder->save(storage_path('app/public/'.$directory.'/'.$value.'/'.$image_name));
//            }

            // $item->photo_id = $photo->id;
            // $item->save();
            return $photo->id;
        }

    }
}
