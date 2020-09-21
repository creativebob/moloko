<?php

namespace App\Observers\System;

use App\Company;

use App\Currency;
use App\LegalForm;
use App\Observers\System\Traits\Commonable;

class CompanyObserver
{

    use Commonable;

    /**
     * Handle the company "creating" event.
     *
     * @param Company $company
     */
    public function creating(Company $company)
    {
        $answer = operator_right($company->getTable(), false, getmethod(__FUNCTION__));
        if ($answer['automoderate'] == false) {
            $company->moderation = true;
        }

        $request = request();

        $company->display = $request->get('display', true);
        $company->system = $request->get('system', false);

        // TODO - 15.09.20 - Думаю стоит в компанию всегда автором писать id робота
        $company->author_id = 1;

        $companiesCount = Company::count();

        // Вычисляем номер для использования в алиасе
        if ($companiesCount == 0) {
            $number = 1;
        } else {
            $number = $companiesCount + 1;
        }

        $legalFormsList = LegalForm::get()
            ->pluck('name', 'id');

        foreach ($legalFormsList as $key => $value) {

            if (preg_match("/(^|\s)" . $value . "\s/i", $company->name, $matches)) {
                $company->name = str_replace($matches[0], "", $company->name);
                $company->legal_form_id = $key;
                $company->alias = \Str::slug($company->name) . '-' . $number;
            } else {
                $company->legal_form_id = $request->legal_form_id ?? 1;
                $company->alias = \Str::slug($company->name) . '-' . $number;
            }
        }
    }

    /**
     * Handle the company "updating" event.
     *
     * @param Company $company
     */
    public function updating(Company $company)
    {
        if (auth()->user()->company_id == $company->id) {
            $this->update($company);
        } else {
            $company->editor_id = 1;
        }
    }

    /**
     * Handle the company "deleting" event.
     *
     * @param Company $company
     */
    public function deleting(Company $company)
    {
        $this->destroy($company);
    }

    /**
     * Handle the company "saved" event.
     *
     * @param Company $company
     */
    public function saved(Company $company)
    {
        $company->load('currencies');
        if ($company->currencies->isEmpty()) {
            $rubleId = Currency::where('abbreviation', 'руб.')
                ->value('id');
            $company->currencies()->attach($rubleId);
        }
    }
}
