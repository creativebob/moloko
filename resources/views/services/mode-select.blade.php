<a id="mode-default" class="modes up-input-button">Вернуться</a>
<label>Группа услуг
	@if (isset($services_products_list))
	{{ Form::select('services_product_id', $services_products_list, null, ['id' => 'services-products-list']) }}
	@else
	<select name="product_id" id="services-products-list" required disabled></select>
	@endif
</label>

<label>Название услуги
	@include('includes.inputs.string', ['value'=>null, 'name'=>'name', 'required'=>'required'])
	<div class="item-error">Такая услуга уже существует!</div>
</label>

{{ Form::hidden('mode', 'mode_select') }}

<script type="text/javascript">

	// $.ajax({
 //        headers: {
 //          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
 //        },
 //        url: '/admin/ajax_services_count',
 //        type: "POST",
 //        data: {id: $('#services-categories-list').val(), entity: 'services'},
 //        success: function(html){
 //        // alert(html);
 //        $('#mode').html(html);

 //      }
 //    });

	$(document).on('change', '#services-categories-list', function() {

    var id = $(this).val();

      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/admin/ajax_services_count',
        type: "POST",
        data: {id: id, entity: 'services'},
        success: function(html){
        // alert(html);
        $('#mode').html(html);

      }
    }); 
  });
	
</script>