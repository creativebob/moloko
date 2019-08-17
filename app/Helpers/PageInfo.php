<?php
use App\Page;

    function pageInfo($alias) {
        $result = Page::with('entity:id,page_id,view_path,metric,ancestor_id')
        ->where([
            'alias' => $alias,
            'site_id' => 1
        ])
        ->first();

        return $result;
    };

    function pageProjectInfo($alias) {
        $result = Page::where(['alias' => $alias, 'site_id' => 2])->first();
        return $result;
    };

?>
