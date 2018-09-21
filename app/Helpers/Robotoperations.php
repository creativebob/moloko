<?php
use App\Note;
use App\Challenge;

// Сохраняем системный комментарий
function add_note($item, $body){

	$note = new Note;

	$note->body = $body;
	$note->company_id = $item->company_id;
	$note->author_id = 1;
	$note->save();

	$item->notes()->save($note);

}

// Отправляем сообщение в телеграмм
function send_message($telegram_destinations, $telegram_message){

    // Отправляем на каждый telegram
	foreach ($telegram_destinations as $destination) {

		$response = Telegram::sendMessage([
			'chat_id' => $destination->telegram_id, 
			'text' => $telegram_message
		]);
	}

}



?>