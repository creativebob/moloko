<select name="albums_category_id" id="select-albums_categories">
	@isset ($placeholder)
	<option value="0">Выберите категорию</option>
	@endisset

	{!! $albums_categories_tree!!}
</select>