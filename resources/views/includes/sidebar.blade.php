

<aside class="sidebar expand" id="sidebar" style="width: 240px;">
  <nav class="nav" id="sidebar-navigation">


    @if($sidebar_tree)
      <ul class="vertical menu accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false" data-slide-speed="250">
        @foreach ($sidebar_tree as $sidebar)
          @if($sidebar['menu_parent_id'] == null)
          {{-- Если родитель --}}
            <li><a data-link="{{ $sidebar['id'] }}"><div class="{{ $sidebar['menu_icon'] }} sprite"></div><span>{{ $sidebar['menu_name'] }}</span></a>
              <ul class="menu vertical">
                @if (isset($sidebar['children']))
                  @foreach($sidebar['children'] as $sidebar)
                    @include('includes.sidebar-list', $sidebar)
                  @endforeach
                @endif
              @endif
            </ul>
          </li>
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