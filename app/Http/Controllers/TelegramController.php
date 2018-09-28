<?php

namespace App\Http\Controllers;

use App\TelegramMessage;

use Illuminate\Http\Request;

use Telegram;

class TelegramController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $updates = Telegram::getUpdates();
        dd($updates);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $updates = Telegram::getUpdates();
        // dd($updates);
        
        $messages = [];
        foreach ($updates as $item) {
            
            $message_id = isset($item['message']['message_id']) ? $item['message']['message_id'] : null;
            $update_id = isset($item['update_id']) ? $item['update_id'] : null;
            
            $from_id = isset($item['message']['from']['id']) ? $item['message']['from']['id'] : null;
            $from_is_bot = isset($item['message']['from']['is_bot']) ? $item['message']['from']['is_bot'] : null;
            $from_first_name = isset($item['message']['from']['first_name']) ? $item['message']['from']['first_name'] : null;
            $from_last_name = isset($item['message']['from']['last_name']) ? $item['message']['from']['last_name'] : null;
            $from_username = isset($item['message']['from']['username']) ? $item['message']['from']['username'] : null;
            $from_language_code = isset($item['message']['from']['language_code']) ? $item['message']['from']['language_code'] : null;
            
            $chat_id = isset($item['message']['chat']['id']) ? $item['message']['chat']['id'] : null;
            $chat_first_name = isset($item['message']['chat']['first_name']) ? $item['message']['chat']['first_name'] : null;
            $chat_last_name = isset($item['message']['chat']['last_name']) ? $item['message']['chat']['last_name'] : null;
            $chat_username = isset($item['message']['chat']['username']) ? $item['message']['chat']['username'] : null;
            $chat_type = isset($item['message']['chat']['type']) ? $item['message']['chat']['type'] : null;
    
            $message = isset($item['message']['text']) ? $item['message']['text'] : null;
            $date_message = isset($item['message']['date']) ? $item['message']['date'] : null;
    
            $messages[] = [
                'message_id' => $message_id,
                'update_id' => $update_id,
    
                'from_id' => $from_id,
                'from_is_bot' => $from_is_bot,
                'from_first_name' => $from_first_name,
                'from_last_name' => $from_last_name,
                'from_username' => $from_username,
                'from_language_code' => $from_language_code,
    
                'chat_id' => $chat_id,
                'chat_first_name' => $chat_first_name,
                'chat_last_name' => $chat_last_name,
                'chat_username' => $chat_username,
                'chat_type' => $chat_type,
    
                'message' => $message,
                'date' => $date_message,
            ];
            
        }
        
        dd($messages);
        
        
        $telegram_messages = TelegramMessage::create($messages);
        
        
        
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
