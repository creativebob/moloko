@if ($item->metrics->isNotEmpty())
@include('goods.metrics.metric_validation')

<fieldset class="fieldset-access">
    <legend>Метрики</legend>
    <div id="metrics-list">

        @foreach ($item->metrics as $metric)
        @include('goods.metrics.metric_input', $metric)
        @endforeach

    </div>
</fieldset>
@endif