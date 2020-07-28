<?php

namespace App\Http\View\Composers\System;

use App\AttachmentsCategory;

use Illuminate\View\View;

class AttachmentsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('attachments_categories', false, 'index');

        $attachmentsCategories = AttachmentsCategory::with([
            'attachments' => function ($q) {
                $q->where('archive', false)
                    ->whereHas('article', function ($q) {
                        $q->where([
                            'draft' => false,
                        ]);
                    })
                    ->with([
                        'article' => function ($q) {
                            $q->with([
                                'unit'
                            ])
                                ->where([
                                    'draft' => false,
                                ]);
                        },
                        'category',
                        'unit_for_composition'
                    ])
                    ->orderBy('sort');
            }
        ])
        ->whereHas('attachments', function ($q) {
            $q->where('archive', false)
            ->whereHas('article', function ($q) {
                $q->where('draft', false);
            });
        })
        ->moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        ->orderBy('sort')
        ->get();
//        dd($attachmentsCategories);

        $attachments = [];
        foreach($attachmentsCategories as $attachmentsCategory) {
            foreach ($attachmentsCategory->attachments as $item) {
//                $item->category = $relatedCategory;
                $attachments[] = $item;
            }
        };
        $attachments = collect($attachments);

        return $view->with(compact('attachmentsCategories', 'attachments'));
    }

}
