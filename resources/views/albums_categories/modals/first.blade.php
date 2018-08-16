<div class="grid-x grid-padding-x align-center modal-content inputs">
  <div class="small-10 cell">
    <label>Название категории
      @include('includes.inputs.name', ['value'=>$albums_category->name, 'name'=>'name', 'required'=>'required'])
      <div class="item-error">Такая категория уже существует!</div>
    </label>
    {{ Form::hidden('first_item', 0, ['class' => 'first-item', 'pattern' => '[0-9]{1}']) }}
    {{ Form::hidden('albums_category_id', $albums_category->id, ['id' => 'albums-category-id']) }}

    @include('includes.control.checkboxes', ['item' => $albums_category])
    
  </div>
</div>
<div class="grid-x align-center">
  <div class="small-6 medium-4 cell">
    {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button '.$class]) }}
  </div>
</div>
