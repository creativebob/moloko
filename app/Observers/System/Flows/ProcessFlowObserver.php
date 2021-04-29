<?php

namespace App\Observers\System\Flows;

use App\Observers\System\BaseObserver;
use App\Process;

class ProcessFlowObserver extends BaseObserver
{
    /**
     * Устанавливаем производителя
     *
     * @param $flow
     */
    public function setManufacturer($flow)
    {
        $flow->manufacturer_id = Process::where('id', $flow->process_id)->value('manufacturer_id');
    }
}
