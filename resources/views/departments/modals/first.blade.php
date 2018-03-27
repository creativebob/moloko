<div class="grid-x grid-padding-x align-center modal-content inputs">
  <div class="small-10 cell">
    <label class="input-icon">Введите город
      @php
      $city_name = null;
      @endphp
      @if (isset($department->city->city_name))
      @php
      $city_name = $department->city->city_name;
      @endphp
      @endif
      @include('includes.inputs.city_name', ['value'=>$city_name, 'name'=>'city_name', 'required'=>'required'])
      @include('includes.inputs.city_id', ['value'=>$department->city_id, 'name'=>'city_id'])
    </label>
    <label>Название филиала
      @include('includes.inputs.name', ['value'=>$department->department_name, 'name'=>'department_name'])
      <div class="item-error">Такой филиал уже существует в организации!</div>
    </label>
    <label>Адресс филиала
     @include('includes.inputs.address', ['value'=>$department->address, 'name'=>'address'])
   </label>
   <label>Телефон филиала
    @include('includes.inputs.phone', ['value'=>$department->phone, 'name'=>'phone', 'required'=>''])
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

