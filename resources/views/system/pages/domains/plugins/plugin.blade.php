<tr class="item" id="plugins-{{ $plugin->id }}" data-name="{{ $plugin->account->name }}">
    <td>{{ $plugin->account->name }}</td>
    <td class="td-control">
{{--        @can('update', $plugin)--}}

            <a class="icon-list-edit sprite sprite-edit" data-open="modal-edit-plugin"></a>

{{--        @endcan--}}

{{--        @can('delete', $plugin)--}}
            <a class="icon-delete sprite" data-open="item-delete-ajax"></a>
{{--        @endcan--}}
    </td>
</tr>








