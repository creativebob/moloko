<?php

namespace App\Listeners;

use App\Events\onAddLeadEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Log;

class AddLeadListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  onAddLeadEvent  $event
     * @return void
     */
    public function handle(onAddLeadEvent $event)
    {
       Log::info('Лид: ' . $event->case_number . ' добавлен в базу данных. Автор: ' . $event->user_name);
    }
}
