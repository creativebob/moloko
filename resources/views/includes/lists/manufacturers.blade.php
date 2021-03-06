{{-- Чекер --}}


@if(!empty($manufacturers))

<div class="checkboxer-wrap {{$name}}">
	<div class="checkboxer-toggle" data-toggle="{{$name}}-dropdown-bottom-left" data-name="{{$name}}">
		<div class="checkboxer-title">
			<span class="title">{{ $title }}</span>
			<span class="count_filter_{{$name}}" id="count_filter_{{$name}}">({{$entity->$name ? $entity->$name->count() : 0}})</span>
		</div>
		<div class="checkboxer-button">
			<span class="sprite icon-checkboxer"></span>
		</div>
	</div>

	<div class="checkboxer-clean {{ $entity->$name ? $entity->$name->count() : 0 > 0 ? 'show-elem' : 'hide-elem' }}" onclick="event.stopPropagation()" data-name="{{$name}}">
		<span class="sprite icon-clean"></span>
	</div>

</div>

<div class="dropdown-pane checkboxer-pane hover {{$name}}" data-position="bottom" data-alignment="left" id="{{$name}}-dropdown-bottom-left" data-dropdown data-auto-focus="true" data-close-on-click="true" data-h-offset="-17" data-v-offset="1">

	<ul class="checkboxer-menu {{$name}}" data-name="{{$name}}">

		@foreach ($manufacturers as $manufacturer)
		<li class="checkbox">
			{{ Form::checkbox('manufacturers[]', $manufacturer->id, null, ['id'=>'manufacturer-'.$manufacturer->id, 'class'=>'manufacturer-checkbox']) }}
			<label for="manufacturer-{{ $manufacturer->id }}"><span>{{ $manufacturer->company->name }}</span></label>
		</li>
		@endforeach

	</ul>

</div>

<script type="application/javascript">

	{{$name}} = new CheckBoxer("{{$name}}", {{ $entity->$name ? $entity->$name->count() : 0 }});

	$(".checkboxer-menu.{{$name}} :checkbox").click(function() {
		{{$name}}.CheckBoxerAddDel(this);
	});

	$(".{{$name}} .checkboxer-toggle").click(function() {
		{{$name}}.CheckBoxerSetWidth(this);
	});

	$(".{{$name}} .checkboxer-clean").click(function() {
		{{$name}}.CheckBoxerClean();
	});

</script>
@endif