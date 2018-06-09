<!-- Добавляем пункт меню -->
<div class="tabs-panel is-active" id="edit-menu">
  <div class="grid-x grid-padding-x align-center modal-content inputs">
    <div class="small-10  cell">
      <label>Название пункта меню
        @include('includes.inputs.name', ['name'=>'name', 'value'=>$menu->name, 'required'=>'required'])
        <span class="form-error">Уж постарайтесь, введите хотя бы 2 символа!</span>
      </label>
      <label>Введите ссылку
        @include('includes.inputs.text-en', ['name'=>'alias', 'value'=>$menu->alias, 'required'=>''])
      </label>
      <label>Страница:
        <select name="page_id" class="pages-select" placeholder="Не выбрано">
          <option value="">Не выбрано</option>
          @php
          echo $pages_list;
          @endphp
        </select>
      </label>


    </div>
  </div>
</div>
<!-- Добавляем опции -->
<div class="tabs-panel" id="edit-options">
  <div class="grid-x grid-padding-x align-center modal-content inputs">
    <div class="small-10 cell">
      <label>Добавляем пункт в:
        <select name="parent_id" class="menu_list">
          @php
          echo $navigation_list;
          @endphp
        </select>
      </label>
      <label>Введите имя иконки
        @include('includes.inputs.text-en', ['name'=>'icon', 'value'=>$menu->icon, 'required'=>''])
      </label>

      {{ Form::hidden('navigation_id', $menu->navigation_id) }}
      {{ Form::hidden('site_id', $site->id) }}
      {{ Form::hidden('menu_id', $menu->id, ['id'=>'menu_id']) }}
      
    </div>
  </div>
</div>
<div class="grid-x align-center">
   {{-- Чекбокс отображения на сайте  --}}
      @can ('publisher', $menu)
      <div class="small-8 cell checkbox">
        {{ Form::checkbox('display', 1, $menu->display, ['id' => 'display']) }}
        <label for="display"><span>Отображать на сайте</span></label>
      </div>
      @endcan
      @if ($menu->moderation == 1)
      <div class="small-8 cell checkbox">
        {{ Form::checkbox('moderation', 1, $menu->moderation, ['id' => 'moderation']) }}
        <label for="moderation"><span>Временная запись.</span></label>
      </div>
      @endif
      @can('god', App\Menu::class)
      <div class="small-8 cell checkbox">
        {{ Form::checkbox('system_item', 1, $menu->system_item, ['id' => 'system-item']) }}
        <label for="system-item"><span>Системная запись.</span></label>
      </div>
      @endcan
</div>
<div class="grid-x align-center">
  <div class="small-6 medium-4 cell">
    {{ Form::submit($submitButtonText, ['data-close', 'class'=>'button modal-button', 'id'=>$id]) }}
  </div>
</div>
