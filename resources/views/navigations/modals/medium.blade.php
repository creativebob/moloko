<!-- Добавляем пункт меню -->
<div class="tabs-panel is-active" id="edit-menu">
  <div class="grid-x grid-padding-x align-center modal-content inputs">
    <div class="small-10  cell">
      <label>Название пункта меню
        @include('includes.inputs.name', ['name'=>'menu_name', 'value'=>$menu->menu_name, 'required'=>'required'])
        <span class="form-error">Уж постарайтесь, введите хотя бы 2 символа!</span>
      </label>
      <label>Введите ссылку
        @include('includes.inputs.text-en', ['name'=>'menu_alias', 'value'=>$menu->menu_alias, 'required'=>''])
      </label>
      <label>Страница:
        <select name="page_id" class="pages-select" placeholder="Не выбрано">
          <option value="">Не выбрано</option>
          @php
            echo $pages_list;
          @endphp
        </select>
      </label>
      @if ($menu->moderation == 1)
      <div class="checkbox">
        {{ Form::checkbox('moderation', 1, $menu->moderation, ['id' => 'moderation']) }}
        <label for="moderation"><span>Временная запись.</span></label>
      </div>
      @endif
      @can('god', App\Menu::class)
      <div class="checkbox">
        {{ Form::checkbox('system_item', 1, $menu->system_item, ['id' => 'system-item']) }}
        <label for="system-item"><span>Системная запись.</span></label>
      </div>
      @endcan
    </div>
  </div>
</div>
<!-- Добавляем опции -->
<div class="tabs-panel" id="edit-options">
  <div class="grid-x grid-padding-x align-center modal-content inputs">
    <div class="small-10 cell">
      <label>Добавляем пункт в:
        <select name="menu_parent_id" class="menu_list">
          @php
            echo $navigation_list;
          @endphp
        </select>
      </label>
      <label>Введите имя иконки
        @include('includes.inputs.text-en', ['name'=>'menu_icon', 'value'=>$menu->menu_icon, 'required'=>''])
      </label>
      {{ Form::hidden('navigation_id', $menu->navigation_id) }}
      {{ Form::hidden('site_id', $site->id) }}
      {{ Form::hidden('menu_id', $menu->id, ['id'=>'menu_id']) }}
      
    </div>
  </div>
</div>
<div class="grid-x align-center">
  <div class="small-6 medium-4 cell">
    {{ Form::submit($submitButtonText, ['data-close', 'class'=>'button modal-button', 'id'=>$id]) }}
  </div>
</div>
  