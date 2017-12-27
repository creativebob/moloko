<div class="grid-x grid-padding-x inputs">
  <div class="small-12 medium-7 large-5 cell tabs-margin-top">
    @if ($errors->any())
      <div class="alert callout" data-closable>
        <h5>Неправильный формат данных:</h5>
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif
    <!-- Должность -->
    <label>Название должности
    {{ Form::text('position_name', $position->position_name, ['class'=>'position-name-field', 'maxlength'=>'40', 'autocomplete'=>'off']) }}
    </label>
    <label>Страница должности:
      {{ Form::select('page_id', $pages, $position->page_id, ['id'=>'page-select']) }}
    </label>
  </div>
  <div class="small-12 medium-5 large-7 cell tabs-margin-top">

  </div>
  <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
  </div>
</div>
