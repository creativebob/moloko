<div class="reveal" id="first-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ индустрии</h5>
    </div>
  </div>
  {{ Form::open(['id'=>'form-first-add', 'data-abide', 'novalidate']) }}

    @include('sectors.modals.first', ['submitButtonText' => 'Добавить индустрию', 'class' => 'submit-add'])

  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>

@include('includes.scripts.inputs-mask')



