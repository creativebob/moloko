@foreach ($get_units_list as $unit)

<option value="{{ $unit->id }}" data-abbreviation="{{ $unit->abbreviation }}">{{ $unit->name }}</option>

@endforeach