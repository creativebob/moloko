@php if(!isset($align)){$align = 'top';} @endphp
@isset($navigations[$align])
    <ul class="cell menu medium-horizontal {{ $align }}-menu {{ $class ?? '' }}">
        @foreach($navigations[$align]->first()->menus as $menu)
            @if(isset($menu->alias))
                <li>
                    <a
                    href="{{ $menu->alias }}"
                    class="{{ $menu->icon ?? '' }}"
                    title="{{ $menu->title ?? '' }}"
                    @if($menu->new_blank) target="_blank" @endif
                    @if($menu->is_nofollow) rel="nofollow" @endif
                    >@if(!$menu->text_hidden) {{ $menu->name }} @endif
                    </a>
                </li>
            @else
                @if($menu->page->alias == $page->alias)
                    <li class="is-active"><span class="isactive-item" >{{ $menu->name }}</span></li>
                @else
                    <li><a href="/{{ $menu->page->alias }}">{{ $menu->name }}</a></li>
                @endif
            @endif
        @endforeach
    </ul>
@endisset
