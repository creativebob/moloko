<!-- Добавляем пункт меню -->
<div class="tabs-panel is-active" id="edit-menu">
    <div class="grid-x grid-padding-x align-center modal-content inputs">
        <div class="small-10  cell">

            <label>Название пункта меню
                @include('includes.inputs.name', ['name' => 'name', 'value' => $menu->name, 'required' => true])
                <span class="form-error">Уж постарайтесь, введите хотя бы 2 символа!</span>
            </label>

            <label>Введите ссылку
                @include('includes.inputs.text-en', ['name'=>'alias', 'value'=>$menu->alias])
            </label>

            <label>Страница:
                <select name="page_id" class="pages-select" placeholder="Не выбрано">
                    <option value="">Не выбрано</option>
                    @php
                    echo $pages_list;
                    @endphp
                </select>
            </label>

            @include('includes.control.checkboxes', ['item' => $menu])

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
                @include('includes.inputs.text-en', ['name'=>'icon', 'value'=>$menu->icon])
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
