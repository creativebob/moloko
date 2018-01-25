<?php

    function pageInfo($alias) {
        $page_info = Page::wherePage_alias($alias)->whereSite_id('1')->first();
        return $result;
    };



?>