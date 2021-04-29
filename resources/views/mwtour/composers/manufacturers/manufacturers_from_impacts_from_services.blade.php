@if($manufacturers->isNotEmpty())
    <h3 class="h3 filter">Фильтр по бренду производителя запчасти:</h3>
    <ul class="grid-x grid-margin-x small-up-2 filter-manufacturer-list">
        @foreach($manufacturers as $manufacturer)
            <li class="cell text-center @if($manufacturer->company->name == request('part-brand')) active @endif">
                <a href="{{ route('project.catalogs_services_items.show', [$catalogs_services_item->catalog->slug, $catalogs_services_item->slug, 'part-brand' => $manufacturer->company->name]) }}">
                    <img
                        src="{{ isset($manufacturer->company->color) ? $manufacturer->company->color->path : $manufacturer->company->photo->path }}"
                        alt="Логотип {{ $manufacturer->company->name }}" width="80" height="80">
                </a>
            </li>
        @endforeach
    </ul>
@endif
