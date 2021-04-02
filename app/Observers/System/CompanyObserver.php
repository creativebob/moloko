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

        $legalForm = $this->cleanName($company);
        $company->legal_form_id = $legalForm->id ?? $request->legal_form_id ?? null;

        $companiesCount = Company::count();
        if ($companiesCount == 0) {
            $number = 1;
        } else {
            $number = $companiesCount + 1;
        }
        $company->alias = \Str::slug($company->name) . '-' . $number;

        if ($company->name_legal == null) {
            $company->legal_form_id = null;
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

        $legalForm = $this->cleanName($company);
        $company->legal_form_id = $legalForm->id ?? request()->legal_form_id ?? null;

        if ($company->name_legal == null) {
            $company->legal_form_id = null;
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


        $organization = \DB::table('organizations')->where([
            'company_id' => auth()->user()->company_id,
            'organization_id' => $company->id
        ])
        ->first();

        if (empty($organization)) {
            // Пишем связь с компанией
            $company->organizations()->attach($company->id, [
                'company_id' => auth()->user()->company_id,
            ]);
        }
    }

    /**
     * Очистка имени
     *
     * @param Company $company
     * @return |null
     */
    public function cleanName(Company $company)
    {

        $cleanCompanyName = str_replace('"', "", $company->name);
        $cleanCompanyName = str_replace('\'', "", $cleanCompanyName);

        $legalForms = LegalForm::get();

        $cleanCompanyNameLowerCase = mb_strtolower($cleanCompanyName, 'UTF-8');
        $item = null;
        foreach ($legalForms as $legalForm) {
            $valueLowerCase = mb_strtolower($legalForm->name, 'UTF-8');
            if (preg_match("/(^|\s)" . $valueLowerCase . "\s/i", $cleanCompanyNameLowerCase, $matches)) {
                $cleanCompanyNameLowerCase = str_replace($matches[0], "", $cleanCompanyNameLowerCase);
                $item = $legalForm;
            }
        }

        $cleanCompanyName = \Str::title($cleanCompanyNameLowerCase);
        $company->name = $cleanCompanyName;

        return $item;

//        foreach ($legalFormsList as $key => $value) {
//
//            if (preg_match("/(^|\s)" . $value . "\s/i", $company->name, $matches)) {
//                $company->name = str_replace($matches[0], "", $company->name);
//                $company->legal_form_id = $key;
//                $company->alias = \Str::slug($company->name) . '-' . $number;
//            } else {
//                $company->legal_form_id = $request->legal_form_id ?? 1;
//                $company->alias = \Str::slug($company->name) . '-' . $number;
//            }
//        }
    }
}
