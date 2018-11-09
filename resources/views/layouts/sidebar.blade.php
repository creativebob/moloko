<aside class="sidebar {{ $open }}" id="sidebar">
    <nav class="nav" id="sidebar-navigation">

        @if($sidebar_tree)

        <ul class="vertical menu accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false" data-slide-speed="250">
            <li><a href="/admin/dashboard" data-link="0"><div class="icon-mcc sprite"></div><span>ЦУП</span></a></li>

            @foreach ($sidebar_tree as $sidebar)

            @if(($sidebar->parent_id == null) && isset($sidebar->childrens))

            {{-- Если родитель --}}
            <li><a data-link="{{ $sidebar->id }}"><div class="{{ $sidebar->icon }} sprite"></div><span>{{ $sidebar->name }}</span></a>

                <ul class="menu vertical">

                    @foreach($sidebar->childrens as $children)
                    @include('layouts.sidebar_list', ['item' => $children])
                    @endforeach

                </ul>
                
            </li>
            
            @endif
            @endforeach
        </ul>

        @endif 

    </nav>
    <section class="menu vertical gen-menu-bot">
        <div id="sidebar-button">
            <div class="icon-arrow-back sprite" id="cursor"></div>
        </div> 
    </section>
</aside>