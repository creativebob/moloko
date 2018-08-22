<ul class="menu vertical small-12 cell checkbox">

	@foreach($grouped_items as $categories)

	@foreach ($categories as $category)
	@if ($category->category_status == 1)

	<li>
		{{ Form::checkbox('test-', 1, null, ['id' => 'test-'.$category->id]) }}
		<label for="test-{{ $category->id }}"><span>{{ $category->name }}</span></label>

		<ul class="menu vertical nested">
			@if(isset($grouped_items[$category->id]))
			@include('includes.checkboxes-list.checkboxes-list', ['grouped_items' => $grouped_items, 'items' => $grouped_items[$category->id]])
			@endif
		</ul>
	</li>
	@endif
	@endforeach

	@endforeach

</ul>