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

                @can('create', $class)
                @switch($type)

                @case('table')

                {{-- Кнопки добавления для страницы ЛИДЫ --}}
                @if($page_info->alias == 'leads')

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


                @elseif($page_info->alias == 'dealers')

                    @if(!empty(Auth::user()->staff[0]))
                        <div class="button-group">
                            @if(extra_right('lead-regular'))
                            {{ link_to_route('dealers.createDealerCompany', '+ Компания', [], ['class' => 'button tiny']) }}
                            @endif

                            @if(extra_right('lead-service'))
                            {{ link_to_route('dealers.createDealerUser', '+ Физическое лицо', [], ['class' => 'button tiny']) }}
                            @endif

                        </div>
                    @endif

                @elseif($page_info->alias == 'clients')

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


                {{-- Кнопки добавления для остальных страниц --}}
                @else

                    @if (isset($page_alias))
                    <a href="/admin/{{ $page_alias }}/create" class="icon-add sprite" data-tooltip class="top" tabindex="2" title="Добавить позицию"></a>
                    @else
                    <a href="/admin/{{ $page_info->alias}}/create" class="icon-add sprite" data-tooltip class="top" tabindex="2" title="Добавить позицию"></a>
                    @endif

                @endif

                @break

                @case('section-table')
                <a href="/admin/{{ $page_alias }}/create" class="icon-add sprite"></a>
                @break

                @case('menu')
                <a class="icon-add sprite" data-open="modal-create" data-tooltip class="top" tabindex="2" title="Добавить позицию"></a>

                @break

                @case('sections-menu')
                {{-- <h2 class="header-content">{{ $page_info->title .' &laquo;'. $name .'&raquo;' }}</h2> --}}
                <a class="icon-add sprite" data-open="modal-create"></a>
                @break

                @endswitch
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



        <div id="port-result-search">
        </div>
        {{-- Подключаем стандартный ПОИСК --}}
        @include('includes.scripts.search-script')

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

            {{-- Дополнительные кнопки приходящие с контроллера --}}
            <div class="black-button-group small-12 cell">
                @if(isset($add_buttons))
                    @foreach($add_buttons as $add_button)
                        <a class="button tiny hollow right {{ $add_button['class'] }}" href="{{ $add_button['href'] }}">{{ $add_button['text'] }}</a>
                    @endforeach
                @endif
            </div>


        </div>

        @endif
    </div>
</div>
