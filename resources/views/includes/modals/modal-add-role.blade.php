{{-- Модалка добавления роли --}}
<div class="reveal" id="role-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ Роли</h5>
    </div>
  </div>
  {{ Form::open(['id' => 'form-role-add']) }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 medium-6 cell">
        <label>Роли
          {{ Form::select('role_id', $roles_list, null, ['id'=>'select-roles']) }}
        </label>
      </div>
      <div class="small-10 medium-6 cell">
        <label>Отделы
          {{ Form::select('department_id', $departments_list, null, ['id'=>'select-departments']) }}
        </label>
        <input type="hidden" name="user_id" id="user-id" value="{{ $user->id }}">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        <button data-close class="button modal-button" id="submit-role-add" type="submit">Сохранить</button>
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки добавления роли --}}