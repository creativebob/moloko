<div class="grid-x">
    <div class="cell small-12 large-5">
        <div class="grid-x grid-padding-x">

            <div class="small-12 medium-6 cell">
                <label>ИНН
                    @include('includes.inputs.inn_company', ['value' => $company->inn])
                </label>
            </div>
            <div class="small-12 medium-6 cell">
                <label>КПП
                    @include('includes.inputs.kpp', ['value'=>$company->kpp, 'name'=>'kpp'])
                </label>
            </div>
            <div class="small-12 medium-6 cell">
                <label>ОГРН
                    @include('includes.inputs.ogrn', ['value'=>$company->ogrn, 'name'=>'ogrn'])
                </label>
            </div>
            <div class="small-12 medium-6 cell">
                <label>ОКПО
                    @include('includes.inputs.okpo', ['value'=>$company->okpo, 'name'=>'okpo'])
                </label>
            </div>

            @if ($company->external_control == 0)
                <div class="small-12 medium-6 cell">
                    <label>Система налогообложения
                        @include('includes.selects.taxation_types', ['placeholder' => 'Не указана'])
                    </label>
                </div>
            @endif

            <div class="small-12 cell" id="bank-accounts-list">

                {{-- Подключаем банковские аккаунты --}}
                @include('includes.bank_accounts.fieldset', ['company' => $company])

            </div>

            <div class="small-12 cell">

                <fieldset>
                    <legend>Валюты</legend>
                    @include('includes.lists.currencies')
                </fieldset>


            </div>

        </div>
    </div>

    <div class="cell small-12 large-7">
        <div class="grid-x grid-padding-x">
            <div class="cell small-12">
                <fieldset>
                    <legend> Юридический адрес</legend>
                        <div class="grid-x grid-padding-x">
                            <div class="small-12 medium-6 cell">
                                @include('system.common.includes.city_search', ['item' => $company, 'required' => true, 'name' => 'legal_city_id', 'prefix' => 'legal'])

                                <label>Адрес
                                    @include('includes.inputs.address', ['value' => optional($company->legal_location)->address, 'name' => 'legal_address'])
                                </label>
                            </div>
                            <div class="small-12 medium-6 cell">
                                @include('includes.selects.countries', ['value' => optional($company->legal_location)->country_id, 'name' => 'legal_country_id'])

                                <label>Почтовый индекс
                                    @include('includes.inputs.zip_code', ['value' => optional($company->legal_location)->zip_code, 'name' => 'legal_zip_code'])
                                </label>
                            </div>
                        </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>
