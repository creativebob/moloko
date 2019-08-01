{{-- Подключаем класс для работы--}}
@include('products.articles.goods.goods.class')

@if ($goods_categories->isNotEmpty())

@foreach($goods_categories as $goods_category)

@if ($goods_category->goods->isNotEmpty())
<li>
	<span class="parent" data-open="goods_category-{{ $goods_category->id }}">{{ $goods_category->name }}</span>
	<div class="checker-nested" id="goods_category-{{ $goods_category->id }}">
		<ul class="checker">

			@foreach($goods_category->goods as $cur_goods)
				@isset($cur_goods->article)
			<li class="checkbox">
				{{ Form::checkbox(null, $cur_goods->id, in_array($cur_goods->id, $article->goods->pluck('id')->toArray()), ['class' => 'add-goods', 'id' => 'goods-' . $cur_goods->id]) }}
				<label for="goods-{{ $cur_goods->id }}">
					<span>{{ $cur_goods->article->name }}</span>
				</label>
			</li>
				@endisset
			@endforeach

		</ul>
	</div>
</li>
@endif

@endforeach

@else
<li>Ничего нет...</li>
@endif

<script>

	goods = new Goods();

	// Чекбоксы
	$(document).on('click', "#dropdown-goods :checkbox", function() {
		goods.change(this);
	});

	// Удаление состав со страницы
	// Открываем модалку
	$(document).on('click', "#table-goods a[data-open=\"delete-item\"]", function() {
		goods.openModal(this);
	});

	// Удаляем
	$(document).on('click', '.item-delete-button', function() {
		let id = $(this).attr('id').split('-')[1];
		goods.delete(id);
	});

    // При клике на свойство отображаем или скрываем его состав
    $(document).on('click', '.parent', function() {
        // Скрываем все состав
        $('.checker-nested').hide();
        // Показываем нужную
        $('#' + $(this).data('open')).show();
    });
</script>

