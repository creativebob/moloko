@foreach ($challenges as $challenge)
<tr class="item @if($challenge->deadline_date < Carbon\Carbon::now()) deadline-active @endif" id="challenges-{{ $challenge->id }}" data-name="{{ $challenge->description }}">
	<td class="name"><span>{{ $challenge->challenge_type->name }}</span><br>Для: {{ $challenge->appointed->second_name . ' ' . $challenge->appointed->first_name }}</td>

	<td class="date"><span>{{ $challenge->deadline_date->format('d.m.Y') }}</span>
		<br>{{ $challenge->deadline_date->format('H:i') }}
	</td>

	<td class="description">
		@php
			echo str_replace("\n", '<br>', $challenge->description);
		@endphp
	</td>

	<td class="action">
		@if ($challenge->appointed_id == Auth::user()->id) 
		<a class="button finish-challenge">Выполнить</a>
		@endif

		@if (($challenge->author_id == Auth::user()->id) && ($challenge->appointed_id != Auth::user()->id))
		<a class="button remove-challenge">Снять</a>
		@endif
	</td>
</tr>
@endforeach