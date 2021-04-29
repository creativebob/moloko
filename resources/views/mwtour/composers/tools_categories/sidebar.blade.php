@isset($tools_categories)
    <aside class="cell small-12 medium-5 large-3 sidebar sidebar-tools_categories" data-sticky-container>
        <div class="sticky" data-sticky data-sticky-on="medium" data-top-anchor="278" data-btm-anchor="wrap-sidebar:bottom" data-margin-top="2">
            @foreach($tools_categories as $tool_category)
                @if($tool_category->tools->isNotEmpty())
                <h3>{{ $tool_category->name }}</h3>
                <ul class="menu tools-category vertical">
                    @foreach($tool_category->tools as $item)
                        <li
                            @isset($tool)
                                @if($item->id == $tool->id)
                                class="is-active"
                                @endif
                            @endisset
                        >
                            <a href="{{ route('project.equipments.show', $item->article->slug) }}">{{ $item->article->name }}</a>
                        </li>
                    @endforeach
                </ul>
                @endif
            @endforeach
        </div>
    </aside>
@endisset
