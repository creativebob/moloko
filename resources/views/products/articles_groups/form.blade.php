<div class="grid-x tabs-wrap inputs">
    <div class="small-12 medium-6 large-6 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            <div class="grid-x grid-padding-x">

                <div class="medium-6 cell">
                    <label>Название группы товара
                        @include('includes.inputs.name', ['required' => true])
                    </label>
                </div>
                <div class="medium-6 cell">
                    <label>Описание
                        @include('includes.inputs.varchar', ['name' => 'description'])
                    </label>
                </div>

                <div class="small-12 medium-6 cell">
                    @include('includes.selects.units_categories', ['default' => isset($articles_group->unit_id) ? $articles_group->unit->category_id : 6, 'type'=>'article'])
                </div>

                <div class="small-12 medium-6 cell">
                    @include('includes.selects.units', ['default' => isset($articles_group->unit_id) ? $articles_group->unit_id : 26, 'units_category_id' => isset($articles_group->unit_id) ? $articles_group->unit->category_id : 6])
                </div>

                <div class="small-12 cell">
                    <fieldset>
                        <legend>Список артикулов в группе:</legend>
                        <ul>
                            @foreach($articles_group->articles as $article)
                            
                                @if(!empty($article->in_goods))
                                    @foreach($article->in_goods as $item)
                                        <li>Товар: <a href="/admin/goods/{{ $item->id }}/edit">{{ $item->article->name }}</a></li>
                                    @endforeach
                                @endif
                                @if(!empty($article->in_raws))
                                    @foreach($article->in_raws as $item)
                                        <li>Сырье: <a href="/admin/raws/{{ $item->id }}/edit">{{ $item->article->name }}</a></li>
                                    @endforeach
                                @endif
                                @if(!empty($article->in_containers))
                                    @foreach($article->in_containers as $item)
                                        <li>Упаковка: <a href="/admin/containers/{{ $item->id }}/edit">{{ $item->article->name }}</a></li>
                                    @endforeach
                                @endif
                            
                            @endforeach
                        </ul>
                    </fieldset>
                </div>

            </div>

        </div>
    </div>

    <div class="small-12 medium-6 large-6 cell tabs-margin-top">
    </div>

    @if ($articles_group->articles->count() == 0)
    <div class="small-12 cell checkbox set-status">
        {{-- <input type="checkbox" name="set_status" id="set-status" value="1"> --}}
        {{ Form::checkbox('set_status', 1, null, ['id' => 'set-status']) }}
        <label for="set-status"><span>Набор</span></label>
    </div>
    @endif

    {{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $articles_group])

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submit_text, ['class' => 'button']) }}
    </div>
</div>

