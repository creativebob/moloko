<div class="reveal" id="first-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ навигации</h5>
    </div>
  </div>
  {{ Form::open(['id' => 'form-first-add', 'data-abide', 'novalidate']) }}

    @include('navigations.modals.navigation', ['submitButtonText' => 'Добавить навигацию', 'id'=>'submit-first-add'])

  {{ Form::close() }}
<div data-close class="icon-close-modal sprite close-modal"></div> 
</div>

@include('includes.scripts.inputs-mask')



