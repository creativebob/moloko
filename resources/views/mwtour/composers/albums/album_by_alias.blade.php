@isset($album)
    <ul class="grid-x small-up-2 medium-up-2 large-up-3 album-list gallery">
        @foreach($album->photos as $photo)
            <li class="cell">
                <a data-fancybox="gallery" href="{{ getPhotoInAlbumPath($photo, 'large') }}">
                    <img src="{{ getPhotoInAlbumPath($photo) }}"
                         class="thumbnail" width="300" height="199" alt="{{ $photo->alt ?? $photo->name }}">
                    <span class="tool-search"></span>
                </a>
            </li>
        @endforeach
    </ul>
@endisset
