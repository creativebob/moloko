<?php

namespace App\Http\Controllers;

// Модели
use App\TelegramMessage;

use App\Lead;
use App\Claim;
use App\User;

use Illuminate\Http\Request;

// Карбон
use Carbon\Carbon;

// Телеграм
use Telegram;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class TelegramController extends Controller
{

    public function get_bot()
    {
        $response = Telegram::getMe();
        
        dd($response);

        $botId = $response->getId();
        $firstName = $response->getFirstName();
        $username = $response->getUsername();
    }
    
    public function set_webhook()
    {
        $response = Telegram::setWebhook(['url' => 'https://vorotamars.ru/admin/telegram_message']);
        dd($response);
    }

    public function remove_webhook()
    {
        $response = Telegram::removeWebhook();
        dd($response);
    }
    
    public function get_updates()
    {
        $response = Telegram::getUpdates();
        dd($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get_message(Request $request)
    {

        $update = Telegram::commandsHandler(true);

        // if ($update['message']['text'] == 'го') {
        // }
        
        // Если нажали inline-нопку:
        if (isset($update['callback_query'])) {

            $access = User::has('staff')->where('telegram_id', $update['callback_query']['message']['chat']['id'])->first();

            if ($access) {

                switch ($update['callback_query']['data']) {
                    case 'report_day':
                    $leads = Lead::with('lead_method', 'lead_type', 'source_claim')
                    ->whereDate('created_at', Carbon::now()->format('Y-m-d'))
                    ->whereNull('draft')
                    ->get();

                    $telegram_message = "Отчет за день (" . getWeekDay(Carbon::now()) . ' ' . Carbon::now()->format('d.m.Y') . "):\r\n\r\n";

                    $claims = Claim::whereDate('created_at', Carbon::now()->format('Y-m-d'))
                    ->get();

                    break;

                    case 'report_month':
                    $leads = Lead::with('lead_method', 'lead_type', 'source_claim')
                    ->where('created_at', '>=', Carbon::now()->startOfMonth())
                    ->whereNull('draft')
                    ->get();

                    $telegram_message = "Отчет за месяц (" . getMonth(Carbon::now()) . "):\r\n\r\n";

                    $claims = Claim::where('created_at', '>=', Carbon::now()->startOfMonth())
                    ->get();
                    break;

                    case 'report_year':
                    $leads = Lead::with('lead_method', 'lead_type', 'source_claim')
                    ->whereYear('created_at', Carbon::now()->format('Y'))
                    ->whereNull('draft')
                    ->get();

                    $telegram_message = "Отчет за год (" . Carbon::now()->format('Y') . "):\r\n\r\n";

                    $claims = Claim::whereYear('created_at', Carbon::now()->format('Y'))
                    ->get();
                    break;

                    default:
                    Telegram::sendMessage([
                        'chat_id' => $update['callback_query']['message']['chat']['id'],
                        // именно в $update[message]['data'] - будет то что прописано у нажатой кнопки в качестве callback_data
                        'text' => "Вы нажали на кнопку с кодом: " . $update['callback_query']['data'], 
                    ]);
                    break;
                }

                if ($leads) {

                    if (count($leads) > 0) {
                        $telegram_message .= "Обращения:\r\n\r\n";

                        // Обычное
                        $leads_regular = $leads->where('lead_type_id', 1);
                        if (count($leads_regular) > 0) {
                            $telegram_message .= "Обычное: " . count($leads_regular) . "\r\n";

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
                            $telegram_message .= "Сервисное: " . count($leads_service) . "\r\n";

                            // Считаем рекламации и обращения
                            $claims_count = 0;
                            $commercial_count = 0;

                            // Групируем по методам и перебираем
                            $grouped_leads_service = $leads_service->groupBy('lead_method.name');
                            // dd($grouped_leads_regular);
                            foreach ($grouped_leads_service as $key => $values) {
                                $telegram_message .= "      " . $key . ": " . count($values) . "\r\n";

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
                                $telegram_message .= "         ---\r\n";

                                if ($claims_count != 0) {
                                    $telegram_message .= "         Внутренние: " . $claims_count . "\r\n";
                                }

                                if ($commercial_count != 0) {
                                    $telegram_message .= "         Внешние: " . $commercial_count . "\r\n";
                                }
                            }
                            $telegram_message .= "\r\n";


                        }

                        // Дилерское
                        $leads_dealer = $leads->where('lead_type_id', 2);
                        if (count($leads_dealer) > 0) {
                            $telegram_message .= "Дилерское: " . count($leads_dealer) . "\r\n";

                            // Групируем по методам и перебираем
                            $grouped_leads_dealer = $leads_dealer->groupBy('lead_method.name');
                            // dd($grouped_leads_regular);
                            foreach ($grouped_leads_dealer as $key => $value) {
                                $telegram_message .= "      " . $key . ": " . count($value) . "\r\n";
                            }
                            $telegram_message .= "\r\n";
                        }

                    } else {
                        // Если обращений не было
                        $telegram_message .= "Обращений не было ...";
                        $telegram_message .= "\r\n";
                    }

                }

                if (count($claims) > 0) {
                    $telegram_message .= "Рекламации: " . count($claims);
                    $telegram_message .= "\r\n";

                    $claims_in_work_count = $claims->where('status', 1)->count();
                    $claims_done_count = $claims->where('status', null)->count();

                    // Выносим рекламации и коммерческие обращения
                    if (($claims_in_work_count != 0) || ($claims_done_count != 0)) {

                        if ($claims_in_work_count != 0) {
                            $telegram_message .= "         В работе: " . $claims_in_work_count . "\r\n";
                        }

                        if ($claims_done_count != 0) {
                            $telegram_message .= "         Отработанные: " . $claims_done_count . "\r\n";
                        }
                        $telegram_message .= "\r\n";
                    } 
                }

                $leads_unaccepted_count = Lead::whereManager_id(1)->where('stage_id', '!=', 1)->count();

                if ($leads_unaccepted_count > 0) {
                    $telegram_message .= "Непринятые обращения: " . $leads_unaccepted_count;
                    $telegram_message .= "\r\n";  
                }

                $leads_potencial_count = Lead::where(['manager_id' => 1, 'stage_id' => 1])->count();

                if ($leads_potencial_count > 0) {
                    $telegram_message .= "Задачи по активным звонкам: " . $leads_potencial_count;
                    $telegram_message .= "\r\n";
                }

                Telegram::sendMessage([
                    'chat_id' => $update['callback_query']['message']['chat']['id'],
                    'text' => $telegram_message, 
                ]);

                // Отправляем телеграму отчет о получении, чтоб не дублировал ответы
                Telegram::answerCallbackQuery([
                    'callback_query_id' => $update['callback_query']['id']
                ]);
            } else {
                // $telegram_message = 'Отчеты охота? Давай ДОСВИДАНИЯ!';
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
