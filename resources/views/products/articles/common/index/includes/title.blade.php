{{-- Заголовок и фильтры --}}
<div data-sticky-container id="head-content">
    <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-sticky-on="small"
         data-top-anchor="head-content:top">
        <div class="top-bar head-content">
            <div class="top-bar-left">
                <h2 class="header-content">{{ $pageInfo->title }} @if(strpos(request()->url(), 'archives')) в
                    архиве @endif
                    <span class="content-count" title="Общее количество">
                        @yield('content-count')
                    </span>
                </h2>

                @can('create', $class)
                    <a class="icon-add sprite top" data-open="modal-create" data-tooltip tabindex="2"
                       title="Добавить позицию"></a>
                @endcan

                @php
                    $model = $pageInfo->entity->model;
                    if ($model == 'App\Goods') {
                        $class = $model . 'Category';
                    } else {
                        $class = $model . 'sCategory';
                    }
                @endphp
                {{--                {{ dd($class) }}--}}

                @can('index', $class)
                    <a href="/admin/{{ $pageInfo->alias}}_categories" class="icon-category sprite top" data-tooltip
                       tabindex="2" title="Настройка категорий"></a>
                @endcan

                @if($entity != 'impacts')
                    @can('index', \App\Models\System\Documents\Consignment::class)
                        <a href="{{ route('consignments.index') }}" class="icon-consignment sprite top" data-tooltip
                           tabindex="2" title="Накладные"></a>
                    @endcan
                @endif

                @php
                    if ($pageInfo->alias == 'goods') {
                        $class = "App\Models\System\Stocks\GoodsStock";
                    } else {
                        $array = explode('\\', $pageInfo->entity->model);
                        $name = end($array);
                        $class = 'App\Models\System\Stocks\\' . $name . 'sStock';
                    }
                @endphp

                @can('index', $class)
                    <a href="/admin/{{ $pageInfo->alias}}_stocks" class="icon-stock sprite top" data-tooltip
                       tabindex="2" title="Склад"></a>
                @endcan

            </div>
            <div class="top-bar-right">

                @if (isset($filter))
                    <a class="icon-filter sprite @if ($filter['status'] == 'active') filtration-active @endif"></a>
                @endif

                <search-articles-component alias="{{ $pageInfo->alias }}"></search-articles-component>

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
                            {{ link_to(Request::url(), 'Сбросить', ['class' => 'small-link']) }}
                        </div>
                    </div>
                    <div class="grid-padding-x">
                        <div class="small-12 cell">
                            {{ Form::open(['url' => Request::url(), 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET', 'id' => 'filter-form', 'class' => 'grid-x grid-padding-x inputs']) }}

                            @includeIf($pageInfo->entity->view_path.'.includes.filters')

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
                    @isset($archivesCount)
                        @if($archivesCount > 0)
                            <a class="button tiny hollow right dismissed" href="{{ route("{$entity}.archives") }}">Архив: {{ $archivesCount }}</a>
                        @endif
                    @else
                        <a class="button tiny hollow right dismissed" href="{{ route("{$entity}.index") }}">Обычные</a>
                    @endisset

                </div>


            </div>

        @endif
    </div>
</div>
