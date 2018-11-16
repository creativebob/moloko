<div class="small-12 cell callout small item" data-name="{{ $bank_account->bank->name }}" id="bank_accounts-{{ $bank_account->id }}">
	<div class="grid-x grid-padding-x">

		<div class="cell auto">
			Банк: {{ $bank_account->bank->name }}<br>БИК: {{ $bank_account->bank->bic }}<br>Р/С: {{ $bank_account->account_settlement }}<br>К/С: {{ $bank_account->account_correspondent }}
		</div>
		<div class="cell shrink">

			@can('update', $bank_account)
				<div class="icon-edit sprite" data-open="bank-account-edit"></div>
			@endcan

			@can ('delete', $bank_account)
				<div class="icon-delete sprite remove-bank-account" data-open="item-delete-ajax"></div>
			@endcan

		</div>
	</div>
</div>