<div class="reveal" id="add-bank-account-modal" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>РЕДАКТИРОВАНИЕ БАНКОВСКИХ СЧЕТОВ для {{ $company->name }}</h5>
        </div>
    </div>

    {{ Form::model($company, ['url' => '/admin/edit_bank_account/'.$bank_account->id, 'data-abide', 'novalidate', 'class' => '', 'id' => 'form-add-bank-account']) }}
    {{ method_field('PATCH') }}

		@include('includes.bank_accounts.form', ['submitButtonText' => 'Редактировать запись', 'param' => ''])
    	{{ Form::hidden('bank_account_id', $bank_account->id) }}

	    <div class="grid-x align-center">
	        <div class="small-6 medium-4 cell">
	            {{ Form::submit('Редактировать счета', ['class'=>'button modal-button', 'id' => 'submit-edit-bank-account']) }}
	        </div>
	    </div>
    {{ Form::close() }}
    <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>