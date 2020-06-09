{{-- Заголовок и фильтры --}}
<div data-sticky-container id="head-content">
    <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-sticky-on="small" data-top-anchor="head-content:top">
        <div class="top-bar head-content">
            <div class="top-bar-left">
                <h2 class="header-content">{{ $page_info->title }}
                    <span class="content-count" title="Общее количество">
                        @yield('content-count')
                    </span>
                </h2>

                @can('create', App\Client::class)
                    @if(!empty(Auth::user()->staff[0]))
                        <div class="button-group">
                            @if(extra_right('lead-regular'))
                            {{ link_to_route('clients.createClientCompany', '+ Компания', [], ['class' => 'button tiny']) }}
                            @endif

                            @if(extra_right('lead-service'))
                            {{ link_to_route('clients.createClientUser', '+ Физическое лицо', [], ['class' => 'button tiny']) }}
                            @endif
                        </div>
                    @endif
                @endcan

            </div>
            <div class="top-bar-right">

                <a class="icon-filter sprite
                @if(count(request()->input())) filtration-active @endif
                    "></a>

                <input class="search-field" type="search" id="search_field" name="search_field" placeholder="Поиск" />
                {{-- <button type="button" class="icon-search sprite button"></button> --}}

            </div>


        </div>



        <div id="port-result-search">
        </div>
        {{-- Подключаем стандартный ПОИСК --}}
        @include('includes.scripts.search-script')

        <div class="grid-x">
            <div class="small-12 cell filters fieldset-filters" id="filters">
                <div class="grid-padding-x">
                    <div class="small-12 cell text-right">
                        <a href="{{ route('clients.index') }}" class="small-link">Сбросить</a>
                    </div>
                </div>
                <div class="grid-padding-x">
                    <div class="small-12 cell">
                        {{ Form::open(['route' => 'clients.index', 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET', 'id' => 'filter-form', 'class' => 'grid-x grid-padding-x inputs']) }}

                        @include('clients.includes.filters')

                        <div class="small-12 cell text-center">
                            {{ Form::submit('Фильтрация', ['class'=>'button']) }}
                            <input hidden name="filter" value="active">
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
                <div class="grid-x">
                    <a class="small-12 cell text-center filter-close">
                        <button type="button" class="icon-moveup sprite"></button>
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>
