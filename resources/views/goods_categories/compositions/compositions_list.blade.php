@if ($raws_categories->isNotEmpty())
@foreach($raws_categories as $raws_category)
@if ($raws_category->raws->isNotEmpty())
<li>
	<span class="parent" data-open="composition_category-{{ $raws_category->id }}">{{ $raws_category->name }}</span>
	<div class="checker-nested" id="composition_category-{{ $raws_category->id }}">
		<ul class="checker">

			@foreach($raws_category->raws as $composition)
			<li class="checkbox">
				{{ Form::checkbox('compositions[]', $composition->id, null, ['class' => 'change-composition', 'id' => 'composition-'.$composition->id]) }}
				<label for="composition-{{ $composition->id }}">
					<span>{{ $composition->article->name }}</span>
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

