<?php

namespace App\Observers\System;

use App\Process;

class ProcessObserver extends BaseObserver
{

    public function creating(Process $process)
    {
        $this->store($process);

        $process->draft = true;
    }

    public function created(Process $process)
    {
        $slug = $this->getProcessSlug($process);
        $process->update([
            'slug' => $slug
        ]);
    }

    public function updating(Process $process)
    {
        $this->update($process);

        $slug = $this->getProcessSlug($process);
        $process->slug = $slug;
    }

    public function getProcessSlug(Process $process)
    {
        $slug = null;
        if (empty($process->slug)) {
            $slug = \Str::slug($process->name);

            $found = Process::where([
                'company_id' => $process->company_id,
                'slug' => $slug
            ])
                ->exists();

            if ($found) {
                $slug .= "-{$process->id}";
            }
        } else {
            $slug = $process->slug;
            $found = Process::where([
                'company_id' => $process->company_id,
                'slug' => $slug
            ])
                ->exists();

            if ($found) {
                $slug .= "-{$process->id}";
            }
        }
        return $slug;
    }
}
