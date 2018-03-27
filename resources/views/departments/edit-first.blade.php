<div class="reveal" id="first-edit" data-reveal data-close-on-click="false">
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Редактирование филиала</h5>
    </div>
  </div>
  {{ Form::open(['id'=>'form-first-edit', 'data-abide', 'novalidate']) }}

    @include('departments.modals.first', ['submitButtonText' => 'Редактировать филиал', 'class' => 'submit-edit'])

  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>

@include('includes.scripts.inputs-mask')



