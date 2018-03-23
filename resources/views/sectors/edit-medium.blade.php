<div class="reveal" id="medium-edit" data-reveal data-close-on-click="false">
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Редактирование сектора</h5>
    </div>
  </div>
  <!-- Редактируем отдел -->
  {{ Form::open(['id'=>'form-medium-edit', 'data-abide', 'novalidate']) }}

    @include('sectors.modals.medium', ['submitButtonText' => 'Редактировать сектор', 'class' => 'submit-edit'])

  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>

@include('includes.scripts.inputs-mask')



