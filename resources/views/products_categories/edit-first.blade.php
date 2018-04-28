<div class="reveal" id="first-edit" data-reveal data-close-on-click="false">
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Редактирование категории</h5>
    </div>
  </div>
  {{ Form::open(['id'=>'form-first-edit', 'data-abide', 'novalidate']) }}

    @include('products_categories.modals.first', ['submitButtonText' => 'Редактировать категорию', 'class' => 'submit-edit'])

  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>

@include('includes.scripts.inputs-mask')



