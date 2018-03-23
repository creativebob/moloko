<div class="reveal" id="medium-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ сектора</h5>
    </div>
  </div>
  <!-- Добавляем сектор -->
  {{ Form::open(['id'=>'form-medium-add', 'data-abide', 'novalidate']) }}

      @include('sectors.modals.medium', ['submitButtonText' => 'Добавить сектор', 'class' => 'submit-add'])

  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>

@include('includes.scripts.inputs-mask')



