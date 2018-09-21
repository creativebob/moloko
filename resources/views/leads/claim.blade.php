@foreach ($claims as $claim)
<tr class="item" id="claims-{{ $claim->id }}" data-name="{{ $claim->body }}">
	<td class="date">
		<span>{{ $claim->created_at->format('d.m.Y') }}</span>
		<br>{{ $claim->created_at->format('H:i') }}
	</td>
	<td class="case-number-claim">
		<span>
			@if(empty($claim->serial_number))
				Старый номер: {{ $claim->old_claim_id or ''}}
			@else
				{{ $claim->serial_number or 'Нет номера!' }}
			@endif
		</span>
	</td>
	<td class="lead-case-number-claim">
		<span>
			@if(!empty($claim->lead_case_number)){{ $claim->lead_case_number or '' }}@endif
		</span>
	</td>
	<td class="description">
		<span>
			@php
				echo str_replace("\n", '<br>', $claim->body);
			@endphp
		</span>
	</td>
	<td class="action">
		@if ($claim->status == 1)
		<a class="button finish-claim">Выполнить</a>
		@endif
	</td>
</tr>
@endforeach