@foreach($item->category->metrics as $metric)
    @include('products.common.edit.metrics.metric_input')
@endforeach
