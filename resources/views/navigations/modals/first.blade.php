<div class="grid-x grid-padding-x align-center modal-content inputs">
  <div class="small-10 cell">
    <label class="input-icon">Введите название навигации
      @include('includes.inputs.name', ['value'=>$navigation->name, 'name'=>'name', 'required'=>'required'])
      <div class="item-error">Такая навигация уже существует!</div>
    </label>
    <label>Категория навигации
      <select name="navigations_category_id">
        @php
        echo $navigations_categories_list;
        @endphp
      </select>
    </label>
    <div class="checkbox">
      {{ Form::checkbox('display', 1, $navigation->display, ['id' => 'display']) }}
      <label for="display"><span>Отображать на сайте</span></label>
    </div>
    @if ($navigation->moderation == 1)
    <div class="checkbox">
      {{ Form::checkbox('moderation', 1, $navigation->moderation, ['id' => 'moderation']) }}
      <label for="moderation"><span>Временная запись.</span></label>
    </div>
    @endif
    @can('god', App\Navigation::class)
    <div class="checkbox">
      {{ Form::checkbox('system_item', 1, $navigation->system_item, ['id' => 'system-item']) }}
      <label for="system-item"><span>Системная запись.</span></label>
    </div>
    @endcan
    {{ Form::hidden('navigation_id', $navigation->id, ['id' => 'navigation-id']) }}
    {{ Form::hidden('site_id', $site->id) }}
    {{ Form::hidden('first_item', 0, ['class' => 'first-item']) }}
  </div>
</div>
<div class="grid-x align-center">
  <div class="small-6 medium-4 cell">
    {{ Form::submit($submitButtonText, ['data-close', 'class'=>'button modal-button', 'id'=>$id]) }}
  </div>
</div>