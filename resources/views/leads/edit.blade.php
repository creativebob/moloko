@extends('layouts.app')

@section('inhead')
@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Редактировать лид')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $lead->name))

@section('title-content')
<div class="top-bar head-content">
	<div class="top-bar-left">
		<h2 class="header-content">ЛИД №: <input name="show_case_number" readonly class="case_number_field" value="{{ $lead->case_number }}"> </h2>
	</div>
	<div class="top-bar-right">
	</div>
</div>
@endsection

@section('content')

{{ Form::model($lead, ['url' => '/admin/leads/'.$lead->id, 'data-abide', 'novalidate', 'class' => 'form-check-city', 'files'=>'true']) }}

{{ method_field('PATCH') }}

@php 
	if($lead->phone != null){
		$readonly = 'readonly';
		$autofocus = '';
	} else {
		$readonly = '';
		$autofocus = 'autofocus';
}

@endphp

@include('leads.form', ['submitButtonText' => 'Редактировать лид', 'param'=>'', $readonly, $autofocus])

{{ Form::close() }}

@endsection

@section('modals')
<section id="modal"></section>
{{-- Модалка удаления с ajax --}}
@include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
@include('leads.scripts')
@include('includes.scripts.cities-list')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')
@include('includes.scripts.upload-file')

<script>

	var lead_id = '{{ $lead->id }}';

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
	});

</script>


@include('includes.scripts.notes', ['id' => $lead->id, 'model' => 'Lead'])
@include('includes.scripts.challenges', ['id' => $lead->id, 'model' => 'Lead'])

@endsection



