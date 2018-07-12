@foreach ($service->album->photos as $photo)
<li class="cell">
  <img src="/storage/{{ $photo->company_id }}/media/albums/{{ $photo->album_id }}/img/small/{{ $photo->name }}" alt="Фотография альбома" data-id="{{ $photo->id }}">
</li>
@endforeach