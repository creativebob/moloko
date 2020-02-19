<?php

namespace App\Http\View\Composers\Project;

use App\ToolsCategory;
use Illuminate\View\View;

class ToolsCategoriesWithToolsComposer
{

    public function compose(View $view)
    {
        $site = $view->site;

        $tools_categories = ToolsCategory::with([
            'tools.article'
        ])
            ->where([
                'company_id' => $site->company_id,
                'display' => true
            ])
            ->get();

        return $view->with(compact('tools_categories'));
    }

}
