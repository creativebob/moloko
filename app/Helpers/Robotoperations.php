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
function send_message($destinations, $message) {

	if (isset($destinations)) {

		// Отправляем на каждый telegram
		foreach ($destinations as $destination) {

			if (isset($destination->telegram_id)) {
				$response = Telegram::sendMessage([
					'chat_id' => $destination->telegram_id, 
					'text' => $message
				]);
			}
		}
	}


}



?>