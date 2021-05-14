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

        {!! Form::hidden('name', $process->name) !!}
        {!! Form::hidden('id', $process->id) !!}
        {!! Form::hidden('entity', 'processes') !!}
        {!! Form::hidden('album_id', $item->album_id) !!}

        {!! Form::close() !!}

        <ul class="grid-x small-up-4 tabs-margin-top" id="photos-list">

            @isset($process->album_id)
                @include('system.pages.marketings.photos.photos', ['album' => $process->album])
            @endisset

        </ul>
    </div>

    <div class="small-12 medium-5 cell" id="photo-edit-partail">

        {{-- Форма редактированя фотки --}}

    </div>
</div>
