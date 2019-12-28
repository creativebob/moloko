<?php

namespace App\Observers;

use App\Site;
use App\Observers\Traits\Commonable;

class SiteObserver
{
    use Commonable;

    public function creating(Site $site)
    {
        // Убираем последнее расширение после точки в домене, и чистим от лишних символов, чтоб получить алиас
//        $alias = preg_replace("/\.\w+$/","", $site->domain);
//        $slug = \Str::slug($alias, '');
//        $site->alias = $slug;

        $site->api_token = \Str::random(60);

        $this->store($site);
    }

    public function updating(Site $site)
    {
        $this->update($site);
    }

    public function deleting(Site $site)
    {
        $this->destroy($site);
    }

//    public function saved(Site $site)
//    {
//        $this->setFilials($site);
//    }
//
//    protected function setFilials(Site $site)
//    {
//        $request = request();
//        $site->filials()->sync($request->filials);
//    }
}
