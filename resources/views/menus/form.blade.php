<div class="grid-x tabs-wrap tabs-margin-top align-center">
	<div class="small-10 medium-4 cell">

		<ul class="tabs-list" data-tabs id="tabs">
			<li class="tabs-title is-active">
				<a href="#menu" aria-selected="true">Меню</a>
			</li>
			<li class="tabs-title">
				<a data-tabs-target="settings" href="#settings">Настройки</a>
			</li>
		</ul>

	</div>
</div>

<div class="tabs-wrap inputs">
	<div class="tabs-content" data-tabs-content="tabs">

		{{-- Основные --}}
		<div class="tabs-panel is-active" id="menu">
			<div class="grid-x grid-padding-x align-center">
				<div class="small-10 cell">

					{{ Form::hidden('menu_id', $menu->id, ['id' => 'menu-id']) }}

					<label>Название
						@include('includes.inputs.name', ['required' => true])
					</label>

					<label>Введите ссылку
						@include('includes.inputs.text-en', ['name' => 'alias'])
					</label>

					<label>Страница
						@include('includes.selects.pages', ['site_id' => $site_id])
					</label>

					<div class="small-12 cell checkbox">
						{{ Form::checkbox('new_blank', 1, null, ['id' => 'new_blank']) }}
						<label for="new_blank"><span>Новая вкладка</span></label>
					</div>

					@include('includes.control.checkboxes', ['item' => $menu])

				</div>
			</div>
		</div>

		{{-- Настройки --}}
		<div class="tabs-panel" id="settings">
			<div class="grid-x grid-padding-x align-center">
				<div class="small-10 cell">

					@isset ($category_id)
					<label>Добавляем пункт в:
						@include('includes.selects.menus', ['navigation_id' => $navigation_id, 'parent_id' => $parent_id, 'item_id' => $menu->id])
					</label>
					@endisset

					<label>Введите имя иконки (class)
						@include('includes.inputs.text-en-space', ['name' => 'icon'])
					</label>

					<label>Тег title для ссылки
						@include('includes.inputs.name', ['name' => 'title'])
					</label>
					
					<div class="small-12 cell checkbox">
						{{ Form::checkbox('text_hidden', 1, null, ['id' => 'text_hidden']) }}
						<label for="text_hidden"><span>Показывать только иконку (Текст ссылки скрыть)</span></label>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

<div class="grid-x align-center">
	<div class="small-6 medium-4 cell text-center">
		{{ Form::submit($submit_text, ['class' => 'button modal-button ' . $class]) }}
	</div>
</div>

<script type="application/javascript">
    $.getScript("/js/system/jquery.maskedinput.js");
    $.getScript("/js/system/inputs_mask.js");
</script>
