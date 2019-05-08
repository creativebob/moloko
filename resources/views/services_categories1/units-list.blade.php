@foreach ($get_units_list as $unit)

<option value="{{ $unit->id }}">{{ $unit->name }}</option>

@endforeach