<div class="reveal" id="modal-edit" data-reveal data-close-on-click="false">
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>{{ $title }}</h5>
    </div>
  </div>
  {{ Form::model($item,['id' => 'form-edit', 'data-abide', 'novalidate']) }}

    @include($entity.'.form', ['submit_text' => 'Редактировать', 'class' => 'submit-edit'])

  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>



