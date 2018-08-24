<div class="grid-x align-center align-middle header">
	<div class="small-11 medium-3 cell align-center text-center medium-text-left company-logo">
		<a href="/" class="logo-link"><img src="/img/logo-small.png" class="logo"></a>
		<a href="/" class="company-name text-right align-middle">
			<h1>{{ $company['name'] }}</h1>
			<span>{{ $activity }}</span>
		</a>
	</div>
	<nav class="small-11 medium-7 cell general-menu">
		<ul class="menu">
			@if(!empty($navigations['main']))
                  <ul class="dropdown vertical menu medium-horizontal large-horizontal" data-dropdown-menu>
                    @foreach ($navigations['main']['menus'] as $menu)
                    @if (empty($menu['alias']))
                    @php
                    $link = '/'.$menu['page']['alias'];
                    @endphp
                    @else
                    @php
                    $link = $menu['alias'];
                    @endphp
                    @endif
                    <li><a href="{{ $link }}" @if ($menu['page']['alias'] == $alias) class="active" @endif>{{ $menu['name'] }}</a></li>
                    @endforeach
                  </ul>
                  @endif


		</ul>
	</nav>
</div>
