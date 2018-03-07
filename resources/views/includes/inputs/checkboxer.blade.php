{{-- Чекбоксер 

--}}
<div class="checkboxer-wrap {{$name}}">
	<div class="checkboxer-toggle" data-toggle="{{$name}}-dropdown-bottom-left">
		<div class="checkboxer-title"> 
			<span class="title">Выбрать город</span>
			<span class="count_filter_{{$name}}" id="count_filter_{{$name}}">({{count($filter[$name]['mass_id'])}})</span>
		</div>
		<div class="checkboxer-button">
			<span class="sprite icon-checkboxer"></span>
		</div>
	</div>

	@php
		if(count($filter[$name]['mass_id'])>0){$show_status = 'show-elem';} else {$show_status = 'hide-elem';};
		$entity_name = $name . '_name';
	@endphp

	<div class="checkboxer-clean {{ $show_status }}">
		<span class="sprite icon-clean"></span>
	</div>

</div>

<div class="dropdown-pane checkboxer-pane hover" data-position="bottom" data-alignment="left" id="{{$name}}-dropdown-bottom-left" data-dropdown data-auto-focus="true" data-close-on-click="true" data-h-offset="-17" data-v-offset="1">

	<ul class="checkboxer-menu {{$name}}">
		@foreach ($filter[$name]['collection'] as $key => $value)
			<li>
				{{ Form::checkbox($name . '_id[]', $value->$name->id, $filter[$name]['mass_id'], ['id'=>$value->$name->id]) }}
				<label for="{{ $value->$name->id }}"><span>{{ $value->$name->$entity_name }}</span></label>
			</li>
		@endforeach
	</ul>
</div>

<script type="text/javascript">

  	// Получаем количество элементов фильтра (городов)
	var count_filter_{{$name}} = {{count($filter[$name]['mass_id'])}};
	if(count_filter_{{$name}} > 0){
		CheckBoxerDelShow();
	};

 	$(".{{$name}} .checkboxer-clean").click(function() {
		CheckBoxerClean();
	});

 	// Функция скрытия кнопки удаления и очистки чекбоксов
	function CheckBoxerClean(){
		$('.checkboxer-menu.{{$name}} :checkbox').removeAttr("checked")
		$('.{{$name}} .checkboxer-clean').addClass('hide-elem');
		$('.{{$name}} .checkboxer-title').css("width", "169px");
		count_filter_{{$name}} = 0;
		$('#count_filter_{{$name}}').html('('+ count_filter_{{$name}} +')');
	}

 	// Функция отображения кнопки удаления
	function CheckBoxerDelShow(){

		$('.{{$name}} .checkboxer-clean').removeClass('hide-elem');
		$('.{{$name}} .checkboxer-title').css("width", "146px");
	}

 	// Функция вычисления количества включенных чекбоксов и отображения их в поле
	function CheckBoxerAddDel(elem){

		CheckBoxerDelShow();

		if($(elem).prop('checked')){
			count_filter_{{$name}} = count_filter_{{$name}} + 1;
			$('#count_filter_{{$name}}').html('('+ count_filter_{{$name}} +')');
		} else {
			count_filter_{{$name}} = count_filter_{{$name}} - 1;
			$('#count_filter_{{$name}}').html('('+ count_filter_{{$name}} +')');

			if(count_filter_{{$name}} == 0){
				$('.checkboxer-menu.{{$name}} :checkbox').removeAttr("checked")
				$('.{{$name}} .checkboxer-clean').addClass('hide-elem');
				$('.{{$name}} .checkboxer-title').css("width", "169px");
			};

		};
	}

  	$(".checkboxer-menu.{{$name}} :checkbox").click(function() {
		CheckBoxerAddDel(this);
	});

</script>