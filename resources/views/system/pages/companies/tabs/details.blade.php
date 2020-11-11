<div class="grid-x">
    <div class="cell small-12 large-5">
        <div class="grid-x grid-padding-x">

            <div class="small-12 medium-6 cell">
                <label>ИНН
                    @include('includes.inputs.inn', ['value' => $company->inn])
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
</div>
