<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TelegramController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        

        $output = json_decode(file_get_contents('php://input'), TRUE);
        
        $message_id = $output['message']['message_id']; if($message_id == ""){$message_id = "пусто";};
        $update_id = $output['update_id']; if($update_id == ""){$update_id = "пусто";};

        $f_chat_id = $output['message']['from']['id']; if($f_chat_id == ""){$f_chat_id = "пусто";};
        $f_first_name = $output['message']['from']['first_name']; if($f_first_name == ""){$f_first_name = "пусто";};
        $f_last_name = $output['message']['from']['last_name']; if($f_last_name == ""){$f_last_name = "пусто";};
        $f_username = $output['message']['from']['username']; if($f_username == ""){$f_username = "пусто";};

        $chat_id = $output['message']['chat']['id']; if($chat_id == ""){$chat_id = "пусто";};
        $first_name = $output['message']['chat']['first_name']; if($first_name == ""){$first_name = "пусто";};
        $last_name = $output['message']['chat']['last_name']; if($last_name == ""){$last_name = "пусто";};
        $username = $output['message']['chat']['username']; if($username == ""){$username = "пусто";};


        $message = $output['message']['text'];
        $date_message = $output['message']['date'];
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
