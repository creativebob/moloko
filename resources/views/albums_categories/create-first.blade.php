<div class="reveal" id="modal-create" data-reveal data-close-on-click="false">
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ категории</h5>
    </div>
  </div>
  {{ Form::open(['id'=>'form-modal-create', 'data-abide', 'novalidate']) }}

    @include('albums_categories.modals.first', ['submitButtonText' => 'Добавить категорию', 'class' => 'submit-add'])

  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>

@include('includes.scripts.inputs-mask')



