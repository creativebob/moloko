@isset($album)
    <ul class="grid-x small-up-3 medium-up-4 large-up-6 album-list gallery">
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
