@if (!empty($cur_news->albums))
@foreach ($cur_news->albums as $album)
  <tr>
    <td>{{ $album->name }}</td>
    <td>{{ $album->albums_category->name }}</td>
    <td class="td-delete"><a class="icon-delete sprite" data-open="item-delete-ajax"></a></td>
  </tr>
@foreach
@endif