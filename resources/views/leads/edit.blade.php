@extends('layouts.app')

@section('inhead')
@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Редактировать лид')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $lead->name))

@section('title-content')
<div class="top-bar head-content">
	<div class="top-bar-left">
		<h2 class="header-content">РЕДАКТИРОВАТЬ ЛИД</h2>
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

	$(document).on('dblclick', '#phone', function() {
		
    // Снятие блокировки с поля номер телефона
    $('#phone').attr('readonly', false);

});

</script>


@include('includes.scripts.notes', ['id' => $lead->id, 'model' => 'Lead'])
@include('includes.scripts.challenges', ['id' => $lead->id, 'model' => 'Lead'])

@endsection



