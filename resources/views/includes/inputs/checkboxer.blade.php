{{-- Чекбоксер 

--}}

<span class="link-city-filter" data-toggle="example-dropdown-bottom-left">Выбрать город</span>
<div class="dropdown-pane filter-select" data-position="bottom" data-alignment="left" id="example-dropdown-bottom-left" data-dropdown data-auto-focus="true" data-close-on-click="true">

	<ul class="supermenu">
		@foreach ($mass_value['collection'] as $key => $value)
			<li>
				{{ Form::checkbox('city_id[]', $value->city->id, $mass_value['mass_id'], ['id'=>$value->city->id]) }}
				<label for="{{ $value->city->id }}"><span>{{ $value->city->city_name }}</span></label>
			</li>
		@endforeach
	</ul>
</div>

<script type="text/javascript">
  
  // $(".toggle-go").click(function() {
  //     // alert('asdas');
  //     $(".mysupertab").toggleClass( "show_it" );
  //     $(".globalplace").toggleClass( "show_it" );
  //     $(".toggle-go").toggleClass( "item" );
  // });

  //     $( ".globalplace" ).click(function() {
  //       $(".mysupertab").toggleClass( "show_it" );
  //       $(".globalplace").toggleClass( "show_it" );
  //     });

</script>