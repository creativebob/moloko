<?php

namespace App\Observers;

use App\Company;

use App\Currency;
use App\Observers\Traits\Commonable;

class CompanyObserver
{

    use Commonable;

//    public function creating(Company $company)
//    {
//        $this->store($company);
//    }

    public function created(Company $company)
    {
        $company->load('currencies');
        if ($company->currencies->isEmpty()) {
            $ruble_id = Currency::where('abbreviation', 'руб.')->value('id');
            $company->currencies()->attach($ruble_id);
        }
    }
//
//    public function updating(Company $company)
//    {
//        $this->update($company);
//    }
//
//    public function deleting(Company $company)
//    {
//        $this->destroy($company);
//    }
}
