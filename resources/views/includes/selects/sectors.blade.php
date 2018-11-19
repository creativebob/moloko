{{-- Селект с секторами (Вид деятельности компании) --}}
<label>Вид деятельности компании

	{{ Form::select('sector_id', $sectors_list, null) }}

</label>