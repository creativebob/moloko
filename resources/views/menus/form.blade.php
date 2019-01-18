{!! Form::hidden('navigation_id', $navigation_id, []) !!}

<label>Введите ссылку
	@include('includes.inputs.text-en', ['name' => 'alias'])
</label>

<label>Страница:
	<select name="page_id" class="pages-select" placeholder="Не выбрано">
		<option value="">Не выбрано</option>
		{{-- @php
		echo $pages_list;
		@endphp --}}
	</select>
</label>

{{-- <label>Тег
	@include('includes.inputs.text-en', [
		'name' => 'tag',
		'check' => true
	]
	)
	<div class="sprite-input-right find-status"></div>
	<div class="item-error">Такой тег уже существует!</div>
</label> --}}


