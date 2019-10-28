<?php

namespace App\Http\Controllers\Traits;

trait Cmvable
{

    public function search($search)
    {

        // Подключение политики
//        $this->authorize('index',  $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('index'));

//        $search = $request->search;
        $items = $this->class::with([
                'article'
            ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->whereHas('article', function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%');
            })
            ->get([
                'id',
                'article_id'
            ]);

//        dd($items);
        foreach ($items as $item) {
            $item->name = $item->article->name;
        }

        return response()->json($items);
    }

}