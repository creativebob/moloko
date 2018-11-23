<div class="reveal" id="first-add" data-reveal data-close-on-click="false">
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ сектора</h5>
    </div>
  </div>
  {{ Form::open(['id'=>'form-create', 'data-abide', 'novalidate']) }}

    @include('sectors.form', ['submitButtonText' => 'Добавить сектор'])

  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>

@include('includes.scripts.inputs-mask')



