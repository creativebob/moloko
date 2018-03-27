<div class="reveal" id="first-edit" data-reveal data-close-on-click="false">
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Редактирование навигации</h5>
    </div>
  </div>
  {{ Form::model($navigation, ['id' => 'form-first-edit', 'data-abide', 'novalidate']) }}

    @include('navigations.modals.first', ['submitButtonText' => 'Редактировать навигацию', 'id'=>'submit-first-edit'])

  {{ Form::close() }}
<div data-close class="icon-close-modal sprite close-modal"></div> 
</div>

@include('includes.scripts.inputs-mask')



