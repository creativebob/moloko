<div class="grid-x grid-padding-x">

    <div class="small-12 medium-7 cell">

        {!!  Form::open([
            'route' => 'photos.ajax_store',
            'data-abide',
            'novalidate',
            'files' => 'true',
            'class' => 'dropzone',
            'id' => 'my-dropzone'
        ]
        ) !!}

        {!! Form::hidden('name', $article->name) !!}
        {!! Form::hidden('id', $article->id) !!}
        {!! Form::hidden('entity', 'articles') !!}
        {!! Form::hidden('album_id', $item->album_id) !!}

        {!! Form::close() !!}
        {{--							<dropzone-component :dropzone="{{ $dropzone }}"></dropzone-component>--}}

        <ul class="grid-x small-up-4 tabs-margin-top" id="photos-list">

            @isset($article->album_id)
                @include('system.pages.marketings.photos.photos', ['album' => $article->album])
            @endisset

        </ul>
    </div>

    <div class="small-12 medium-5 cell" id="photo-edit-partail">

        {{-- Форма редактированя фотки --}}

    </div>
</div>
