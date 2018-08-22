<div class="reveal" id="medium-add" data-reveal data-close-on-click="false">
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ каталога</h5>
    </div>
  </div>
  <!-- Добавляем сектор -->
  {{ Form::open(['id'=>'form-medium-add', 'data-abide', 'novalidate']) }}

      @include('catalogs.modals.medium', ['submitButtonText' => 'Добавить каталог', 'class' => 'submit-add'])

  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>

@include('includes.scripts.inputs-mask')



