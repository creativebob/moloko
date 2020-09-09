{{-- dd($lead->filial) --}}
@extends('layouts.print')

@section('title', 'Печать чека')

@section('print-content')
    <div class="grid-x check-order">
        <div class="cell small-6 left-part-check">
            <div class="grid-x company-info">
                <div class="cell shrink">
                    <img src="{{ $lead->company->black->path }}" width="100mm" alt="Логотип">
                </div>
                <div class="cell auto contact-info">
                    <span class="company-phone">{{ decorPhone($lead->filial->main_phone->phone) }}</span><br>
                    @isset($domain)
                        <span class="company-site">{{ idn_to_utf8($domain->domain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46) }}</span>
                    @endisset
{{--                    <span class="company-site">{{ $lead->filial->name }}</span>--}}
                </div>
            </div>
            <div class="grid-x order-info">
                <div class="cell small-6">
                    <ul class="order-info-list">
                        <li><span class="order-date">Дата: </span><span>{{ $lead->created_at->format('d.m.Y') }}</span></li>
                        <li><span class="order-time">Время заказа: </span><span>{{ $lead->created_at->format('H:i') }}</span></li>
                    </ul>
                </div>
                <div class="cell small-6 client-info">
                    <ul class="client-info-list">
                        <li><span class="client-name">Имя: </span><span>{{ $lead->name }}</span></li>
                        <li><span class="client-city">Город: </span><span>{{ $lead->location->city->name }}</span></li>
                        <li><span class="client-address">Адрес клиента: </span><span>{{ $lead->location->address }}</span></li>
                        <li><span class="client-phone">Телефон: </span><span>{{ decorPhone($lead->main_phone->phone) }}</span></li>
                        <li><span class="client-time">Заказ на: </span><span class="shipment_time">{{ isset($lead->shipment_at) ? $lead->shipment_at->format('d.m.Y H:i') : 'Время не указано' }}</span></li>
                        {{-- <li><span class="client-points">Р/х для клиента:</span><span> </span></li> --}}
                    </ul>
                </div>
            </div>
            <div class="grid-x estimate-info">
                <table class="print_table">
                    <caption>Счёт № {{ $lead->estimate->number }}</caption>
                    <thead>
                        <tr>
                            <th>№</th>
                            <th>Наименование</th>
                            <th>Цена</th>
                            <th>Кол-во</th>
                            <th>Стоимость</th>
                            <th>Скидка</th>
                            <th>Итого</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lead->estimate->goods_items as $item)
                            <tr class="item">
                                <td></td>
                                <td>{{ $item->goods->article->name }}</td>
                                <td>{{ num_format($item->price, 0) }}</td>
                                <td>{{ num_format($item->count, 0) }}</td>
                                <td>{{ num_format($item->amount, 0) }}</td>
                                <td>{{ $item->discount_percent != 0 ? num_format($item->discount_percent, 0) . '%' : '' }}</td>
                                <td>{{ num_format($item->total, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">Итого:</td>
                            <td></td>
                            <td></td>
                            <td>{{ num_format($lead->estimate->amount, 0) }} {{ $item->currency->symbol }}</td>
                            <td></td>
                            <td>{{ num_format($lead->estimate->total, 0) }} {{ $item->currency->symbol }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Сумма всех скидок:</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ num_format($lead->estimate->discount_currency, 0) }} {{ $item->currency->symbol }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="cell small-6 right-part-check">
            <div class="grid-x company-info">
                <div class="cell shrink">
                    <img src="{{ $lead->company->black->path }}"  width="100mm" alt="Логотип">
                </div>
                <div class="cell auto contact-info">
                    <span class="company-phone">{{ decorPhone($lead->filial->main_phone->phone) }}</span><br>
                    @isset($domain)
                        <span class="company-site">{{ idn_to_utf8($domain->domain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46) }}</span>
                    @endisset
{{--                    <span class="company-site">{{ $lead->filial->name }}</span>--}}
                </div>
            </div>
            <div class="grid-x order-info">
                <div class="cell small-6">
                    <ul class="order-info-list">
                        <li><span class="order-date">Дата: </span><span>{{ $lead->created_at->format('d.m.Y') }}</span></li>
                        <li><span class="order-time">Время заказа: </span><span>{{ $lead->created_at->format('H:i') }}</span></li>
                    </ul>
                </div>
                <div class="cell small-6 client-info">
                    <ul class="client-info-list">
                        <li><span class="client-name">Имя: </span><span>{{ $lead->name }}</span></li>
                        <li><span class="client-city">Город: </span><span>{{ $lead->location->city->name }}</span></li>
                        <li><span class="client-address">Адрес клиента: </span><span>{{ $lead->location->address }}</span></li>
                        <li><span class="client-phone">Телефон: </span><span>{{ decorPhone($lead->main_phone->phone) }}</span></li>
                        <li><span class="client-time">Заказ на: </span><span class="shipment_time">{{ isset($lead->shipment_at) ? $lead->shipment_at->format('d.m.Y H:i') : 'Время не указано' }}</span></li>
                        {{-- <li><span class="client-points">Р/х для клиента:</span><span> </span></li> --}}
                    </ul>
                </div>
            </div>
            <div class="grid-x estimate-info">
                <table class="print_table">
                    <caption>Счёт № {{ $lead->estimate->number }}</caption>
                    <thead>
                        <tr>
                            <th>№</th>
                            <th>Наименование</th>
                            <th>Цена</th>
                            <th>Кол-во</th>
                            <th>Стоимость</th>
                            <th>Скидка</th>
                            <th>Итого</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lead->estimate->goods_items as $item)
                            <tr class="item">
                                <td></td>
                                <td>{{ $item->goods->article->name }}</td>
                                <td>{{ num_format($item->price, 0) }}</td>
                                <td>{{ num_format($item->count, 0) }}</td>
                                <td>{{ num_format($item->amount, 0) }}</td>
                                <td>{{ $item->discount_percent != 0 ? num_format($item->discount_percent, 0) . '%' : '' }}</td>
                                <td>{{ num_format($item->total, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">Итого:</td>
                            <td></td>
                            <td></td>
                            <td>{{ num_format($lead->estimate->amount, 0) }} {{ $item->currency->symbol }}</td>
                            <td></td>
                            <td>{{ num_format($lead->estimate->total, 0) }} {{ $item->currency->symbol }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Сумма всех скидок:</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ num_format($lead->estimate->discount_currency, 0) }} {{ $item->currency->symbol }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
