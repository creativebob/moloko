{{-- Список стран --}}
<label>Страна

	{{ Form::select($name ?? 'country_id', $countries_list, $value) }}

</label>
