<div class="reveal" id="modal-create" data-reveal data-close-on-click="false">
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ навигации</h5>
    </div>
  </div>
  {{ Form::open(['id' => 'form-modal-create', 'data-abide', 'novalidate']) }}

    @include('navigations.modals.first', ['submitButtonText' => 'Добавить навигацию', 'id'=>'submit-modal-create'])

  {{ Form::close() }}
<div data-close class="icon-close-modal sprite close-modal"></div>
</div>

@include('includes.scripts.inputs-mask')



