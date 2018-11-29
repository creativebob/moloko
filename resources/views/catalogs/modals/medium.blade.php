<div class="grid-x grid-padding-x modal-content inputs">
  <div class="small-10 small-offset-1 cell">
    <label>Расположение
      <select name="parent_id">
        @php
        echo $catalogs_list;
        @endphp
      </select>
    </label>
    <label>Название каталога
      @include('includes.inputs.name', ['value'=>$catalog->name, 'name'=>'name', 'required' => true])
      <div class="sprite-input-right find-status"></div>
      <div class="item-error">Такой сектор уже существует!</div>
    </label>
    {{ Form::hidden('category_id', $catalog->id, ['id' => 'category-id']) }}
    {{ Form::hidden('site_id', $site->id) }}
    {{ Form::hidden('medium_item', 0, ['class' => 'medium-item', 'pattern' => '[0-9]{1}']) }}

    @include('includes.control.checkboxes', ['item' => $catalog])
  </div>
</div>
<div class="grid-x align-center">
  <div class="small-6 medium-4 cell">
    {{ Form::submit($submitButtonText, ['data-close', 'class'=>'button modal-button '.$class]) }}
  </div>
</div>