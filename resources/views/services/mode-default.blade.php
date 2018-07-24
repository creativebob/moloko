@if ($services_products_count > 0)
<a id="mode-select" class="modes up-input-button">Добавить в группу</a> |
@endif
<a id="mode-add" class="modes up-input-button">Создать группу</a>
<label>Название услуги
	@include('includes.inputs.string', ['value'=>null, 'name'=>'name', 'required'=>'required'])
	<div class="item-error">Такая услуга уже существует!</div>
</label>
{{ Form::hidden('mode', 'mode_default') }}

<script type="text/javascript">


	// $(document).on('change', '#services-categories-list', function() {

	// 	// var id = $(this).val();

	// 	$.ajax({
	// 		headers: {
	// 			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	// 		},
	// 		url: '/admin/ajax_services_modes',
	// 		type: "POST",
	// 		data: {mode: 'mode-default', entity: 'services', services_category_id: $('#services-categories-list').val()},
	// 		success: function(html){
 //                // alert(html);
 //                $('#mode').html(html);
 //            }
 //        }); 
	// });
	
</script>
