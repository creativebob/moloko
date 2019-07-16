{{-- Подключаем класс для работы--}}
@include('products.articles.goods.raws.class')

@if ($raws_categories->isNotEmpty())

@foreach($raws_categories as $raws_category)

@if ($raws_category->raws->isNotEmpty())
<li>
	<span class="parent" data-open="raw_category-{{ $raws_category->id }}">{{ $raws_category->name }}</span>
	<div class="checker-nested" id="raw_category-{{ $raws_category->id }}">
		<ul class="checker">

			@foreach($raws_category->raws as $raw)
			<li class="checkbox">
				{{ Form::checkbox(null, $raw->id, in_array($raw->id, $article->raws->pluck('id')->toArray()), ['class' => 'add-raw', 'id' => 'raw-' . $raw->id]) }}
				@if(isset($raw->article))
					<label for="raw-{{ $raw->id }}">
						<span>{{ $raw->article->name }}</span>
					</label>
				@endif
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

<script type="application/javascript">

	let raws = new Raws();

	// Чекбоксы
	$(document).on('click', "#dropdown-raws :checkbox", function() {
		raws.change(this);
	});

	// Удаление состав со страницы
	// Открываем модалку
	$(document).on('click', "#table-raws a[data-open=\"delete-item\"]", function() {
		raws.openModal(this);
	});

	// Удаляем
	$(document).on('click', '.item-delete-button', function() {
		let id = $(this).attr('id').split('-')[1];
		raws.delete(id);
	});

    // При клике на свойство отображаем или скрываем его состав
    $(document).on('click', '.parent', function() {
        // Скрываем все состав
        $('.checker-nested').hide();
        // Показываем нужную
        $('#' + $(this).data('open')).show();
    });
</script>

