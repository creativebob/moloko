<div class="small-12 cell up-input-button text-center">
<a id="mode-default" class="modes">Вернуться</a>
</div>
<label>Введите название новой группы
  @include('includes.inputs.string', ['value'=>null, 'name'=>'goods_product_name', 'required'=>'required'])
</label>

{{ Form::hidden('mode', 'mode-add', ['id' => 'mode']) }}