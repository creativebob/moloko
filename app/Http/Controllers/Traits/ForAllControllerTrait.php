<?php

namespace App\Http\Controllers\Traits;

trait ForAllControllerTrait
{

    // Отдает нужное название метода для отправки на проверку права
    protected function getmethod($method){

        if($method == 'index'){return 'index';};
        if($method == 'show'){return 'view';};
        if(($method == 'edit')||($method == 'update')){return 'update';};
        if(($method == 'create')||($method == 'store')){return 'create';};
        if($method == 'destroy'){return 'delete';};

    }
}