<?php
namespace App\Console\Commands;

// Модели
use App\Lead;
use App\User;

// Карбон
use Carbon\Carbon;

use Illuminate\Console\Command;

class ReportMonth extends Command
{
    /**
   * The name and signature of the console command.
   *
   * @var string
   */
    protected $signature = 'report:month';

    /**
   * The console command description.
   *
   * @var string
   */
    protected $description = 'Ежемесячный отчет';

    /**
   * Create a new command instance.
   *
   * @return void
   */
    public function __construct()
    {
        parent::__construct();
    }

    /**
   * Execute the console command.
   *
   * @return mixed
   */
    public function handle()
    {

        // Получаем лидов
        $leads = Lead::with('lead_method', 'lead_type', 'source_claim')
        ->whereMonth('created_at', Carbon::now()->format('m'))
        ->whereYear('created_at', Carbon::now()->format('Y'))
        ->whereNull('draft')
        ->get();

        $telegram_message = "Отчет за месяц (" . Carbon::now()->format('d.m.Y') . "):\r\n\r\n";

        if (count($leads) > 0) {
            $telegram_message .= "Обращения:\r\n\r\n";

            // Обычное
            $leads_regular = $leads->where('lead_type_id', 1);
            if (count($leads_regular) > 0) {
                $telegram_message .= "Обычное обращение: " . count($leads_regular) . "\r\n";

                // Групируем по методам и перебираем
                $grouped_leads_regular = $leads_regular->groupBy('lead_method.name');
                // dd($grouped_leads_regular);
                foreach ($grouped_leads_regular as $key => $value) {
                    $telegram_message .= "      " . $key . ": " . count($value) . "\r\n";
                }
                $telegram_message .= "\r\n";
            }

            // Сервисное
            $leads_service = $leads->where('lead_type_id', 3);
            if (count($leads_service) > 0) {
                $telegram_message .= "Сервисное обращение: " . count($leads_service) . "\r\n";

                // Считаем рекламации и обращения
                $claims_count = 0;
                $commercial_count = 0;

                // Групируем по методам и перебираем
                $grouped_leads_service = $leads_service->groupBy('lead_method.name');
                // dd($grouped_leads_regular);
                foreach ($grouped_leads_service as $key => $values) {
                    $telegram_message .= "  " . $key . ": " . count($values) . "\r\n";

                    foreach ($values as $value) {
                        if (isset($value->source_claim)) {
                            $claims_count++;
                        } else {
                            $commercial_count++;
                        }
                    }
                }

                // Выносим рекламации и коммерческие обращения
                if (($claims_count != 0) || ($commercial_count != 0)) {
                    $telegram_message .= "      ---\r\n";

                    if ($claims_count != 0) {
                        $telegram_message .= "         Рекламации: " . $claims_count . "\r\n";
                    }

                    if ($commercial_count != 0) {
                        $telegram_message .= "         Платный ремонт: " . $commercial_count . "\r\n";
                    }
                }
                $telegram_message .= "\r\n";
            }

            // Дилерское
            $leads_dealer = $leads->where('lead_type_id', 2);
            if (count($leads_dealer) > 0) {
                $telegram_message .= "Дилерское обращение: " . count($leads_dealer) . "\r\n";

                // Групируем по методам и перебираем
                $grouped_leads_dealer = $leads_dealer->groupBy('lead_method.name');
                // dd($grouped_leads_regular);
                foreach ($grouped_leads_dealer as $key => $value) {
                    $telegram_message .= "  " . $key . ": " . count($value) . "\r\n";
                }
                $telegram_message .= "\r\n";
            }

        } else {
            // Если обращений не было
            $telegram_message .= "Обращений не было ...";
            $telegram_message .= "\r\n";
        }

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
    }
}