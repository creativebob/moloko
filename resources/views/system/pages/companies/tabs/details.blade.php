<div class="grid-x">
    <div class="cell small-12 large-5">
        <div class="grid-x grid-padding-x">
            <div class="cell small-12">
                <fieldset>
                    <legend>Официальное название (юридическое)</legend>
                        <div class="grid-x grid-padding-x">
                            <div class="small-2 medium-3 cell">
                                @include('includes.selects.legal_forms', ['value'=>$company->legal_form_id])
                            </div>
                            <div class="small-12 large-9 cell">
                                <label>Полное название компании
                                    @include('includes.inputs.name', ['value'=>$company->name_legal, 'name'=>'name_legal'])
                                </label>
                            </div>
                            <div class="small-12 large-6 cell">
                                <label>Короткий вариант названия
                                    @include('includes.inputs.name', ['value'=>$company->name_short, 'name'=>'name_short'])
                                </label>
                            </div>
                            <div class="small-6 medium-6 cell">
                                <label>Коммерческое обозначение (устаревшее поле)
                                    @include('includes.inputs.name', ['value'=>$company->designation, 'name' => 'designation'])
                                </label>
                            </div>
                        </div>
                </fieldset>

                <fieldset>
                    <legend>Юридический адрес</legend>
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

                <fieldset>
                    <legend>Дополнительные контакты:</legend>
                        <div class="small-12 medium-6 cell" id="extra-phones">
                            @if (count($company->extra_phones) > 0)
                                @foreach ($company->extra_phones as $extra_phone)
                                    @include('includes.extra-phone', ['extra_phone' => $extra_phone])
                                @endforeach
                            @else
                                @include('includes.extra-phone')
                            @endif
                        </div>
                </fieldset>
            </div>
        </div>
    </div>

    <div class="cell small-12 large-7">
        <div class="grid-x grid-padding-x">
            <div class="cell small-12">
                <fieldset>
                    <legend>Прочие реквизиты</legend>
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
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Банковские реквизиты</legend>
                    <div class="grid-x grid-padding-x">
                        <div class="small-12 cell" id="bank-accounts-list">

                            {{-- Подключаем банковские аккаунты --}}
                            @include('includes.bank_accounts.fieldset', ['company' => $company])

                        </div>
                    </div>
                </fieldset>
                @include('system.common.files.files', ['item' => $company])
            </div>
        </div>
    </div>
</div>
