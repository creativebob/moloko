<div class="reveal" id="medium-add" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>ДОБАВЛЕНИЕ отдела / должности</h5>
        </div>
    </div>
    <div class="grid-x tabs-wrap align-center tabs-margin-top">
        <div class="small-10 cell">
            <ul class="tabs-list" data-tabs id="tabs">
                <li class="tabs-title is-active"><a href="#add-department" aria-selected="true">Отдел</a></li>
                <li class="tabs-title"><a data-tabs-target="add-position" href="#add-position">Должность</a></li>
            </ul>
        </div>
    </div>
    <div class="tabs-wrap inputs">
        <div class="tabs-content" data-tabs-content="tabs">
            <!-- Добавляем отдел -->
            {{ Form::open(['id' => 'form-medium-add']) }}
            <div class="tabs-panel is-active" id="add-department">
                <div class="grid-x grid-padding-x align-center modal-content inputs">
                    <div class="small-12 cell">
                        <label>Добавляем отдел в:
                            <select class="departments-list" name="parent_id">
                                @php
                                echo $departments_list;
                                @endphp
                            </select>
                        </label>
                        <label>Название отдела
                            @include('includes.inputs.name', ['value'=>null, 'name'=>'name', 'required'=>'required'])
                            <div class="item-error">Данный отдел уже существует в этом филиале!</div>
                        </label>
                    </div>
                    <div class="small-4 cell">
                        <label class="input-icon">Введите город
                        @include('includes.inputs.city_search', ['city_value'=>null, 'city_id_value'=>null, 'required'=>'required'])
                    </label>
                </div>
                <div class="small-8 cell">
                    <label>Адрес отдела
                    @include('includes.inputs.address', ['value'=>null, 'name'=>'address', 'required'=>''])
                </label>            
            </div>
            <div class="small-12 cell">
                <label>Телефон отдела
                    @include('includes.inputs.phone', ['value'=>null, 'name'=>'phone', 'required'=>''])
                </label>

                @include('includes.control.checkboxes', ['item' => $department])

                {{ Form::hidden('filial_id', 0, ['class' => 'filial-id']) }}
                {{ Form::hidden('parent_id', 0, ['class' => 'parent-id']) }}
                {{ Form::hidden('medium_item', 0, ['class' => 'medium-item', 'pattern' => '[0-9]{1}']) }}
            </div>
        </div>
        <div class="grid-x align-center">
            <div class="small-6 medium-4 cell">
                {{ Form::submit('Добавить отдел', ['data-close', 'class'=>'button modal-button submit-add']) }}
            </div>
        </div>

    </div>
    {{ Form::close() }}
    <!-- Добавляем должность -->
    {{ Form::open(['url' => 'admin/staff','id' => 'form-position-add']) }}
    <div class="tabs-panel" id="add-position">
        <div class="grid-x grid-padding-x align-center modal-content inputs">
          <div class="small-12 cell">

            <label>Добавляем должность в:
              <select class="departments-list" name="department_id">
                @php
                echo $departments_list;
                @endphp
            </select>
        </label>
        <label>Должность
          {{ Form::select('position_id', $positions_list, ['class'=>'positions-list']) }}
      </label>

      {{-- @include('includes.control.checkboxes', ['item' => $staffer]) --}}

      {{ Form::hidden('filial_id', 0, ['class' => 'filial-id']) }}
  </div>
</div>
<div class="grid-x align-center">
  <div class="small-6 medium-4 cell">
    {{ Form::submit('Добавить должность', ['data-close', 'class'=>'button modal-button', 'id'=>'submit-position-add']) }}
</div>
</div>
</div>
{{ Form::close() }}
<!-- Добавляем график работы -->
{{ Form::open(['id' => 'form-worktimes-add']) }}
<div class="tabs-panel" id="add-worktimes">
    <div class="grid-x grid-padding-x align-center modal-content inputs">
      <div class="small-10 cell">
        <label>Добавляем должность в:
          <select class="departments-list" name="department_id">
            @php
            echo $departments_list;
            @endphp
        </select>
    </label>
    <label>Должность
      {{ Form::select('position_id', $positions_list, ['class'=>'positions-list']) }}
  </label>

  {{ Form::hidden('filial_id', 0, ['class' => 'filial-id']) }}
</div>
</div>
<div class="grid-x align-center">
  <div class="small-6 medium-4 cell">
    {{ Form::submit('Добавить должность', ['data-close', 'class'=>'button modal-button', 'id'=>'submit-position-add']) }}
</div>
</div>
</div>
{{ Form::close() }}
</div>
</div>
<div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>

@include('includes.scripts.inputs-mask')



