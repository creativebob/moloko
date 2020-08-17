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

                @can('create', App\Lead::class)
                    @if(!empty(Auth::user()->staff[0]))
                        <div class="button-group">
                            @if(extra_right('lead-regular'))
                                {{ link_to_route('leads.create', '+ Обычное', ['lead_type' => 1], ['class' => 'button tiny']) }}
                            @endif

                            @if(extra_right('lead-service'))
                                {{ link_to_route('leads.create', '+ Сервис', ['lead_type' => 3], ['class' => 'button tiny']) }}
                            @endif

                            @if(extra_right('lead-dealer'))
                                {{ link_to_route('leads.create', '+ Дилер', ['lead_type' => 2], ['class' => 'button tiny']) }}
                            @endif
                        </div>
                    @endif
                @endcan

            </div>
            <div class="top-bar-right">

                <a class="icon-filter sprite
                @if(count(request()->input())) filtration-active @endif
                    "></a>

                    <search-leads-component></search-leads-component>
                {{-- <button type="button" class="icon-search sprite button"></button> --}}

            </div>


        </div>

        <div class="grid-x">
            <div class="small-12 cell filters fieldset-filters" id="filters">
                <div class="grid-padding-x">
                    <div class="small-12 cell text-right">
                        <a href="{{ route('leads.index') }}" class="small-link">Сбросить</a>
{{--                        {{ Form::open(['route' => 'leads.resetFilter']) }}--}}
{{--                        <button class="small-link">Сбросить</button>--}}
{{--                        {{ Form::close() }}--}}
                    </div>
                </div>
                <div class="grid-padding-x">
                    <div class="small-12 cell">
                        {{ Form::open(['route' => 'leads.index', 'data-abide', 'novalidate', 'method'=>'GET', 'id' => 'filter-form', 'class' => 'grid-x grid-padding-x inputs']) }}

                        @include('leads.includes.filters')

                        <div class="small-12 cell text-center">
                            {{ Form::submit('Фильтрация', ['class'=>'button']) }}
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
