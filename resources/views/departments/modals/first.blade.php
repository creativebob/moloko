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

    <div class="tabs-panel is-active" id="department">
      <div class="grid-x grid-padding-x align-center modal-content inputs">
        <div class="small-10 cell">
          <label class="input-icon">Введите город
            @php
            $city_name = null;
            $city_id = null;
            if (isset($department->location->city->name)) {
            $city_name = $department->location->city->name;
            $city_id = $department->location->city->id;
          }
          @endphp
          @include('includes.inputs.city_search', ['city_value'=>$city_name, 'city_id_value'=>$city_id, 'required'=>'required'])
        </label>
        <label>Название филиала
          @include('includes.inputs.name', ['value'=>$department->name, 'name'=>'name', 'required'=>'required'])
          <div class="item-error">Такой филиал уже существует в организации!</div>
        </label>
        <label>Адресс филиала
          @php
          $address = null;
          if (isset($department->location->address)) {
          $address = $department->location->address;
        }
        @endphp
        @include('includes.inputs.address', ['value'=>$address, 'name'=>'address', 'required'=>''])
      </label>
      <label>Телефон филиала
        @include('includes.inputs.phone', ['value'=>$department->phone, 'name'=>'phone', 'required'=>'required'])
      </label>
      {{ Form::hidden('department_id', $department->id, ['id' => 'department-id']) }}
      {{ Form::hidden('first_item', 0, ['class' => 'first-item']) }}

      @include('includes.control.checkboxes', ['item' => $department])

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
  <div class="small-6 medium-4 cell text-center">
    {{ Form::submit($submitButtonText, ['data-close', 'class'=>'button modal-button '.$class]) }}
  </div>
</div>

</div>
  </div>

