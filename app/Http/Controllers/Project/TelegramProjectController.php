<?php

namespace App\Http\Controllers\Project;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Api;


use App\Http\Controllers\Controller;


class TelegramProjectController extends Controller
{

  public function me(){
    $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    $response = $telegram->getMe();
    return $response;
  }



  public function getHome()
  {
    return view('home');
  }

  public function getUpdates()
  {
    $updates = Telegram::getUpdates();
    dd($updates);
  }

  

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  // Бот отвечает сообщением, которое получил добавляя префикс "echo: "
  public function index(Request $request)
  {

    $updates = Telegram::getWebhookUpdates();

    // $updates = $request->updates;

  $text = $updates["message"]["text"]; //Текст сообщения
  $chat_id = $updates["message"]["chat"]["id"]; //Уникальный идентификатор пользователя

  $telegram = new Api(env('TELEGRAM-BOT-TOKEN')); // Устанавливаем токен, полученный у BotFather
  $telegram->sendMessage([
    'chat_id' => $chat_id,
    'text' => $text 
  ]);

  // $telegram->sendMessage([ 
  //   'chat_id' => '228265675',
  //   'text' => "Отправьте текстовое сообщение."
  // ]);

}

public function setWebHook(Request $request) {

  $telegram = new Api(env('TELEGRAM-BOT-TOKEN'));
  $res = $telegram->setWebhook(['url' => 'https://new.vkmars.ru/telegram']);
  dd($res);

}

public function webhook(Request $request){
  $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));

  $chatid = $request['message']['chat']['id'];
  $text = $request['message']['text'];

  switch($text) {
    case '/start':
    $this->showMenu($telegram, $chatid);
    break;
    case '/menu':
    $this->showMenu($telegram, $chatid);
    break;
    case '/website':
    $this->showWebsite($telegram, $chatid);
    break;
    case '/contact';
    $this->showContact($telegram, $chatid);
    break;
    default:
    $info = 'I do not understand what you just said. Please choose an option';
    $this->showMenu($telegram, $chatid, $info);
  }
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
        //
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
