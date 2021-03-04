<?php

namespace App\Http\Controllers;

use App\Entity;
use App\File;
use App\Http\Requests\System\FileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * FileController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'files';
        $this->entityDependence = false;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FileRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(FileRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), File::class);

        if ($request->hasFile('file')) {

            $model = Entity::where('alias', $request->alias)
                ->value('model');

            $item = $model::find($request->id);

            $file = $request->file('file');

            $data = $request->input();

            $data['size'] = $file->getSize() / 1024;
            $data['extension'] = $file->getClientOriginalExtension();

            $data['slug'] = Str::slug($request->name) . '-' . time() . '.' . $data['extension'];

            if ($request->alias == 'companies') {
                $directory = "{$item->id}/files/{$request->alias}/{$item->id}";
            } else {
                $directory = "{$item->company_id}/files/{$request->alias}/{$item->id}";
            }
            $data['path'] = "/storage/{$directory}/{$data['slug']}";

            $res = Storage::disk('public')
                ->putFileAs($directory, $file, $data['slug']);

            $file = File::create($data);

            $item->files()->attach($file->id);

            return response()->json($file);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FileRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(FileRequest $request, $id)
    {
        $file = File::find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $file);

        $data = $request->validated();
        $file->update($data);

        return response()->json($file);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $file = File::with([
            'vendors'
        ])
            ->moderatorLimit($answer)
            ->find($id);
        if (empty($file)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $file);

        $relations = [
            'vendors'
        ];

        foreach ($relations as $relation) {
            if ($file->$relation->isNotEmpty()) {
                foreach($file->$relation as $item) {
                    Storage::disk('public')
                        ->delete("{$item->company_id}/files/{$relation}/{$item->id}/{$file->slug}");
                    $item->files()->detach($file->id);
                }
            }
        }

        $res = $file->delete();

        return response()->json($res);
    }
}
