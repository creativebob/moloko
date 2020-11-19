{{-- Заголовок и фильтры --}}
<div data-sticky-container id="head-content">
    <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-sticky-on="small"
         data-top-anchor="head-content:top">
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

                <div class="grid-x">
                    <div class="cell small-12">
                        <a
                            href="{{ route('leads.index', ['period_date_min' => today()->format('d.m.Y'), 'period_date_max' => today()->format('d.m.Y')]) }}"
                            class="button tiny hollow"
                        >День</a>
                        <a
                            href="{{ route('leads.index', ['period_date_min' => \Carbon\Carbon::create('first day of this month')->format('d.m.Y'), 'period_date_max' => \Carbon\Carbon::create('last day of this month')->format('d.m.Y')]) }}"
                            class="button tiny hollow"
                        >Месяц</a>
                        <a
                            href="{{ route('leads.index', ['period_date_min' => \Carbon\Carbon::create('first day of January ' . now()->format('Y'))->format('d.m.Y'), 'period_date_max' => \Carbon\Carbon::create('last day of December ' . now()->format('Y'))->format('d.m.Y')]) }}"
                            class="button tiny hollow"
                        >Год</a>
                    </div>

                </div>

                <div class="cell small-12">
                    <a class="icon-filter sprite
                @if(!(count(request()->input()) == 1 && request()->input(['page'])) && count(request()->input())) filtration-active @endif
                        "></a>

                    <search-leads-component></search-leads-component>
                </div>
            </div>
        </div>

        <div class="grid-x">
            <div class="small-12 cell filters fieldset-filters" id="filters">
                <div class="grid-padding-x">
                    <div class="small-12 cell text-right">
                        {!! Form::open(['route' => ['reset_filter', $pageInfo->alias]]) !!}
                        {!! Form::submit('Сбросить', ['class'=>'small-link filter-reset']) !!}
                        {!! Form::close() !!}
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
