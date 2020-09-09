<?php

namespace App\Console;

use App\Company;
use App\Console\Commands\System\ClientsIndicatorsDay;
use App\Console\Commands\System\ClientsIndicatorsCommand;
use App\Console\Commands\System\DiscountsRecalculateCommand;
use App\Console\Commands\System\Parsers\RollHouseCommand;
use App\Console\Commands\System\TestCommand;
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
        // Тестовая команда
//        $schedule->command(TestCommand::class)
//            ->everyMinute();

        $companies = Company::with([
            'settings'
        ])
            ->has('settings')
            ->get();

        if ($companies->isNotEmpty()) {
            foreach ($companies as $company) {
                foreach ($company->settings as $setting) {
                    switch ($setting->alias) {

                        // Показатели клиентской базы
//                        case 'clients_indicators':
//                            // Ежедневные показатели клиентской базы
//                            $schedule->command(ClientsIndicatorsDay::class, $companyId = $company->id)
//                                ->dailyAt('03:00');
//
//                            // Ежемесячные показатели клиентской базы
//                            $schedule->command(ClientsIndicatorsCommand::class)
//                                ->monthlyOn(1, '04:00');
//                            break;

                        // Скидки
                        case 'discounts':
                            // Перерасчет скидок
                            $schedule->command(DiscountsRecalculateCommand::class, [
                                    'companyId' => $company->id
                                ])
                                ->everyMinute();
                            break;

                    }
                }
            }
        }


        // Показатели клиентской базы
        if (config('app.clients_indicators')) {
            // Ежедневные показатели клиентской базы
            $schedule->command(ClientsIndicatorsDay::class)
                ->dailyAt('03:00');

            // Ежемесячные показатели клиентской базы
            $schedule->command(ClientsIndicatorsCommand::class)
                ->monthlyOn(1, '04:00');
        }
//
//        // Скидки
//        if (config('app.discounts')) {
//            // Перерасчет скидок
//            $schedule->command(DiscountsCommand::class)
//                ->everyMinute();
//        }

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
