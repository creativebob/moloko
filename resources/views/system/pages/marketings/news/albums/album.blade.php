<tr class="item" id="albums-{{ $album->id }}" data-name="{{ $album->name }}">
  <td>{{ $album->name }}</td>
  <td>{{ $album->category->name }}</td>
  <td class="td-delete"><a class="icon-delete sprite" data-open="item-delete-ajax"></a></td>
  {{ Form::hidden('albums[]', $album->id) }}
</tr>
