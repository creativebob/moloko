@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.dropzone-inhead')
    @include('includes.scripts.fancybox-inhead')
    @include('includes.scripts.sortable-inhead')

    @if ($entity == 'goods')
        @include('includes.scripts.chosen-inhead')
        @include('products.articles.goods.attachments.class')
        @include('products.articles.goods.raws.class')
        @include('products.articles.goods.containers.class')
    @endif
@endsection

@section('title', $title)

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $pageInfo, $article))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">{{ $title }} &laquo{{ $article->name }}&raquo</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@php
    $disabled = $article->draft == 0 ? true : null;
@endphp

@section('content')
    <div class="grid-x tabs-wrap">
        <div class="small-12 cell">
            <ul class="tabs-list" data-tabs id="tabs">

                <li class="tabs-title is-active">
                    <a href="#tab-general" aria-selected="true">Общая информация</a>
                </li>

                {{-- Табы для сущности --}}
                @includeIf($pageInfo->entity->view_path . '.tabs')

                @if($article->kit == 0)
                    <li class="tabs-title">
                        <a data-tabs-target="tab-parts" href="#tab-parts">Части</a>
                    </li>
                @endif

                <li class="tabs-title">
                    <a data-tabs-target="tab-photos" href="#tab-photos">Фотографии</a>
                </li>

                <li class="tabs-title">
                    <a data-tabs-target="tab-options" href="#tab-options">Опции</a>
                </li>

                @can('index', App\Site::class)
                    <li class="tabs-title">
                        <a data-tabs-target="tab-site" href="#tab-site">Настройка для сайта</a>
                    </li>
                @endcan

                <li class="tabs-title">
                    <a data-tabs-target="tab-seo" href="#tab-seo">SEO</a>
                </li>

            </ul>
        </div>
    </div>

    <div class="grid-x tabs-wrap inputs">
        <div class="small-12 cell tabs-margin-top">
            <div class="tabs-content" data-tabs-content="tabs">

                {{ Form::model($article, [
                    'route' => [$entity.'.update', $item->id],
                    'data-abide',
                    'novalidate',
                    'files' => 'true',
                    'id' => 'form-edit'
                ]
                ) }}
                @method('PATCH')

                {!! Form::hidden('previous_url', $previous_url ?? null) !!}

                {{-- Общая информация --}}
                <div class="tabs-panel is-active" id="tab-general">
                    @include('products.articles.common.edit.tabs.general')
                </div>

                {{-- Табы для сущности --}}
                @includeIf($pageInfo->entity->view_path . '.tabs_content')

                {{-- Дополнительные опции --}}
                <div class="tabs-panel" id="tab-parts">
                    @include('products.articles.common.edit.tabs.parts', ['id' => $item->id])
                </div>

                {{-- Дополнительные опции --}}
                <div class="tabs-panel" id="tab-options">
                    @include('products.articles.common.edit.tabs.options')
                </div>

                {{-- Сайт --}}
                @can('index', App\Site::class)
                    <div class="tabs-panel" id="tab-site">
                        @include('products.articles.common.edit.tabs.site')
                    </div>
                @endcan

                {{-- SEO --}}
                <div class="tabs-panel" id="tab-seo">
                    @include('system.common.tabs.seo', ['seo' => $article->seo])
                </div>

                {{ Form::close() }}

                {{-- Фотографии --}}
                <div class="tabs-panel" id="tab-photos">
                    @include('products.articles.common.edit.tabs.photos')
                </div>

            </div>
        </div>
    </div>
@endsection

@section('modals')
    @includeIf($pageInfo->entity->view_path . '.modals')
@endsection

@push('scripts')
    <script>
        // Основные настройки
        var category_entity = '{{ $category_entity }}';

        // При клике на фотку подствляем ее значения в блок редактирования
        $(document).on('click', '#photos-list .edit', function (event) {
            event.preventDefault();

            // Удаляем всем фоткам активынй класс
            $('#photos-list img').removeClass('active');
            $('#photos-list img').removeClass('updated');

            // Наваливаем его текущей
            $(this).addClass('active');

            // Получаем инфу фотки
            $.post('/admin/photo_edit/' + $(this).data('id'), function (html) {
                // alert(html);
                $('#photo-edit-partail').html(html);
            })
        });

        // При сохранении информации фотки
        $(document).on('click', '#form-photo-edit .button-photo-edit', function (event) {
            event.preventDefault();

            let button = $(this);
            button.prop('disabled', true);

            let id = $(this).closest('#form-photo-edit').find('input[name=id]').val();
            // alert(id);

            // Записываем инфу и обновляем
            $.ajax({
                url: '/admin/photo_update/' + id,
                type: 'PATCH',
                data: $(this).closest('#form-photo-edit').serialize(),
                success: function (res) {

                    if (res == true) {
                        button.prop('disabled', false);

                        $('#photos-list').find('.active').addClass('updated').removeClass('active');
                    } else {
                        alert(res);
                    }
                    ;
                }
            })
        });

        // При сохранении удалении фотки
        $(document).on('click', '#form-photo-edit .button-delete-photo', function (event) {
            event.preventDefault();

            let button = $(this);
            button.prop('disabled', true);

            let id = $(this).data('photo-id');
            // alert(id);

            // Записываем инфу и обновляем
            $.ajax({
                url: '/admin/photo_delete/' + id,
                type: 'DELETE',
                success: function (html) {
                    $('#photos-list').html(html);
                    $('#photo-edit-partail').html('');
                }
            })
        });
    </script>

    {{--	@include('products.articles.common.edit.change_articles_groups_script')--}}
    @include('products.articles.common.edit.change_packages_script', [
        'package_unit' => $article->unit->abbreviation
    ])

    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.upload-file')
    @include('includes.scripts.ckeditor')

    @include('includes.scripts.dropzone', [
        'settings' => $settings,
        'item_id' => $article->id,
        'item_entity' => 'articles'
    ])

    {{-- Проверка поля на существование --}}
    @include('includes.scripts.check', [
        'entity' => 'articles',
        'id' => $article->id
    ])

    {{--	@includeIf($pageInfo->entity->view_path . '.scripts')--}}
@endpush
