<div class="small-12 cell up-input-button text-center">
<a id="mode-default" class="modes">Вернуться</a>
</div>
<label>Введите название новой группы
  @include('includes.inputs.string', ['name' => 'group_name', 'required' => true])
</label>

{{ Form::hidden('mode', 'mode-add', ['id' => 'mode']) }}
