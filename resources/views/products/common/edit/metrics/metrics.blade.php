@if ($item->category->metrics->isNotEmpty())
@include('products.common.edit.metrics.metric_validation')

<fieldset class="fieldset-access">
    <legend>Метрики</legend>
    <div id="metrics-list">

        @foreach ($item->category->metrics as $metric)
        @include('products..common.edit.metrics.metric_input')
        @endforeach

    </div>
</fieldset>
@endif
