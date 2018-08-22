@foreach ($items as $item)
<li>
	{{ Form::checkbox('test-', 1, null, ['id' => 'test-'.$item->id]) }}
	<label for="test-{{ $item->id }}"><span>{{ $item->name }}</span></label>

	@if (isset($grouped_items[$item->id]))

	@if(isset($grouped_items[$item->id]))
	<ul class="menu vertical nested">
		
		@include('includes.checkboxes-list.checkboxes-list', ['grouped_items' => $grouped_items, 'items' => $grouped_items[$item->id]])
		
	</ul>
	@endif

	@endif
</li>
@endforeach