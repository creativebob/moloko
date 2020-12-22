@isset($estimatesTotals)
<tr>
    <td class="td-drop"></td>
    <td colspan="3">Итоговые значения:</td>
    <td class="td-name"></td>
    <td class="td-phone"></td>
    <td class="td-amount">{{ num_format($estimatesTotals->amount, 0) }}</td>
    <td class="td-discount-currency">{{ num_format($estimatesTotals->discount_currency, 0) }}</td>
    <td class="td-total" title="С вычетом партнерской доли: {{ num_format($estimatesTotals->total - $estimatesTotals->partner_currency, 0) }}">{{ num_format($estimatesTotals->total, 0) }}</td>
    <td class="td-payment"></td>
    @if(extra_right('margin-show'))
        <td class="td-margin_currency"></td>
    @endif
    @if(extra_right('partner-currency-show'))
        <td class="td-partner">
            {{ num_format($estimatesTotals->partner_currency, 0) }}       
        </td>
    @endif
    @if(extra_right('share-currency-show'))
        <td class="td-share-currency">{{ num_format($estimatesTotals->share_currency, 0) }}</td>
    @endif
    @if(extra_right('principal-currency-show'))
        <td class="td-principal-currency">{{ num_format($estimatesTotals->principal_currency, 0) }}</td>
    @endif
    <td class="td-saled"></td>
    <td class="td-dissmissed"></td>
</tr>
    @endisset

