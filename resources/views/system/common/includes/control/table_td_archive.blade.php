<td class="td-delete">
@can('delete', $item)
    <a class="icon-delete sprite" data-open="item-archive"></a>
@endcan
</td>

@section('modals')
    @include('includes.modals.modal-archive')
@endsection

@push('scripts')
    @include('includes.scripts.modal-archive-script')
@endpush
