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

{{--                @can('create', App\Employee::class)--}}
{{--                    <a href="{{ route('employees.create') }}" class="icon-add sprite top" data-tooltip tabindex="2" title="Добавить позицию"></a>--}}
{{--                @endcan--}}

            </div>
            <div class="top-bar-right">

                @if (isset($filter))
                <a class="icon-filter sprite @if ($filter['status'] == 'active') filtration-active @endif"></a>
                @endif

                <input class="search-field" type="search" id="search_field" name="search_field" placeholder="Поиск" />
            </div>
        </div>

        <div id="port-result-search">
        </div>

        {{-- Блок фильтров --}}
        @if (isset($filter))

        <div class="grid-x">
            <div class="small-12 cell filters fieldset-filters" id="filters">
                <div class="grid-padding-x">
                    <div class="small-12 cell text-right">
                        {{ link_to(Request::url() . '?filter=disable', 'Сбросить', ['class' => 'small-link']) }}
                    </div>
                </div>
                <div class="grid-padding-x">
                    <div class="small-12 cell">
                        {{ Form::open(['url' => Request::url(), 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET', 'id' => 'filter-form', 'class' => 'grid-x grid-padding-x inputs']) }}

                        @includeIf($page_info->entity->view_path.'.filters')

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

            {{-- Дополнительные кнопки --}}
            <div class="black-button-group small-12 cell">
                @if($employees_active_count > 0)
                    <a class="button tiny hollow right dismissed" href="{{ route('employees.index') }}">Действующие сотрудники</a>
                @endif
            </div>


        </div>

        @endif
    </div>
</div>
