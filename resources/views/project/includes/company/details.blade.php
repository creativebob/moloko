		@isset($company)
			<table class="table-company_details">
				<tr><td>Организация:</td><td>{{ $company->legal_form->name }} {{ $company->name }}</td></tr>
				<tr><td>Адрес:</td><td>{{ $company->location->zip_code }}, {{ $company->location->city->name }}, {{ $company->location->address }}</td></tr>
				<tr><td>ИНН:</td><td>{{ $company->inn }}</td></tr>
				<tr><td>ОГРН:</td><td>{{ $company->ogrn }}</td></tr>

				@if(!empty($company->bank_account))
					<tr><td>Банк:</td><td>{{ $company->bank_account->bank->name }}</td></tr>
					<tr><td>БИК:</td><td>{{ $company->bank_account->bank->bic }}</td></tr>
					<tr><td>Р/С:</td><td>{{ $company->bank_account->account_settlement }}</td></tr>
					<tr><td>К/С:</td><td>{{ $company->bank_account->account_correspondent }}</td></tr>
				@endif
			</table>
		@endisset