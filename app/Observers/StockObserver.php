<?php

namespace App\Observers;

use App\Stock;

class StockObserver
{

    public function creating(Stock $stock)
    {

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        $answer = operator_right('stocks', false, getmethod(__FUNCTION__));
        if($answer['automoderate'] == false){
            $stock->moderation = 1;
        }

        $request = request();

        $stock->system_item = $request->system_item;
        $stock->display = $request->display;

        $user = $request->user();
        $stock->company_id = $user->company_id;
        $stock->author_id = hideGod($user);
    }

    public function updating(Stock $stock)
    {
        $request = request();

        $stock->system_item = $request->system_item;
        $stock->display = $request->display;
        $stock->moderation = $request->moderation;

        $stock->editor_id = hideGod($request->user());
    }

    public function deleting(Stock $stock)
    {
        $request = request();

        $stock->editor_id = hideGod($request->user());
        $stock->save();
        // dd($stock);
        // $stock->update([
        //     'editor_id' => hideGod($request->user()),
        // ]);
        // dd($stock);
    }

}
