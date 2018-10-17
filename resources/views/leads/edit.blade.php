@extends('layouts.app')

@section('inhead')
@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Редактировать лид')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, isset($lead->case_number) ? $lead->case_number : 'нет номера'))

@section('title-content')
<div class="top-bar head-content">
	<div class="top-bar-left">
		<h2 class="header-content">ЛИД №: <input id="show-case-number" name="show_case_number" readonly class="case_number_field" value="{{ $lead->case_number }}"> </h2>
	</div>
	<div class="top-bar-right">
	</div>
</div>
@endsection

@section('content')

{{ Form::model($lead, ['url' => '/admin/leads/'.$lead->id, 'data-abide', 'novalidate', 'class' => 'form-check-city', 'files'=>'true']) }}

{{ method_field('PATCH') }}

@php 

$readonly = '';
$autofocus = 'autofocus';

if(isset($lead->main_phone)){

if($lead->main_phone->phone != null){
$readonly = 'readonly';
$autofocus = '';
} else {
$readonly = '';
$autofocus = 'autofocus';
}
}

if($lead->manager_id == 1){
$disabled_leadbot = 'disabled';
} else {
$disabled_leadbot = '';
}


@endphp

@include('leads.form', ['submitButtonText' => 'Сохранить', 'param'=>'', $readonly, $autofocus])

{{ Form::close() }}

@endsection

@section('modals')
<section id="modal"></section>
{{-- Модалка удаления с ajax --}}
@include('includes.modals.modal-delete-ajax')

@include('includes.modals.modal-add-claim', ['lead' => $lead])
@endsection

@section('scripts')
@include('leads.scripts')
@include('includes.scripts.cities-list')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')
@include('includes.scripts.upload-file')

<script>

	var lead_id = '{{ $lead->id }}';
	var lead_type_id = '{{ $lead->lead_type_id }}';

	$(document).on('dblclick', '#phone', function() {
		
    	// Снятие блокировки с поля номер телефона
    	$('#phone').attr('readonly', false);

    });

	$(document).on('click', '#lead-free', function(event) {
		event.preventDefault();

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: "/admin/lead_direction_check",
			type: "POST",
			success: function(data){

				if (data == 1) {

					$.ajax({
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						url: "/admin/lead_appointed",
						type: "POST",
						data: {id: lead_id},
						success: function(html){
							$('#modal').html(html);
							$('#add-appointed').foundation();
							$('#add-appointed').foundation('open');
						}
					});
				} else {

					$.ajax({
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						url: "/admin/lead_free",
						type: "POST",
						data: {id: lead_id},
						success: function(date){

							var result = $.parseJSON(date);
							if (result.error_status == 0) {

								var url = '{{ url("admin/leads") }}';

								window.location.replace(url);

								// $(location).attr('href', );
							} else {
                				// Выводим ошибку на страницу
                				alert(result.error_message);
                			};

                		}
                	});
				}
			}
		});
	});

	$(document).on('click', '#submit-appointed', function(event) {
		event.preventDefault();

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: "/admin/lead_distribute",
			type: "POST",
			data: $(this).closest('form').serialize(),
			success: function(date){

				var url = '{{ url("admin/leads") }}/' + lead_id + '/edit';

				window.location.replace(url);

			}
		});
	});

	$(document).on('click', '.take-lead', function(event) {
		event.preventDefault();

		var entity_alias = $(this).closest('.item').attr('id').split('-')[0];
		var id = $(this).closest('.item').attr('id').split('-')[1];
		var item = $(this);


		/* Act on the event */
	});

	$(document).on('click', '#submit-add-claim', function(event) {
		event.preventDefault();

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: "/admin/claim_add",
			type: "POST",
			data: $('#form-claim-add').serialize(),
			success: function(html){
				$('#add-claim').foundation('close')
				$('#claims-list').html(html);
				$('#form-claim-add textarea[name=body]').val('');


			}
		});
	});

	$(document).on('click', '.finish-claim', function(event) {
		event.preventDefault();

		// Находим описание сущности, id и название удаляемого элемента в родителе
		var parent = $(this).closest('.item');
		var id = parent.attr('id').split('-')[1];
		var name = parent.data('name');

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: "/admin/claim_finish",
			type: "POST",
			data: {id: id},
			success: function(data){
				var result = $.parseJSON(data);

				if (result['error_status'] == 0) {
					$('#claims-' + id + ' .action a').remove();
				} else {
					alert(result['error_message']);
				};
			}
		});
	});


	$(document).on('click', '#change-lead-type', function(event) {
		event.preventDefault();

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: "/admin/open_change_lead_type",
			type: "POST",
			data: {lead_type_id: lead_type_id, lead_id: lead_id},
			success: function(html){
				$('#modal').html(html);
				$('#modal-change-lead-type').foundation();
				$('#modal-change-lead-type').foundation('open');
			}
		});
	});

	$(document).on('click', '#submit-change-lead-type', function(event) {
		event.preventDefault();

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: "/admin/change_lead_type",
			type: "POST",
			data: $(this).closest('form').serialize(),
			success: function(data){
				$('#modal-change-lead-type').foundation('close');
				$('#lead-type-name').html(data['lead_type_name']);
				$('#show-case-number').val(data['case_number']);		
			}
		});
	});



</script>


@include('includes.scripts.notes', ['id' => $lead->id, 'model' => 'Lead'])
@include('includes.scripts.challenges', ['id' => $lead->id, 'model' => 'Lead'])

@endsection



