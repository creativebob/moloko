<?php

namespace App\Observers;

use App\Process;

class ProcessObserver
{

    public function creating(Process $process)
    {

        $process->draft = true;

        $user = request()->user();

        $process->company_id = $user->company_id;
        $process->author_id = hideGod($user);
    }

    public function updating(Process $process)
    {
        $request = request();
        // dd($request);

        // Проверки только для черновика
        // if ($process->getOriginal('draft') == 1) {

        // }

        $process->editor_id = hideGod($request->user());

        // Cохраняем / обновляем фото
        $photo_id = savePhoto($request, $process);
        $process->photo_id = $photo_id;
    }
}
