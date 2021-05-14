@foreach ($album->photos as $photo)
    <li class="cell">
        <img src="{{ getPhotoInAlbumPath($photo) }}" alt="{{ $photo->title }}" data-id="{{ $photo->id }}" class="edit">
        <p class="delete" data-id="{{ $photo->id }}">Удалить</p>
    </li>
@endforeach
