@extends('project.layouts.app')

@section('title', 'Клиника')

@section('content')
{{-- Контент --}}
<main class="grid-x align-center clinic">
	<div class="small-11 medium-10 cell">

		<h2 class="text-center title">Клиника</h2>

		<div class="grid-x grid-margin-x">
			<div class="small-12 large-6 cell">
				@if (isset($page->photo_id))
				<img src="/storage/{{ $page->company_id }}/media/pages/{{ $page->id }}/img/large/{{ $page->photo->name }}" alt="{{ $page->photo->name }}">
				@endif
				
				<section class="departments">

			        <h4>Структурные подразделения КДП «Профессорская клиника»:</h4>
			
			      
			          <dl>
			            <dt>Отделение косметологии</dt>
			          </dl>
			          <dl>
			            <dt>Консультативное отделение</dt>
			            <dd>Консультации ведущих профессоров Иркутского государственного медицинского университета и ведущих специалистов областных учреждений здравоохранения.</dd>
			          </dl>
			          <dl>
			            <dt>Отделение медицинских осмотров</dt>
			            <dd>Медицинский осмотр и оформление медицинской книжки, получение медсправки «О допуске к управлению транспортным средством», справки на оружие.</dd>
			          </dl>
			          <dl>
			            <dt>Отделение гепатологии</dt>
			            <dd>Диагностика и лечение заболеваний печени (консультации врача гепатолога спроведением эластографии (метод исследования печени))</dd>
			          </dl>
			          <dl>
			            <dt>Отделение ультрозвуковой и функциональной диагностики</dt>
			            <dd>УЗИ, ЭКГ, холтеровское мониторирование.</dd>
			          </dl>
			          <dl>
			            <dt>Клинико-диагностическая лаборатория</dt>
			            <dd>Проводятся все виды лабораторных исследований</dd>
			          </dl>
			          <dl>
			            <dt>Кабинет акушера-гинеколога</dt>
			          </dl>
			       
			
			
			    </section>
			</div>

			<div class="small-12 large-6 cell">
				@if (isset($page->content))
				@php
				echo $page->content;
				@endphp
				@endif
			</div>

		</div>
	

	</div>
</main>
@endsection

@section('scripts')

@endsection