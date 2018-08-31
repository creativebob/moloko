@foreach ($challenges as $challenge)
<tr class="item" id="challenges-{{ $challenge->id }}" data-name="{{ $challenge->description }}">
	<td>{{ $challenge->challenge_type->name }}</td>
	<td>{{ $challenge->deadline_date->format('d.m.Y') }}</td>
	<td>{{ $challenge->deadline_date->format('H:i') }}</td>
	<td>{{ $challenge->description }}</td>
	<td>{{ $challenge->appointed->second_name . ' ' . $challenge->appointed->first_name }}</td>
</tr>
@endforeach