@isset($estimatesTotals)
<tr>
    <th class="td-drop"></th>
    <th class="td-checkbox checkbox-th"></th>
    <th class="td-date"></th>
    <th class="td-number"></th>
    <th class="td-name"></th>
    <th class="td-phone"></th>
    <th class="td-amount">{{ num_format($estimatesTotals->amount, 0) }}</th>
    <th class="td-discount-currency">{{ num_format($estimatesTotals->discount_currency, 0) }}</th>
    <th class="td-total">{{ num_format($estimatesTotals->total, 0) }}</th>
    <th class="td-payment"></th>
    @if(extra_right('margin-show'))
        <th class="td-margin_currency"></th>
    @endif
    <th class="td-partner">
    </th>
    @if(extra_right('share-currency-show'))
        <th class="td-share-currency">{{ num_format($estimatesTotals->share_currency, 0) }}</th>
    @endif
    @if(extra_right('principal-currency-show'))
        <th class="td-principal-currency">{{ num_format($estimatesTotals->principal_currency, 0) }}</th>
    @endif
    <th class="td-saled"></th>
    <th class="td-dissmissed"></th>
</tr>
    @endisset

