{{-- Список стран --}}
<label>Страна

	{{ Form::select('country_id', $countries_list, $value) }}

</label>