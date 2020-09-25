<?php

namespace App\Http\Controllers\Api\v1;

use App\Album;
use App\Entity;
use App\Http\Controllers\Traits\Photable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PhotoController extends Controller
{

    use Photable;
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->hasFile('photo')) {

            // Обновляем id альбома
            $entity = Entity::whereAlias($request->entity)->first();
            $model = 'App\\'.$entity->model;
            $item = $model::with('album')->find($request->id);


            if (isset($item->album)) {
                $album = $item->album;
            } else {
                // Получаем пользователя
                $user = $request->user();

                $album = Album::firstOrCreate(
                    [
                        'name' => $request->name,
                        'category_id' => 1,
                        'company_id' => $user->company_id,
                    ], [
                        'description' => $request->name,
                        'alias' => Str::slug($request->name),
                        'author_id' => hideGod($user),
                    ]
                );

                $item->album_id = $album->id;
                $item->save();
            }

            // Cохраняем / обновляем фото
            $result = $this->savePhotoInAlbum($request, $album);

            $album->photos()->attach($result['photo']->id);

            return response()->json($result['upload_success'], 200);
            // return response()->json($photo, 200);

        } else {
            return response()->json('error', 400);
        }
    }
}
