@foreach($pricesServices as $price)
    <tr>
        <td>{{ $price->service->process->name }}</td>
        <td class="length">{{ $price->service->process->lengthTrans }} {{ $price->service->process->unit_length->abbreviation }}.</td>
        <td class="price">{{ num_format($price->price, 0) }} <span class="currency">{{ $price->currency->symbol }}</span></td>
    </tr>
@endforeach
