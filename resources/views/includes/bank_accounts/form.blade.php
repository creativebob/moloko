
    <div class="grid-x grid-padding-x align-center modal-content inputs">

        <div class="small-12 medium-3 cell">
            <label>БИК
                @include('includes.inputs.bic', ['value'=>$bank_company->bic, 'name'=>'bank_bic', 'required'=>'required'])
            </label>
        </div>
        <div class="small-12 medium-9 cell">
            <label>Банк
                @include('includes.inputs.name', ['value'=>$bank_company->name, 'name'=>'bank_name', 'required'=>'required'])
            </label>
        </div>

        <div class="small-12 medium-6 cell">
            <label>Р/С
                @include('includes.inputs.account', ['value'=>$bank_account->account_settlement, 'name'=>'account_settlement', 'required'=>'required'])
            </label>
        </div>
        <div class="small-12 medium-6 cell">
            <label>К/С
                @include('includes.inputs.account', ['value'=>$bank_account->account_correspondent, 'name'=>'account_correspondent', 'required'=>'required'])
            </label>
        </div>

    </div>


