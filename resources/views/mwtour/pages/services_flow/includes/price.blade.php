@if($serviceFlow->process->prices->isNotEmpty())
    @if($serviceFlow->process->prices->first()->is_show_price == 1 && $serviceFlow->process->prices->first()->total_catalogs_item_discount != $serviceFlow->process->prices->first()->price)
        <span class="price old-price">{{ num_format($serviceFlow->process->prices->first()->price, 0) }} ₽</span>
        <span class="discount-description">{{ $serviceFlow->process->prices->first()->discount_price->description }}</span>
    @endif
    <span class="price">{{ num_format($serviceFlow->process->prices->first()->total_catalogs_item_discount, 0) }}  ₽ / чел.</span>
@endif

<label>Выберите дату тура:
    <select class="select-service-flow">
        @foreach($serviceFlow->process->actualFlows as $flow)
            <option
                value="{{ $flow->id }}"
                @if($serviceFlow->id == $flow->id)
                selected
                @endif
            >{{ $flow->start_at->translatedFormat('j F') }} - {{ $flow->finish_at->translatedFormat('j F') }}</option>
        @endforeach
    </select>
</label>

<div class="wrap-button-center">
    <a href="#" class="button fill-blue" data-open="modal-call" id="button-order">Бронировать</a>
</div>

@push('scripts')
    <script>
        $(document).on('change', '.select-service-flow', function () {
            $('#button-order').prop('disabled', true);
            let url = '{{ route('project.tours.show', $serviceFlow->process->process->slug) }}' + '?flow_id=' + $('.select-service-flow').val();
            window.location.replace(url + '#select-tour');
        })
    </script>
@endpush
