

<div class="reveal" id="medium-edit" data-reveal data-close-on-click="false">
	<div class="grid-x">
		<div class="small-12 cell modal-title">
			<h5>Редактирование отдел</h5>
		</div>
	</div>
	<div class="grid-x tabs-wrap align-center tabs-margin-top">
  <div class="small-8 cell">
    <ul class="tabs-list" data-tabs id="tabs">
      <li class="tabs-title is-active"><a href="#department" aria-selected="true">Филиал</a></li>
      <li class="tabs-title"><a data-tabs-target="worktimes" href="#worktimes">График работы</a></li>
    </ul>
  </div>
</div>
<div class="tabs-wrap inputs">
  <div class="tabs-content" data-tabs-content="tabs">

    
	{{ Form::open(['id'=>'form-medium-edit', 'data-abide', 'novalidate']) }}
	<div class="tabs-panel is-active" id="department">
	<div class="grid-x grid-padding-x align-center modal-content inputs">
		<div class="small-10 cell">
			<label>Отдел находится в:
				<select class="departments-list" name="parent_id">
					@php
					echo $departments_list;
					@endphp
				</select>
			</label>
			<label>Название отдела
				@include('includes.inputs.name', ['value'=>$department->name, 'name'=>'name', 'required'=>'required'])
				<div class="item-error">Данный отдел уже существует в этом филиале!</div>
			</label>
			<label class="input-icon">Введите город
				@php
				$city_name = null;
				$city_id = null;
				if(isset($department->location->city->name)) {
				$city_name = $department->location->city->name;
				$city_id = $department->location->city->id;
			}
			@endphp
			@include('includes.inputs.city_search', ['city_value'=>$city_name, 'city_id_value'=>$city_id, 'required'=>'required'])
		</label>
		<label>Адрес отдела
			@php
			$address = null;
			if (isset($department->location->address)) {
			$address = $department->location->address;
		}
		@endphp
		@include('includes.inputs.address', ['value'=>$address, 'name'=>'address', 'required'=>''])
	</label>
	<label>Телефон отдела
		@include('includes.inputs.phone', ['value'=>$department->phone, 'name'=>'phone', 'required'=>''])
	</label>

	{{-- Чекбокс отображения на сайте --}}
    @can ('publisher', $department)
    <div class="small-12 cell checkbox">
      {{ Form::checkbox('display', 1, $department->display, ['id' => 'display']) }}
      <label for="display"><span>Отображать на сайте</span></label>
    </div>
    @endcan

    @if ($department->moderation == 1)
    <div class="checkbox">
      {{ Form::checkbox('moderation', 1, $department->moderation, ['id' => 'moderation']) }}
      <label for="moderation"><span>Временная запись.</span></label>
    </div>
    @endif

    @can('god', App\Department::class)
    <div class="checkbox">
      {{ Form::checkbox('system_item', 1, $department->system_item, ['id' => 'system-item']) }}
      <label for="system-item"><span>Системная запись.</span></label>
    </div>
    @endcan

	{{ Form::hidden('filial_id', $department->filial_id, ['id' => 'filial-id']) }}
	{{ Form::hidden('department_id', $department->id, ['id' => 'department-id']) }}
	{{ Form::hidden('medium_item', 1, ['class' => 'medium-item', 'pattern' => '[0-9]{1}']) }}
</div>
</div>
</div>
<!-- Схема работы -->
<div class="tabs-panel" id="worktimes">
  <div class="grid-x grid-padding-x align-center">
    <div class="small-8 cell">
      @include('includes.inputs.schedule', ['value'=>$worktime]) 
    </div>
  </div>
</div>
<div class="grid-x align-center">
	<div class="small-6 medium-4 cell">
		{{ Form::submit('Редактировать отдел', ['data-close', 'class'=>'button modal-button submit-edit']) }}
	</div>
</div>
{{ Form::close() }}
<div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
</div>
  </div>

@include('includes.scripts.inputs-mask')



