<?php

namespace App\Console;

use App\Company;
use App\Console\Commands\System\ClientsIndicatorsDay;
use App\Console\Commands\System\ClientsIndicatorsCommand;
use App\Console\Commands\System\DiscountsRecalculateCommand;
use App\Console\Commands\System\Parsers\RollHouseCommand;
use App\Console\Commands\System\Parsers\VkusnyashkaMailingCommand;
use App\Console\Commands\System\TestCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Telegram;

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
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Тестовая команда
//        $schedule->command(TestCommand::class)
//            ->everyMinute();

        $companies = Company::with([
            'settings' => function ($q) {
                $q->whereHas('category', function ($q) {
                    $q->where('alias', 'cron');
                });
            }
        ])
            ->whereHas('settings', function ($q) {
                $q->whereHas('category', function ($q) {
                   $q->where('alias', 'cron');
                });
            })
            ->has('settings')
            ->get();

        foreach ($companies as $company) {
            foreach ($company->settings as $setting) {
                switch ($setting->alias) {

                    // Показатели клиентской базы
                    case 'clients-indicators':
                        // Ежедневные показатели клиентской базы
                        $schedule->command(ClientsIndicatorsDay::class)
                            ->dailyAt('03:00');

                        // Ежемесячные показатели клиентской базы
                        $schedule->command(ClientsIndicatorsCommand::class)
                            ->monthlyOn(1, '04:00');
                        break;

                    // Скидки
                    case 'discounts-recalculate':
                        // Перерасчет скидок
                        $schedule->command(DiscountsRecalculateCommand::class, [
                            $company->id
                        ])
                            ->everyMinute();
                        break;

                }
            }
        }


        // Показатели клиентской базы
//        if (config('app.clients_indicators')) {
//            // Ежедневные показатели клиентской базы
//            $schedule->command(ClientsIndicatorsDay::class)
//                ->dailyAt('03:00');
//
//            // Ежемесячные показатели клиентской базы
//            $schedule->command(ClientsIndicatorsCommand::class)
//                ->monthlyOn(1, '04:00');
//        }

        if (config('app.roll_house_parser')) {
            // Парсер лидов для РХ
            $schedule->command(RollHouseCommand::class)
                ->everyMinute();
        }

        // Рассылка ВК
        if (config('app.vkusnyashka_mailing')) {
            $schedule->command(VkusnyashkaMailingCommand::class)
                ->hourly()
                ->between('9:00', '22:00');
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
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
