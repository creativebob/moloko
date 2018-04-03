{{-- Буклистер

--}}

@php
	$checkboxer_mass = $value;
	$default_count = $checkboxer_mass['booklist']['booklists']['default_count'];
	$main_mass = $checkboxer_mass[$name]['collection']->sortByDesc('id');
	$request_mass = $checkboxer_mass['booklist']['booklists']['request_mass'];
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


	<div class="input-group inputs @if ($default_count == 0) hide-elem @endif ">
	  <span class="input-group-label">Выбрано элементов: {{$default_count}}</span>
	  <input class="input-group-field" type="text" name="new_booklist" id="new_booklist" autocomplete="off">
	  <input type="hidden" name="entity_alias" value="users">
	  <div class="input-group-button">
	    <a href="#" class="button" id="button_send_booklister" onclick="button_send_booklister();">Создать список</a>
	  </div>
	</div>

	<ul class="checkboxer-menu {{$name}}" data-name="{{$name}}">

		@foreach ($main_mass as $key => $value)
			<li class="item" id="booklists-{{$value->id}}" data-name="{{$value->$entity_name}}">

				@if($value->$entity_name != 'Default')

					@php
					 	$checked = '';
						if(isset($request_mass)){

							if(in_array($value->id, $request_mass)){$checked = 'checked';};

						};

					@endphp

					{{ Form::checkbox($name . '_id[]', $value->id, $checkboxer_mass[$name]['mass_id'], ['id'=>$name.'-'.$value->id, $checked]) }}
					<label for="{{$name}}-{{ $value->id }}">
						<span class="rename_field_{{$value->id}} dblrename" data-booklist_id="{{$value->id}}">

							<span class="text_rename">{{ str_limit($value->$entity_name, $limit = 30, $end = ' ...')}}</span>
							<span class="count_rename"> ({{$checkboxer_mass['booklist']['booklists'][$value->id]['mass_count']}})</span>

						</span>
					</label>

					<span title="Добавить позиции в список" class="booklist_button plus" data-booklist_id_send="{{$value->id}}">+
						<span class="count_booklist_button">{{ $checkboxer_mass['booklist']['booklists'][$value->id]['plus'] }}</span>
					</span>

					<span title="Исключить позиции из списка" class="booklist_button minus" data-booklist_id_send="{{$value->id}}">-
						<span class="count_booklist_button">{{ $checkboxer_mass['booklist']['booklists'][$value->id]['minus'] }}</span>
					</span>

					<em class="icon-delete sprite booklist_delete" data-booklist_id = "{{$value->id}}" aria-controls="item-delete" aria-haspopup="true" tabindex="0"></em>

				@endif

			</li>
		@endforeach
	</ul>

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


 	// Удаление элемента из Буклиста
  	$(".booklist .booklist_delete").click(function() {

  			// var booklist_id = $('.booklist_delete').data('booklist_id');
  			// var entity_alias = $('#table-content').data('entity-alias');

			var entity_alias = $(this).closest('.item').attr('id').split('-')[0];
			var booklist_id = $(this).closest('.item').attr('id').split('-')[1];

  			$.ajax({

		    headers: {
		      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
		    url: '/booklists/' + booklist_id,
		    type: "DELETE",
		    data: {entity_alias:entity_alias},
		    success: function (html) {

				cleanBooklister();
		    	$('#booklists').html(html);
		    	iniBooklister();

		    }

		  });

	});


  	// Добавление или исключение отмеченных элементов в Буклист
  	$(".booklist_button").click(function() {

  		if($(this).hasClass('plus')){var operation_booklist = "plus";};
  		if($(this).hasClass('minus')){var operation_booklist = "minus";};
  		var booklist_id_send = $(this).data('booklist_id_send');
  		var entity_alias = $('#table-content').data('entity-alias');
  		var count_elem = $(this).children('span').text();
  		
  		// Делаем запрос только если элемент не равен нулю
  		if(count_elem != 0){

  			$.ajax({

		    headers: {
		      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
		    url: '/setbooklist',
		    type: "POST",
		    data: {entity_alias:entity_alias, operation_booklist:operation_booklist, booklist_id_send:booklist_id_send},
		    success: function (html) {

				cleanBooklister();
		    	$('#booklists').html(html);
		    	iniBooklister();
		    }

		  });
  		}
	});

  	// Создание нового буклиста
	function button_send_booklister() {

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

				cleanBooklister();
		    	$('#booklists').html(html);
		    	iniBooklister();
		    	cleanAllCheckboxes();
		    }

		  });
  	};

  	// Подгрузка буклиста
  	$(".checkboxer-wrap.booklist").click(function() {

	  		if($('.booklist').hasClass('is-open')){var booklist_open = true;} else {var booklist_open = false;};

	  		// Проверка на наличие изменений. 
  			if(counter_checkbox != storage_counter_checkbox){

  			// Маркер изменений. Фиксируем изменения, чтоб в следующий раз не делать запрос
  			// при отсутствии изменений
  			storage_counter_checkbox = counter_checkbox;

  			var booklist_id = $('.booklist input:checkbox:checked').map(function() {return this.value;
  			}).get();

  			// alert(booklist_mass);


				$('#{{$name}}-dropdown-bottom-left').foundation('_destroy');
  				var entity_alias = $('#table-content').data('entity-alias');
				$.ajax({

					headers: {
					    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: '/updatebooklist',
					type: "GET",
					data: {entity_alias: entity_alias, booklist_id: booklist_id},
					success: function (html) {

						// alert(html);

						cleanBooklister();
					    $('#booklists').html(html);

						var elem = $('#{{$name}}-dropdown-bottom-left');
						var booklister = new Foundation.Dropdown(elem);

					   	if(booklist_open == false){
					    	$(elem).foundation('open');
					    };

						{{$name}}.CheckBoxerSetWidth(elem);
					}
				});
		 	};
	});

  	$(".checkboxer-wrap.booklist").click(function() {

	  		if($('.booklist').hasClass('is-open')){var booklist_open = true;} else {var booklist_open = false;};

	  		// Проверка на наличие изменений. 
  			if(counter_checkbox != storage_counter_checkbox){

  			// Маркер изменений. Фиксируем изменения, чтоб в следующий раз не делать запрос
  			// при отсутствии изменений
  			storage_counter_checkbox = counter_checkbox;

  			var booklist_id = $('.booklist input:checkbox:checked').map(function() {return this.value;
  			}).get();

  			// alert(booklist_mass);


				$('#{{$name}}-dropdown-bottom-left').foundation('_destroy');

  				var entity_alias = $('#table-content').data('entity-alias');

				$.ajax({

					headers: {
					    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: '/updatebooklist',
					type: "GET",
					data: {entity_alias: entity_alias, booklist_id: booklist_id},
					success: function (html) {

						// alert(html);

						cleanBooklister();
					    $('#booklists').html(html);

						var elem = $('#{{$name}}-dropdown-bottom-left');
						var booklister = new Foundation.Dropdown(elem);

					   	if(booklist_open == false){
					    	$(elem).foundation('open');
					    };

						{{$name}}.CheckBoxerSetWidth(elem);
					}
				});
		 	};
	});



  	$(".dblrename").dblclick(function() {
  	// $(document).on('dblclick', '.dblrename', function() {

  		// Собираем данные
  		var booklist_id = $(this).data('booklist_id');
  		var text_rename = $('.rename_field_' + booklist_id + ' .text_rename');
  		var count_rename = $('.rename_field_' + booklist_id + ' .count_rename').text();

  		// Создаем и вставляем Input для переименования
  		var elem = '<input type="text" placeholder="Имя списка" value="' + text_rename.text() + '" id="rename_input_' + booklist_id + '">';
  		$('.rename_field_' + booklist_id).html(elem);
		$('#rename_input_' + booklist_id).focus();

  		// Создаем функцию на потерю фокуса
		$('#rename_input_' + booklist_id).focusout(function(){
			var user_text = $(this).val();
			$('#rename_input_' + booklist_id).remove();
  			var elem2 = '<span class="text_rename">' + user_text + '</span><span class="count_rename">' + count_rename + '</span>';
  			$('.rename_field_' + booklist_id).html(elem2);
		});


	  	// 	if($('.booklist').hasClass('is-open')){var booklist_open = true;} else {var booklist_open = false;};


  		// 	// Маркер изменений. Фиксируем изменения, чтоб в следующий раз не делать запрос
  		// 	// при отсутствии изменений
  		// 	storage_counter_checkbox = counter_checkbox;

  		// 	var booklist_id = $('.booklist input:checkbox:checked').map(function() {return this.value;
  		// 	}).get();

  		// 	// alert(booklist_mass);


				// $('#{{$name}}-dropdown-bottom-left').foundation('_destroy');

  		// 		var entity_alias = $('#table-content').data('entity-alias');
  				
				// $.ajax({

				// 	headers: {
				// 	    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				// 	},
				// 	url: '/updatebooklist',
				// 	type: "GET",
				// 	data: {entity_alias: entity_alias, booklist_id: booklist_id},
				// 	success: function (html) {

				// 		// alert(html);

				// 		cleanBooklister();
				// 	    $('#booklists').html(html);

				// 		var elem = $('#{{$name}}-dropdown-bottom-left');
				// 		var booklister = new Foundation.Dropdown(elem);

				// 	   	if(booklist_open == false){
				// 	    	$(elem).foundation('open');
				// 	    };

				// 		{{$name}}.CheckBoxerSetWidth(elem);
				// 	}
				// });
	});


  	// Инициализация Буклиста
  	function iniBooklister(){

		    var elem = $('#{{$name}}-dropdown-bottom-left');
		    var booklister = new Foundation.Dropdown(elem);
		    $(elem).foundation('open');
			{{$name}}.CheckBoxerSetWidth(elem);
  	};

  	// Удаление Буклиста
   	function cleanBooklister(){

			$('#{{$name}}-dropdown-bottom-left').foundation('_destroy');
			$(".checkboxer-menu.{{$name}} :checkbox").off( "click");
			$(".{{$name}} .checkboxer-toggle").off( "click");
			$(".{{$name}} .checkboxer-clean").off( "click");		
  	}; 	

	$(document).ready(function() {
	      $('#filter-form #new_booklist').keydown(function(event){
	        if(event.keyCode == 13) {
		        event.preventDefault();
				button_send_booklister();
		        return false;
	      }
	   });
	});

	var count_default_booklist = @if(isset($default_count)){{$default_count}} @else 0 @endif;

</script>
@endif