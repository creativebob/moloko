<div class="reveal" id="medium-edit" data-reveal data-close-on-click="false">
<div class="grid-x">
  <div class="small-12 cell modal-title">
    <h5>Редактирование пункта меню</h5>
  </div>
</div>
<div class="grid-x tabs-wrap tabs-margin-top align-center">
  <div class="small-10 medium-4 cell">
    <ul class="tabs-list" data-tabs id="tabs">
      <li class="tabs-title is-active"><a href="#edit-menu" aria-selected="true">Меню</a></li>
      <li class="tabs-title"><a data-tabs-target="edit-options" href="#edit-options">Настройки</a></li>
    </ul>
  </div>
</div>
<div class="tabs-wrap inputs">
  <div class="tabs-content" data-tabs-content="tabs">
    {{ Form::model($menu, ['id' => 'form-medium-edit', 'data-abide', 'novalidate']) }}

      @include('navigations.modals.medium', ['submitButtonText' => 'Редактировать пункт', 'id'=>'submit-medium-edit'])

    {{ Form::close() }}
  </div>
</div>
<div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>

@include('includes.scripts.inputs-mask')



