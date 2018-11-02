@foreach ($item->compositions as $composition)
@include ('goods.compositions.composition_input', $composition)
@endforeach