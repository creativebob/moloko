<?php

namespace App\Observers;

use App\Domain;

use App\Observers\Traits\Commonable;

class DomainObserver
{

    use Commonable;

    public function creating(Domain $domain)
    {
        $this->store($domain);
    }

    public function updating(Domain $domain)
    {
        $this->update($domain);
    }

    public function deleting(Domain $domain)
    {
        $this->destroy($domain);
    }
}
