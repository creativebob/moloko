
	<div class="grid-x grid-padding-x">
		@can ('index', App\BankAccount::class)

			 <!-- Если банковских аккаунтов 0 или 1, то отображаем поля для ввода -->
			@if($company->bank_accounts->count() == 0)
			    <div class="small-12 medium-3 cell">
			        <label>БИК
			            @include('includes.inputs.bic', ['value'=>($company->bank_accounts->first() != null) ? $company->bank_accounts->first()->bank->bic : null, 'name'=>'bank_bic', 'required'=>''])
			        </label>
			    </div>
			    <div class="small-12 medium-9 cell">
			        <label>Банк
			            @include('includes.inputs.bank', ['value'=>($company->bank_accounts->first() != null) ? $company->bank_accounts->first()->bank->name : null, 'name'=>'bank_name', 'required'=>''])
			        </label>
			    </div>
			    <div class="small-12 medium-6 cell">
			        <label>Р/С
			            @include('includes.inputs.account', ['value'=>($company->bank_accounts->first() != null) ? $company->bank_accounts->first()->account_settlement : null, 'name'=>'account_settlement', 'required'=>''])
			        </label>
			    </div>
			    <div class="small-12 medium-6 cell">
			        <label>К/С
			            @include('includes.inputs.account', ['value'=>($company->bank_accounts->first() != null) ? $company->bank_accounts->first()->account_correspondent : null, 'name'=>'account_correspondent', 'required'=>''])
			        </label>
			    </div>

			<!-- Если банковских аккаунтов более 1, то выводим их все блоками --> 
			@else
			    <div class="small-12 cell">
					<fieldset>
						<legend>Банковские счета:</legend>
						<div class="grid-x grid-padding-x" id="listing-bank-account"> 

								@can ('create', App\BankAccount::class)
							        @foreach($company->bank_accounts as $bank_account)

							        		@include('includes.bank_accounts.item', ['bank_account' => $bank_account])

							        @endforeach
								@endcan
							
						</div>

						@can ('create', App\BankAccount::class)
						<div class="grid-x grid-padding-x align-left">
							<div class="small-4 cell">
								@can ('update', $company)
									<a class="button green-button" data-open="open-form-bank-account">Добавить</a>
								@endcan
							</div>
						</div>
						@endcan

					</fieldset> 
			    </div>
			@endif
		@endcan
	</div>	

