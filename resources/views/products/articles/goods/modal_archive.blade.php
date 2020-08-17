<div class="reveal rev-small" id="modal-goods-{{ $item->id }}-archive" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Архивация "{{ $item->article->name }}"</h5>
    </div>
  </div>
  <div class="grid-x align-center modal-content ">

      @if($item->in_kits->isNotEmpty())
          <div class="small-10 cell">
              <h6>Товар находится в наборах:</h6>
              <ol>
                  @foreach($item->in_kits as $kit)
                      <li>
                          <a href="{{ route('goods.edit', $kit->cur_goods->id) }}" target="_blank">{{ $kit->name }}</a>
                      </li>
                  @endforeach
              </ol>
          </div>
      @endif

      @if($item->related->isNotEmpty())
          <div class="small-10 cell">
              <h6>Товар является сопутствующим для товаров:</h6>
              <ol>
                  @foreach($item->related as $related)
                      <li>
                          <a href="{{ route('goods.edit', $related->id) }}" target="_blank">{{ $related->article->name }}</a>
                      </li>
                  @endforeach
              </ol>
          </div>
      @endif

      @if($item->prices->isNotEmpty())
          <div class="small-10 cell">
            <h6>Товар находится в прайсах:</h6>
              <ol>
                  @foreach($item->prices as $price)
                      <li>{{ $price->catalog->name }} - {{ $price->catalogs_item->name }}</li>
                      @if($price->promotions->isNotEmpty())
                          <ol>
                          @foreach($price->promotions as $promotion)
                              <li>{{ $promotion->name }}</li>
                              @endforeach
                          </ol>
                          @endif
                  @endforeach
              </ol>
          </div>
      @endif

  </div>
    @can('delete', $item)
        <div class="grid-x align-center grid-padding-x">
            <div class="cell small-10">
                При архивации все связи будут удалены!
            </div>
        </div>
      <div class="grid-x align-center grid-padding-x">
        <div class="small-6 medium-4 cell">
{{--          {!! Form::open(['route' => ['goods.archive', $item->id]]) !!}--}}
{{--            {!! Form::submit('В архив', ['class' => 'button modal-button']) !!}--}}
{{--            <button class="button modal-button" type="submit">В архив</button>--}}
{{--        {!! Form::close() !!}--}}
            <a href="{{ route('goods.archive', $item->id) }}" class="button modal-button">В архив</a>
        </div>
<div class="small-6 medium-4 cell">
<button data-close class="button modal-button" id="save-button" type="submit">Отменить</button>
</div>
</div>

{{--        @else--}}
{{--        <div class="grid-x align-center grid-padding-x">--}}
{{--            <div class="cell small-10">--}}
{{--                Товар находится в составе набора, архивация невозможна!--}}
{{--            </div>--}}
{{--        </div>--}}
@endcan
<div data-close class="icon-close-modal sprite close-modal remove-modal"></div>
</div>
