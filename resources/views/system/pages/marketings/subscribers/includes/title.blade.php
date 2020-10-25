{{-- Заголовок и фильтры --}}
<div data-sticky-container id="head-content">
    <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-sticky-on="small" data-top-anchor="head-content:top">
        <div class="top-bar head-content">
            <div class="top-bar-left">
                <h2 class="header-content">{{ $pageInfo->title }}
                    <span class="content-count" title="Общее количество">
                        @yield('content-count')
                    </span>
                </h2>

                @can('create', App\Subscriber::class)
                    @if($clients_count > 0)
                        <a href="{{ route('subscribers.create') }}" class="icon-add sprite top" data-tooltip tabindex="2" title="Добавить позицию"></a>
                    @endif
                @endcan

            </div>
            <div class="top-bar-right">

                @if (isset($filter))
                    <a class="icon-filter sprite @if ($filter['status'] == 'active') filtration-active @endif"></a>
                @endif

                <input class="search-field" type="search" id="search_field" name="search_field" placeholder="Поиск" />
                {{-- <button type="button" class="icon-search sprite button"></button> --}}

            </div>
        </div>

    </div>
</div>
