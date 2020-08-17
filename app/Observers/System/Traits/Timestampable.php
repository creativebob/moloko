<?php

namespace App\Observers\System\Traits;

use Carbon\Carbon;

trait Timestampable
{
    /**
     * Из 2 значений (дата (формат - d.m.Y - PickMeUp календарь) и время (формат - H:i)) получаем значение для вставки в бд.
     * Если поле не обязательно и пришло только одно значение - не вставляем ничего
     *
     * @param $name
     * @param bool $required
     * @return Carbon|null
     */
    public function getTimestamp($fieldName, $required = false)
    {
        $request = request();
        $dateFieldName = "{$fieldName}_date";
        $timeFieldName = "{$fieldName}_time";
        $value = null;
        $date = null;
        $time = null;

        if (isset($request->$dateFieldName)) {
            $date = Carbon::createFromFormat('d.m.Y', $request->$dateFieldName);
        } else {
            if ($required) {
                $date = today()->format('Y-m-d');
            }
        }

        if (isset($request->$timeFieldName)) {
            $time = Carbon::createFromFormat('H:i', $request->$timeFieldName);
        } else {
            if ($required) {
                $time = now()->format('H:i');
            } else {
                if ($date) {
                    $time = Carbon::createFromFormat('H:i', '00:00');
                }
            }
        }

        // TODO - 15.08.20 - Посмотреть как в карбоне сращивать дату с временем
        if ($date && $time) {
            $value = Carbon::createFromFormat('d.m.Y H:i', "{$date->format('d.m.Y')} {$time->format('H:i')}");
        }

        return $value;
    }
}
