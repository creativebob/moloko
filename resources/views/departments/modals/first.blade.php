<div class="grid-x grid-padding-x align-center modal-content inputs">
  <div class="small-10 cell">
    <label class="input-icon">Введите город
      @php
      $city_name = null;
      $city_id = null;
      if(isset($department->city->city_name)) {
      $city_name = $department->city->city_name;
      $city_id = $department->city->city_id;
      }
      @endphp
      @include('includes.inputs.city_search', ['city_value'=>$city_name, 'city_id_value'=>$city_id, 'required'=>'required'])
    </label>
    <label>Название филиала
      @include('includes.inputs.name', ['value'=>$department->department_name, 'name'=>'department_name', 'required'=>'required'])
      <div class="item-error">Такой филиал уже существует в организации!</div>
    </label>
    <label>Адресс филиала
     @include('includes.inputs.address', ['value'=>$department->address, 'name'=>'address', 'required'=>'required'])
   </label>
   <label>Телефон филиала
    @include('includes.inputs.phone', ['value'=>$department->phone, 'name'=>'phone', 'required'=>'required'])
  </label>
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
  {{ Form::hidden('department_id', $department->id, ['id' => 'department-id']) }}
  {{ Form::hidden('first_item', 0, ['class' => 'first-item']) }}
</div>
</div>
<div class="grid-x align-center">
  <div class="small-6 medium-4 cell text-center">
    {{ Form::submit($submitButtonText, ['data-close', 'class'=>'button modal-button '.$class]) }}
  </div>
</div>

