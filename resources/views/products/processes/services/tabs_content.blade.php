{{-- Каталоги --}}
<div class="tabs-panel" id="catalogs">
    <div class="grid-x grid-padding-x">

        <div class="small-12 medium-6 cell">
            @include('products.processes.services.prices.catalogs')

            <table class="table-compositions">
                <thead>
                    <tr>
                        <th>Каталог:</th>
                        <th>Пункт:</th>
                        <th>Филиал:</th>
                        <th>Цена:</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="table-prices">

                    @if ($item->prices->isNotEmpty())
                    @foreach ($item->prices as $price)
                    @include('products.processes.services.prices.price', ['prices_service' => $price])
                    @endforeach
                    @endif

                </tbody>
            </table>

        </div>
    </div>
</div>

<div class="tabs-panel" id="workflows">

    {{-- Состав --}}
    @include('products.processes.services.workflows.workflows')

</div>

