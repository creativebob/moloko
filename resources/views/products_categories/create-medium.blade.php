<div class="reveal" id="medium-add" data-reveal data-close-on-click="false">
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ категории продукции</h5>
    </div>
  </div>
  {{ Form::open(['url' => '/products_categories', 'id'=>'form-medium-add', 'data-abide', 'novalidate']) }}

      @include('products_categories.modals.medium', ['submitButtonText' => 'Добавить категорию продукции', 'class' => 'submit-add'])

  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>

@include('includes.scripts.inputs-mask')
@include('includes.scripts.upload-file')



