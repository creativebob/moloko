<?php

namespace App\Observers\Traits;

trait CommonTrait
{

	public function store($item)
    {
        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        $answer = operator_right($item->getTable(), false, getmethod(__FUNCTION__));
        if($answer['automoderate'] == false){
            $item->moderation = true;
        }

        $request = request();

        $user = $request->user();
        $item->company_id = $user->company_id;
        $item->author_id = hideGod($user);

        return $item;
    }

    public function update($item)
    {
        $request = request();

        $item->editor_id = hideGod($request->user());

        return $item;
    }

    public function destroy($item)
    {
        $item->editor_id = hideGod(request()->user());
        $item->save();

        return $item;
    }

    protected function setSlug($item)
    {
        $item->slug = \Str::slug($item->name);
    }

//    protected function setSlug($item)
//    {
//        if (empty($item->alias)) {
//            $item->alias = \Str::slug($item->name);
//            $item->slug = \Str::slug($item->name);
//        }
//    }
}
