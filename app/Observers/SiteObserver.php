<?php

namespace App\Observers;

use App\Site;

use App\Observers\Traits\CommonTrait;

class SiteObserver
{
    use CommonTrait;

    public function creating(Site $site)
    {
        // Пока отсекаем по точке
        $site_alias = explode('.', $site->domain);
        $site->alias = $site_alias[0];
        // $site->slug = $site_alias[0];

        $site->api_token = \Str::random(60);

        $this->store($site);
    }

    public function updating(Site $site)
    {
        $site_alias = explode('.', $site->domain);
        $site->alias = $site_alias[0];
        // $site->slug = $site_alias[0];

        $this->update($site);
    }

    public function deleting(Site $site)
    {
        $this->destroy($site);
    }

    public function saved(Site $site)
    {
        $this->setFilials($site);
    }

    protected function setFilials(Site $site)
    {
        $request = request();
        $site->filials()->sync($request->filials);
    }
}
