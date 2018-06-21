

@if($product->metrics_count > 0)
<fieldset class="fieldset-access">
  <legend>Свойства</legend>
  @each('products.metric-input', $product->metrics, 'metric')
</fieldset>
@endif

@if($product->compositions_count > 0)
<fieldset class="fieldset-access">
  <legend>Состав</legend>
  @each('products.composition-input', $product->compositions, 'composition')
</fieldset>
@endif



