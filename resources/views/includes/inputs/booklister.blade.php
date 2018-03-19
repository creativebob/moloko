{{-- Буклистер

--}}

@php
	$checkboxer_mass = $value;
@endphp

@if(!empty($checkboxer_mass[$name]))
<div class="checkboxer-wrap {{$name}}">
	<div class="checkboxer-toggle" data-toggle="{{$name}}-dropdown-bottom-left" data-name="{{$name}}">
		<div class="checkboxer-title">
			<span class="title">{{$checkboxer_mass[$name]['title']}}</span>
			<span class="count_filter_{{$name}}" id="count_filter_{{$name}}">({{$checkboxer_mass[$name]['count_mass']}})</span>
		</div>
		<div class="checkboxer-button">
			<span class="sprite icon-checkboxer"></span>
		</div>
	</div>

	@php
		if($checkboxer_mass[$name]['count_mass'] > 0){$show_status = 'show-elem';} else {$show_status = 'hide-elem';};
		$entity_name = $name . '_name';
	@endphp

	<div class="checkboxer-clean {{ $show_status }}" onclick="event.stopPropagation()" data-name="{{$name}}">
		<span class="sprite icon-clean"></span>
	</div>

</div>

<div class="dropdown-pane checkboxer-pane hover {{$name}}" data-position="bottom" data-alignment="left" id="{{$name}}-dropdown-bottom-left" data-dropdown data-auto-focus="true" data-close-on-click="true" data-h-offset="-17" data-v-offset="1">

	<ul class="checkboxer-menu {{$name}}" data-name="{{$name}}">

		@foreach ($checkboxer_mass[$name]['collection'] as $key => $value)
			<li>

				@if($checkboxer_mass[$name]['mode'] == 'id')

					{{ Form::checkbox($name . '_id[]', $value->$name->id, $checkboxer_mass[$name]['mass_id'], ['id'=>$name.'-'.$value->$name->id]) }}
					<label for="{{$name}}-{{ $value->$name->id }}"><span>{{ $value->$name->$entity_name }}</span></label>
				@else

					{{ Form::checkbox($name . '_id[]', $value->id, $checkboxer_mass[$name]['mass_id'], ['id'=>$name.'-'.$value->id]) }}
					<label for="{{$name}}-{{ $value->id }}">
						<span>{{ str_limit($value->$entity_name, $limit = 18, $end = ' ...') }}</span>
						<span title="Добавить позиции в список" class="booklist_button plus">+
							<span class="count_booklist_button" id="">14</span>
						</span>
						<span title="Исключить позиции из списка" class="booklist_button minus">-
							<span class="count_booklist_button">3</span>
						</span>
					</label>
				@endif

			</li>
		@endforeach
	</ul>

				<div class="input-group inputs">
				  {{-- <span class="input-group-label">Элементов: 34</span> --}}
				  <input class="input-group-field" type="text" name="new_booklist" id="new_booklist">
				  <input type="hidden" name="entity_alias" value="users">
				  <div class="input-group-button">
				    <a href="#" class="button" id="button_send_booklister">Создать список</a>
				  </div>
				</div>

</div>

<script type="text/javascript">

	let {{$name}} = new CheckBoxer("{{$name}}", {{$checkboxer_mass[$name]['count_mass']}});

  	$(".checkboxer-menu.{{$name}} :checkbox").click(function() {
		{{$name}}.CheckBoxerAddDel(this);
	});

  	$(".{{$name}} .checkboxer-toggle").click(function() {
		{{$name}}.CheckBoxerSetWidth(this);
	});

 	$(".{{$name}} .checkboxer-clean").click(function() {
		{{$name}}.CheckBoxerClean();
	});

  	var button_send_booklister = document.getElementById('button_send_booklister');

	button_send_booklister.onclick = function() {

  		var new_booklist_name = document.getElementById('new_booklist').value;
  		var entity_alias = $('#table-content').data('entity-alias');

		  $.ajax({

		    headers: {
		      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
		    url: '/setbooklist',
		    type: "POST",
		    data: {new_booklist_name: new_booklist_name, entity_alias: entity_alias},
		    success: function (html) {

		      // alert(html); 

			$('#{{$name}}-dropdown-bottom-left').foundation('_destroy');

			$(".checkboxer-menu.{{$name}} :checkbox").off( "click");
			$(".{{$name}} .checkboxer-toggle").off( "click");
			$(".{{$name}} .checkboxer-clean").off( "click");		
		    // alert(delete booklist);

		    $('#booklister').html(html);
		    var elem = $('#{{$name}}-dropdown-bottom-left');
		    var booklister = new Foundation.Dropdown(elem);
		    $(elem).foundation('open');

			{{$name}}.CheckBoxerSetWidth(elem);

		    }

		  });

  	};

</script>
@endif