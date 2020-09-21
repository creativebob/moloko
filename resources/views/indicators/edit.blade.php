@extends('layouts.app')

@section('title', 'Редактировать показатель')

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $pageInfo, $indicator))

@section('title-content')
<div class="top-bar head-content">
	<div class="top-bar-left">
		<h2 class="header-content">РЕДАКТИРОВАТЬ показатель</h2>
	</div>
	<div class="top-bar-right">
	</div>
</div>
@endsection

@section('content')

{{ Form::model($indicator, ['route' => ['indicators.update', $indicator->id], 'data-abide', 'novalidate']) }}

{{ method_field('PATCH') }}

@include('indicators.form', ['submit_text' => 'Редактировать', 'form_method' => 'edit'])


{{ Form::close() }}

@endsection

@push('scripts')
@include('includes.scripts.inputs-mask')
@include('indicators.scripts')
{{-- Проверка поля на существование --}}
@include('includes.scripts.check', ['entity' => 'indicators'])

@include('includes.scripts.get_units')
@endpush


{{-- <div class="grid-x grid-padding-x inputs">

	<div class="small-12 medium-6 cell tabs-margin-top">

		<div class="grid-x grid-padding-x">

			<div class="small-12 cell">
				<label>Название
					@include('includes.inputs.name', ['required' => true])
					<div class="sprite-input-right find-status" id="alias-check"></div>
					<div class="item-error">Такой показатель уже существует!</div>
				</label>
			</div>

			<div class="small-12 medium-6 cell">
				<label>Категория
					{!! Form::select('indicators_category_id', $indicator->indicators_category->pluck('name', 'id'), $indicator->indicators_category->id, ['disabled']) !!}
				</label>
			</div>



			<div class="small-12 medium-6 cell">
				<label>Сущность
					{!! Form::select('entity_id', $indicator->entity->pluck('name', 'id'), $indicator->entity->id, ['disabled']) !!}
				</label>
			</div>

			<div class="small-12 medium-6 cell">
				<label>Направление
					{!! Form::select('direction_id', $indicator->category->pluck('name', 'id'), $indicator->category->id, ['disabled']) !!}

				</label>
			</div>

			<div class="small-12 cell">
				<div class="grid-x grid-margin-x">
					<div class="small-12 medium-6 cell">
						@include('includes.selects.units_categories', ['default' => 6])
					</div>

					<div class="small-12 medium-6 cell">
						@include('includes.selects.units', ['default' => 26, 'units_category_id' => 6])
					</div>
				</div>
			</div>

			<div class="small-12 cell">
				<label>Описание
					@include('includes.inputs.textarea', ['name' => 'description'])
				</label>
			</div>
		</div>
	</div>

	<div class="small-12 medium-5 large-7 cell tabs-margin-top">
	</div>

	Чекбоксы управления
	@include('includes.control.checkboxes', ['item' => $indicator])

	<div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
		{{ Form::submit('Редактировать', ['class'=>'button']) }}
	</div>
</div> --}}
