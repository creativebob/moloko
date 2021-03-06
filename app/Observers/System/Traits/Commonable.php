<?php

namespace App\Observers\System\Traits;

trait Commonable
{

	public function store($item)
    {
        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        $answer = operator_right($item->getTable(), false, getmethod(__FUNCTION__));
        if ($answer['automoderate'] == false) {
            $item->moderation = true;
        }

        $request = request();

        $item->display = $request->get('display', true);
        $item->system = $request->get('system', false);

        $user = auth()->user();
        $item->company_id = $user->company_id;
        $item->author_id = $this->getUserId($user);

        return $item;
    }

    public function update($item)
    {
        $user = auth()->user();
        if ($user) {
            $item->editor_id = $this->getUserId($user);
        } else {
            $item->editor_id = 1;
        }

        return $item;
    }

    public function destroy($item)
    {
        $user = auth()->user();
        $item->editor_id = $this->getUserId($user);
        $item->save();

        return $item;
    }

    protected function setSlug($item)
    {
        $item->slug = \Str::slug($item->name);
    }

    /**
     * Скрываем бога и ставим Id робота.
     *
     * @param  $user
     * @return int
     */
    function getUserId($user = null)
    {
        if ($user) {
            // Если бог, то ставим автором робота
            $userId = $user->god == 1 ? 1 : $user->id;
        } else {
            // Если пользователь не авторизован, например cron
            $userId = 1;
        }

        return $userId;
    }
}
