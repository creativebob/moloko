{{-- Фотка с лого --}}
<div class="grid-x align-center align-middle head">
	<div class="small-11 medium-2 cell">
		<p class="head-left">САМЫЕ НИЗКИЕ ЦЕНЫ НА ТКАНИ В ИРКУТСКЕ</p>
	</div>
	<div class="small-11 medium-6 cell text-center align-middle head-center">

		<div class="align-center head-logo">
			<a href="/" class="logo-link"><img src="/img/logo-small.png" class="logo"></a>
			<a href="/">
				<h1>{{ $company['name'] }}</h1>
				<span>{{ $activity }}</span>
			</a>
		</div>
		<ul class="menu align-center head-menu">
			@include('includes.navigation.menu')
		</ul>
		<span class="head-phone">8(914) 926-67-71</span>

	</div>
	<div class="small-11 medium-2 cell">
	</div>
</div>