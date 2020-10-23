<?php

namespace App\Observers\System;

use App\Template;

class TemplateObserver extends BaseObserver
{
    /**
     * Handle the template "creating" event.
     *
     * @param Template $template
     */
    public function creating(Template $template)
    {
        $this->store($template);
    }

    /**
     * Handle the template "updating" event.
     *
     * @param Template $template
     */
    public function updating(Template $template)
    {
        $this->update($template);
    }

    /**
     * Handle the template "deleting" event.
     *
     * @param Template $template
     */
    public function deleting(Template $template)
    {
        $this->destroy($template);
    }
}
