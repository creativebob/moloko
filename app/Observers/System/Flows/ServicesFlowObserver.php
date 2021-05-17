<?php

namespace App\Observers\System\Flows;

use App\Models\System\Flows\EventsFlow;
use App\Models\System\Flows\ServicesFlow as Flow;
use Carbon\Carbon;

class ServicesFlowObserver extends ProcessFlowObserver
{

    public function creating(Flow $flow)
    {
        $this->store($flow);
        $this->setManufacturer($flow);
    }

    public function created(Flow $flow)
    {
        // Автоинициация событий
        $flow->load('process.process.events');

        if ($flow->process->process->events->isNotEmpty()) {
            $start = $flow->start_at->toString();

            $data = [
                'filial_id' => $flow->filial_id,

                'capacity_min' => $flow->capacity_min,
                'capacity_max' => $flow->capacity_max,

                'initiator_id' => $flow->id,
            ];

            foreach ($flow->process->process->events as $event) {
                $data['process_id'] = $event->id;
                $data['start_at'] = Carbon::create($start);
                $data['finish_at'] = Carbon::create($start)->addSeconds($event->process->length);

                EventsFlow::create($data);

                $start = $data['finish_at']->toString();
            }
        }
    }

    public function updating(Flow $flow)
    {
        $this->update($flow);
        $this->setManufacturer($flow);
    }

    public function deleting(Flow $flow)
    {
        $this->destroy($flow);
    }

    public function deleted(Flow $flow)
    {
        if ($flow->events->isNotEmpty()) {
            $flow->events()->delete();
        }
    }
}
