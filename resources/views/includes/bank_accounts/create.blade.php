<div class="reveal" id="add-bank-account-modal" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>ДОБАВЛЕНИЕ БАНКОВСКИХ СЧЕТОВ для {{ $company->name }}</h5>
        </div>
    </div>

	{{ Form::open(['url' => '/admin/create_bank_account', 'data-abide', 'data-live-validate' => 'true', 'novalidate', 'id' => 'form-add-bank-account']) }}

		@include('includes.bank_accounts.form', ['param' => ''])

    	{{ Form::hidden('company_id', $company->id) }}

	    <div class="grid-x align-center">
	        <div class="small-6 medium-4 cell">
	            {{ Form::submit('Добавить счета', ['class'=>'button modal-button', 'id' => 'submit-add-bank-account']) }}
	        </div>
	    </div>

    {{ Form::close() }}
    <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
