@foreach ($units as $unit)
<option value="{{ $unit->id }}" data-abbreviation="{{ $unit->abbreviation }}">{{ $unit->name }}</option>
@endforeach