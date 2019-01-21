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

					<label>Название
						@include('includes.inputs.name', ['required' => true])
					</label>

					<label>Введите ссылку
						@include('includes.inputs.text-en', ['name' => 'alias'])
					</label>

					<label>Страница
						@include('includes.selects.pages', ['site_id' => $site_id])
					</label>

					@include('includes.control.checkboxes', ['item' => $menu])
				</div>
			</div>
		</div>

		{{-- Настройки --}}
		<div class="tabs-panel" id="settings">
			<div class="grid-x grid-padding-x align-center">
				<div class="small-10 cell">

					<label>Добавляем пункт в:
						<select name="parent_id" class="menu_list">

						</select>
					</label>

					<label>Введите имя иконки
						@include('includes.inputs.text-en', ['name' => 'icon'])
					</label>

					{{ Form::hidden('menu_id', $menu->id, ['id' => 'menu_id']) }}
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

<script type="text/javascript">
    $.getScript("/crm/js/inputs_mask.js");
</script>
