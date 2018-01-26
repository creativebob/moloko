<?php
use App\Page;

    function pageInfo($alias) {
        $result = Page::where(['page_alias' => $alias, 'site_id' => 1])->first();
        return $result;
    };
?>