<div class="reveal" id="first-edit" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Редактирование индустрии</h5>
    </div>
  </div>
  {{ Form::open(['id'=>'form-first-edit', 'data-abide', 'novalidate']) }}

    @include('sectors.modals.first', ['submitButtonText' => 'Редактировать индустрию', 'class' => 'submit-edit'])

  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>

@include('includes.scripts.inputs-mask')



