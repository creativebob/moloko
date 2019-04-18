@if ($categories->isNotEmpty())
@foreach($categories as $category)
@if ($category->articles->isNotEmpty())
<li>
	<span class="parent" data-open="composition_category-{{ $category->id }}">{{ $category->name }}</span>
	<div class="checker-nested" id="composition_category-{{ $category->id }}">
		<ul class="checker">

			@foreach($category->articles as $composition)
			<li class="checkbox">
				{{ Form::checkbox('compositions[]', $composition->id, null, ['class' => 'change-composition', 'id' => 'composition-'.$composition->id]) }}
				<label for="composition-{{ $composition->id }}">
					<span>{{ $composition->name }}</span>
				</label>
			</li>
			@endforeach

		</ul>
	</div>
</li>
@endif
@endforeach
@else
<li>Ничего нет...</li>
@endif

<script type="text/javascript">

	let compositions = new Compositions();

	// Чекбоксы
	$(document).on('click', "#compositions-dropdown :checkbox", function() {
		compositions.change(this);
	});

	// Удаление состав со страницы
	// Открываем модалку
	$(document).on('click', "#compositions-table a[data-open=\"delete-composition\"]", function() {
		compositions.openModal(this);
	});

	// Удаляем
	$(document).on('click', '.composition-delete-button', function() {
		let id = $(this).attr('id').split('-')[1];
		compositions.deleteComposition(id);
	});

    // При клике на свойство отображаем или скрываем его состав
    $(document).on('click', '.parent', function() {
        // Скрываем все состав
        $('.checker-nested').hide();
        // Показываем нужную
        $('#' + $(this).data('open')).show();
    });

</script>

