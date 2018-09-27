<?php

namespace App\Console;

use App\Lead;
use App\User;

use Carbon\Carbon;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        $schedule->call(function () {
            $leads = Lead::whereDate('created_at', Carbon::now()->format('Y-m-d'))->whereNull('draft')->get();

            $telegram_message = "Отчет за день (".Carbon::now()->format('d.m.Y')."): \r\n\r\nЗвонков: ".count($leads->where('lead_type_id', 1))."\r\nЗаявок с сайта: ".count($leads->where('lead_type_id', 2)."\r\n\r\nВсего за день: ".count($leads);
            
            $telegram_destinations = User::whereHas('staff', function ($query) {
                $query->whereHas('position', function ($query) {
                    $query->whereHas('notifications', function ($query) {
                        $query->where('notification_id', 3);
                    });
                });
            })
            ->where('telegram_id', '!=', null)
            ->get(['telegram_id']);

            send_message($telegram_destinations, $telegram_message);

        })
        ->dailyAt('18:30')
        ->timezone('Asia/Irkutsk');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
