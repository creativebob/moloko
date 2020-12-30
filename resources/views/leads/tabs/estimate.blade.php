<div class="grid-x grid-padding-x wrap-estimate-title">
    <div class="small-12 medium-shrink cell estimate-title">
        <p>Клиентский заказ
            @if($lead->estimate->registered_at)
                № {{ $lead->estimate->number ?? '' }}
                от {{ $lead->estimate->registered_at->format('d.m.Y') }} <span
                    class="tiny-text">({{ $lead->estimate->registered_at->getTranslatedShortDayName('dd') }})</span>
            @endif
        </p>
    </div>
    <div class="small-12 medium-auto cell estimate-control">
        <a href="/admin/leads/{{ $lead->id }}/print_sticker_stock" target="_blank">
            <span class="button-print-stock-sticker" title="Маркер для склада"></span>
        </a>
    </div>
</div>

<div class="grid-x grid-margin-x">
    <div class="small-12 medium-12 large-12 cell">
        <estimate-component
            :estimate='@json($lead->estimate)'
            :settings='@json(auth()->user()->company->settings)'
            :outlet-settings='@json($outlet->settings)'
        ></estimate-component>
    </div>
</div>
