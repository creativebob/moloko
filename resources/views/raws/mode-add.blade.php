<div class="up-input-button">
<a id="mode-default" class="modes">Вернуться</a>
</div>
<label>Введите название новой группы
  @include('includes.inputs.string', ['value'=>null, 'name'=>'raws_product_name', 'required' => true])
</label>
<label>Название сырья
  @include('includes.inputs.string', ['value'=>null, 'name'=>'name', 'required' => true])
  <div class="item-error">Названия сырья и группы сырья не должны совпадать!</div>
</label>

{{ Form::hidden('mode', 'mode-add') }}