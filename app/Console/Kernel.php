<?php

namespace App\Console;

use App\Console\Commands\System\ClientsIndicatorsDay;
use App\Console\Commands\System\ClientsIndicatorsCommand;
use App\Console\Commands\System\Parsers\RollHouseCommand;
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
//        'App\Console\Commands\ClientsIndicatorsDay',
//        'App\Console\Commands\ClientsIndicatorsReport',
    ];

    /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return \DateTimeZone|string|null
     */
    protected function scheduleTimezone()
    {
        return config('app.timezone');
    }

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (config('app.clients_indicators')) {
            // Ежедневные показатели клиентской базы
            $schedule->command(ClientsIndicatorsDay::class)
                ->dailyAt('03:00');

            // Ежемесячные показатели клиентской базы
            $schedule->command(ClientsIndicatorsCommand::class)
                ->monthlyOn(1, '04:00');
        }

        if (config('app.roll_house_parser')) {
            // Парсер лидов для РХ
            $schedule->command(RollHouseCommand::class)
                ->everyMinute();
        }

        // Ежедневный отчет
//        $schedule->command('report:day')
//        ->dailyAt('18:30')
//        ->timezone('Asia/Irkutsk');
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
