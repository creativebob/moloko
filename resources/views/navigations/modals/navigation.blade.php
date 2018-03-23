<div class="grid-x grid-padding-x align-center modal-content inputs">
  <div class="small-10 cell">
    <label class="input-icon">Введите название навигации
      @include('includes.inputs.name', ['value'=>$navigation->name, 'name'=>'navigation_name'])
    </label>
    {{ Form::hidden('navigation_id', $navigation->id, ['id' => 'navigation-id']) }}
    {{ Form::hidden('site_id', $site->id) }}
  </div>
</div>
<div class="grid-x align-center">
  <div class="small-6 medium-4 cell">
    {{ Form::submit($submitButtonText, ['data-close', 'class'=>'button modal-button', 'id'=>$id]) }}
  </div>
</div>