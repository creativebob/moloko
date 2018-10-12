<?php

namespace App\Http\Controllers;

// Модели
use App\TelegramMessage;

use Illuminate\Http\Request;

// Карбон
use Carbon\Carbon;

// Телеграм
use Telegram;

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
  //    if (!class_exists('Bootstrap\Telegram\Commands\StartCommand::class')) {
        //     dd('нету');
        // }
        
        // $message = Telegram::commandsHandler(true);
        
        
        // $message = Telegram::getWebhookUpdates();
        // dd($item);
        
        // if (isset($message['message'])) {
            
        //     $text = $message['message']['text'];
            
            
        //     switch ($text) {
        //         case '/report':
        //         $keyboard = [
        //             ['Маркетинговый отчет'],
        //             ['Финансовый отчет'],
        //             ['Производственный отчет'],
        //             ['Сброс']
        //         ];
                
        //         $reply_markup = Telegram::replyKeyboardMarkup([
        //             'keyboard' => $keyboard, 
        //             'resize_keyboard' => true, 
        //             'one_time_keyboard' => true
        //         ]);
                
        //         $response = Telegram::sendMessage([
        //             'chat_id' => $message['message']['chat']['id'], 
        //             'reply_markup' => $reply_markup
        //         ]);
                
        //         // $messageId = $response->getMessageId();
        //         break;
                
        //         case 'Доступ':
                
        //         $tel_msg = new TelegramMessage;
                
        //         $tel_msg->message_id = isset($message['message']['message_id']) ? $message['message']['message_id'] : null;
        //         $tel_msg->update_id = isset($message['update_id']) ? $message['update_id'] : null;
                
        //         $tel_msg->from_id = isset($message['message']['from']['id']) ? $message['message']['from']['id'] : null;
        //         $tel_msg->from_is_bot = isset($message['message']['from']['is_bot']) ? $message['message']['from']['is_bot'] : null;
        //         $tel_msg->from_first_name = isset($message['message']['from']['first_name']) ? $message['message']['from']['first_name'] : null;
        //         $tel_msg->from_last_name = isset($message['message']['from']['last_name']) ? $message['message']['from']['last_name'] : null;
        //         $tel_msg->from_username = isset($message['message']['from']['username']) ? $message['message']['from']['username'] : null;
        //         $tel_msg->from_language_code = isset($message['message']['from']['language_code']) ? $message['message']['from']['language_code'] : null;
                
        //         $tel_msg->chat_id = isset($message['message']['chat']['id']) ? $message['message']['chat']['id'] : null;
        //         $tel_msg->chat_first_name = isset($message['message']['chat']['first_name']) ? $message['message']['chat']['first_name'] : null;
        //         $tel_msg->chat_last_name = isset($message['message']['chat']['last_name']) ? $message['message']['chat']['last_name'] : null;
        //         $tel_msg->chat_username = isset($message['message']['chat']['username']) ? $message['message']['chat']['username'] : null;
        //         $tel_msg->chat_type = isset($message['message']['chat']['type']) ? $message['message']['chat']['type'] : null;
                
        //         $tel_msg->message = isset($message['message']['text']) ? $message['message']['text'] : null;
        //         $tel_msg->date = isset($message['message']['date']) ? $message['message']['date'] : null;
                
        //         $tel_msg->save();
                
        //         if ($tel_msg) {
        //             $response = Telegram::sendMessage([
        //                 'chat_id' => $tel_msg->chat_id, 
        //                 'text' => 'Ваш Telegram ID: '.$tel_msg->chat_id
        //             ]);
        //         } else {
        //             dd('не вышло');
        //         }
        //         break;
                
        //         case 'Лол':
        //         $response = Telegram::sendMessage([
        //             'chat_id' => $message['message']['chat']['id'], 
        //             'text' => 'Ты как кек: '.$message['message']['text']
        //         ]);
        //         break;
                
        //         default:

        //         $response = Telegram::sendMessage([
        //             'chat_id' => $message['message']['chat']['id'], 
        //             'text' => 'Я конечно извиняюсь, но такое обращение для меня непонятно: "'.$text.'"... Лучше ознакомтесь с доступными командами - /help'
        //         ]);
        //         break;
        //     }
            
        // } else {
        //     dd('ничео');
        // }
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
