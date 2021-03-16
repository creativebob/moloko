<?php

namespace App\Http\Controllers\System\Traits;

use App\Entity;

trait Cmvable
{
    /**
     * Получаем массив алиасов сущностей тмц
     *
     * @return mixed
     */
    public function getArrayCmvAliases()
    {
        $cmvEntities = Entity::whereHas('type', function ($q) {
            $q->where('alias', 'cmv');
        })
            ->get([
                'alias',
            ]);

        return $cmvEntities->pluck('alias')->toArray();
    }
}
