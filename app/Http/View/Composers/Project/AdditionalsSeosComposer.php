<?php

namespace App\Http\View\Composers\Project;

use App\Models\Project\Album;
use App\Seo;
use Illuminate\View\View;

class AdditionalsSeosComposer
{
	public function compose(View $view)
	{
        $seo = $view->item->seo;
	    if ($seo) {
            $countRequestInput = count(request()->input());

            // Если есть параметры и доп сео
	        if ($countRequestInput > 0 && $seo->childs_count > 0) {
	            $params = request()->input();
                $query = Seo::where('parent_id', $seo->id)
                    ->has('params', $countRequestInput);

                    foreach($params as $param => $value) {
                        $query->whereHas('params', function ($q) use ($param, $value) {
                            $q->where([
                                'param' => $param,
                                'value' => $value
                            ]);
                        });
                    }

                $additionalSeo = $query->first();
                if ($additionalSeo) {
                    $seo = $additionalSeo;
                }
            }
        } else {
	        $seo = $view->page->seo;
	        if (empty($seo)) {
                $seo = (object) [
                    'title' => '',
                    'description'  => '',
                    'keywords'  => '',
                ];
            }
        }
        return $view->with(compact('seo'));
    }

}
