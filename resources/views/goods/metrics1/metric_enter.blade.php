@foreach ($item->metrics as $metric)
@include('goods.metrics.metric_input', ['metric' => $metric, 'metrics_values' => null])
@endforeach