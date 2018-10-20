<?php

namespace App\Telegram;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class ReportCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "report";

    /**
     * @var string Command Description
     */
    protected $description = "Список отчетов";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        
        $params['text'] = 'Выберите тип отчета:';
        $params['disable_notification'] = TRUE;
        $params['parse_mode'] = 'HTML';

        $day = ['text' => 'Текущий день', 'callback_data' => 'report_day'];
        $month = ['text' => 'Текущий месяц', 'callback_data' => 'report_month'];
        $year = ['text' => 'Текущий год', 'callback_data' => 'report_year'];
        
        $keyboard = ['inline_keyboard' => [
                [$day],
                [$month],
                [$year]
            ]
        ];
        $params['reply_markup'] = json_encode($keyboard, TRUE);

        $this->replyWithMessage($params);
        
        return 'ok';
        
    }
}