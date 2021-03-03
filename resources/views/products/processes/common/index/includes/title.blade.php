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

                @can('create', $class)
                    <a class="icon-add sprite top" data-open="modal-create" data-tooltip tabindex="2" title="Добавить позицию"></a>
                @endcan

                @php
                    $model = $pageInfo->entity->model;
                    $class = $model . 'sCategory';
                @endphp
{{--                {{ dd($class) }}--}}

                @can('index', $class)
                <a href="/admin/{{ $pageInfo->alias}}_categories" class="icon-category sprite top" data-tooltip tabindex="2" title="Настройка категорий"></a>
                @endcan

{{--                @can('index', $class)--}}
{{--                <a href="/admin/{{ $pageInfo->alias}}_graphs" class="icon-stock sprite top" data-tooltip tabindex="2" title="График"></a>--}}
{{--                @endcan--}}

            </div>
            <div class="top-bar-right">

                <a class="icon-filter sprite
                @if(!(count(request()->input()) == 1 && request()->input(['page'])) && count(request()->input())) filtration-active @endif
                    "></a>


                <search-processes-component alias="{{ $pageInfo->alias }}"></search-processes-component>
{{--                <input class="search-field" type="search" id="search_field" name="search_field" placeholder="Поиск" />--}}
                {{-- <button type="button" class="icon-search sprite button"></button> --}}

            </div>


        </div>



        <div id="port-result-search">
        </div>

        {{-- Блок фильтров --}}
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
                        {{ Form::open(['route' => $pageInfo->alias . '.index', 'data-abide', 'novalidate', 'method'=>'GET', 'id' => 'filter-form', 'class' => 'grid-x grid-padding-x inputs']) }}

                        @includeIf($pageInfo->entity->view_path.'.includes.filters')

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
