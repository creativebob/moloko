<div class="grid-x grid-padding-x align-center modal-content inputs">
  <div class="small-10 cell">
    <label class="input-icon">Введите название навигации
      @include('includes.inputs.name', ['value'=>$navigation->name, 'name'=>'name', 'required' => true])
      <div class="item-error">Такая навигация уже существует!</div>
    </label>
    <label>Категория навигации
      <select name="navigations_category_id">
        @php
        echo $navigations_categories_list;
        @endphp
      </select>
    </label>

    {{ Form::hidden('navigation_id', $navigation->id, ['id' => 'navigation-id']) }}
    {{ Form::hidden('site_id', $site->id) }}
    {{ Form::hidden('first_item', 0, ['class' => 'first-item']) }}

    @include('includes.control.checkboxes', ['item' => $navigation])

  </div>
</div>
<div class="grid-x align-center">
  <div class="small-6 medium-4 cell">
    {{ Form::submit($submitButtonText, ['data-close', 'class'=>'button modal-button', 'id'=>$id]) }}
  </div>
</div>