@isset($catalog_services)
    @if($catalog_services->items->isNotEmpty())
        <ul class="vertical menu" id="sidebar-catalog_services">
            @foreach (buildTree($catalog_services->items) as $item)
                @if(is_null($item->parent_id))
                    <li>
                        <h3>{{ $item->name }}</h3>
                        @isset ($item->childrens)
                            <ul class="vertical menu accordion-menu" data-multi-open="false" data-accordion-menu>
                                @include('viandiesel.composers.catalogs_services.sidebar_item', ['items' => $item->childrens])
                            </ul>
                        @endisset

                    </li>
                @endif
            @endforeach
        </ul>
    @endif

    {{-- Фильтры --}}
    @include('project.composers.manufacturers.manufacturers_from_impacts_from_services')
    @include('project.composers.manufacturers.manufacturers_from_owners_impacts_from_services')

    @if((request('part-brand')) || (request('car-brand')))
        <div class="grid-x grid-margin-x">
            <div class="cell text-center">
                <a class="reset-filter" href="{{ route('project.catalogs_services_items.show', [$catalogs_services_item->catalog->slug, $catalogs_services_item->slug]) }}">Показать
                    все</a>
            </div>
        </div>
    @endif

@endisset
