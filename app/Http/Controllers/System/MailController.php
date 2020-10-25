<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{

    /**
     * MailController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Отправка почты
     */
    public function send()
    {
        $to_name = 'Любимому клиенту';
        $to_email = 'makc_berluskone@mail.ru';
        $data = [
            'name' => "Антон Павлович",
            "body" => "Мы хотим продать вам интересные штучки!"
        ];

        Mail::send('vkusnyashka/templates/emails/offers/newyear2021/index', $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)->subject('Новогодние подарки 2021');
            $message->from('smpcreativebob@gmail.com','Вкусняшка');
        });
    }
}
