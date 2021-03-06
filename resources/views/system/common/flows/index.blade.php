@extends('layouts.app')

@section('inhead')

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
    {{-- Количество элементов --}}
    {{ $flows->isNotEmpty() ? num_format($flows->total(), 0) : 0 }}
@endsection

@section('title-content')
    {{-- Таблица --}}
    @include('system.common.flows.includes.title')
@endsection

@section('content')

    {{-- Таблица --}}

    <div class="grid-x" id="pagination">
        <div class="small-6 cell pagination-head">
            {{ $flows->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
        </div>
    </div>

    <div class="grid-x">
        <div class="small-12 cell">

            <table class="content-table tablesorter" id="content" class="content-flows" data-sticky-container
                   data-entity-alias="flows">

                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2"
                       data-sticky-on="medium" data-top-anchor="head-content:bottom">

                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th">
                        <input type="checkbox" class="table-check-all" name="" id="check-all">
                        <label class="label-check" for="check-all"></label>
                    </th>
                    <th class="td-date">Дата начала</th>
                    <th class="td-date">Дата завершения</th>
                    <th class="td-name">Название</th>
                    <th class="td-capacity-min">Min кол-во</th>
                    <th class="td-capacity-max">Max кол-во</th>
                    <th class="td-capacity-fact">Фактическое кол-во</th>
                    <th class="td-staff">Сотрудники</th>
                    <th class="td-manufacturer">Исполнитель</th>
                    <th class="td-company">Компания</th>
                    <th class="td-delete"></th>
                </tr>

                </thead>

                <tbody data-tbodyId="1" class="tbody-width">

                @if($flows->isNotEmpty())
                    @foreach($flows as $flow)

                        <tr
                            class="item @if($flow->moderation == 1)no-moderation @endif @if($flow->process->archive == 1) archive-cmv @endif"
                            id="{{ $pageInfo->alias }}-{{ $flow->id }}"
                            data-name="{{ $flow->process->process->name }}"
                        >
                            <td class="td-drop">
                                <div class="sprite icon-drop"></div>
                            </td>

                            <td class="td-checkbox checkbox">
                                <input type="checkbox" class="table-check" name="flow_id" id="check-{{ $flow->id }}"
                                       @if(!empty($filter['booklist']['booklists']['default']))
                                       @if (in_array($flow->id, $filter['booklist']['booklists']['default'])) checked
                                    @endif
                                    @endif
                                >
                                <label class="label-check" for="check-{{ $flow->id }}"></label>
                            </td>

                            <td class="td-date">
                                <span>{{ $flow->start_at->format('d.m.Y') }}</span><br>
                                <span class="tiny-text">{{ $flow->start_at->format('H:i') }}</span>
                            </td>

                            <td class="td-date">
                                <span>{{ $flow->finish_at->format('d.m.Y') }}</span><br>
                                <span class="tiny-text">{{ $flow->finish_at->format('H:i') }}</span>
                            </td>

                            <td class="td-name" title="ID ТМЦ: {{ $flow->process->id }}">
                                @can('update', $flow)
                                    <a href="{{ route($flow->getTable() . '.edit', $flow->id) }}">{{ $flow->process->process->name }}</a>
                                @else
                                    {{ $flow->process->process->name }}
                                @endcan
                                <br><span class="tiny-text">{{ $flow->process->category->name }}</span>
                            </td>
                            <td class="td-capacity-min">
                                {{ $flow->capacity_min ?? '' }}
                            </td>
                            <td class="td-capacity-max">
                                {{ $flow->capacity_max ?? '' }}
                            </td>
                            <td class="td-capacity-fact">
                                {{ optional($flow->clients)->count() ?? '' }}
                            </td>
                            <td class="td-staff">
                                {{ $flow->staff->implode('user.name', ', ') ?? '' }}
                            </td>
                            <td class="td-manufacturer">
                                {{ $flow->manufacturer->company->name ?? '' }}
                            </td>

                            <td class="td-company">
                                @if(!empty($flow->company->name))
                                    {{ $flow->company->designation ?? $flow->company->name }}
                                @else

                                    @if($flow->system == null)
                                        Шаблон
                                    @else
                                        Системная
                                    @endif

                                @endif
                            </td>
                            <td class="td-delete">
                                @can('delete', $flow)
                                    <a class="icon-delete sprite" data-open="item-delete"></a>
                                @endcan
                            </td>
                        </tr>

                    @endforeach
                @endif

                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="grid-x" id="pagination">
        <div class="small-6 cell pagination-head">
            <span class="pagination-title">Кол-во записей: {{ $flows->count() }}</span>
            {{ $flows->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
        </div>
    </div>
@endsection

@section('modals')
    @php
        $arr = explode('_', $pageInfo->alias);
        $processAlias = $arr[0];
    @endphp
    @include('system.common.flows.create', ['autoInitiated' => false, 'processAlias' => $processAlias])
    {{-- Модалка удаления с refresh --}}
    @include('includes.modals.modal-delete')
@endsection

@push('scripts')
    {{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
    @include('includes.scripts.tablesorter-script')
    @include('includes.scripts.sortable-table-script')
    @include('includes.scripts.checkbox-control')

    {{-- Скрипт отображения на сайте --}}
    @include('includes.scripts.ajax-display')

    {{-- Скрипт системной записи --}}
    @include('includes.scripts.ajax-system')

    {{-- Скрипт модалки удаления --}}
    @include('includes.scripts.modal-delete-script')
    @include('includes.scripts.delete-ajax-script')

    <script>
        // ----------- Добавление -------------

        // Название сущности
        var entity = '{{ $pageInfo->alias }}';

        // Открываем модалку
        $(document).on('click', '[data-open="modal-create"]', function () {
            $.get('/admin/' + entity + '/create', function (html) {
                $('#modal').html(html).foundation();
                $('#modal-create').foundation('open');
            });
        });

        // Закрываем модалку
        $(document).on('click', '.add-item', function () {
            $('.reveal-overlay').remove();
        });
    </script>

    {{-- Скрипт модалки удаления --}}
    @include('includes.scripts.modal-delete-script')

@endpush
