<header class="cell small-12">
	<div class="grid-x">
		<div class="cell small-6 block-logo">
			<img src="img/mwtour/logo.svg">
		</div>

		<div class="cell small-6 block-nav align-right">
			<div class="grid-x">
				<div class="cell auto">
					@include('project.composers.navigations.navigation_by_align', ['align' => 'top'])
				</div>
				<div class="cell shrink wrap-phone">
					<a href="tel:89041248598">8 (904) 124-85-98</a>
				</div>
			</div>

		    {{-- Навигация --}}

		    @include('project.composers.navigations.navigation_by_align', ['align' => 'right'])
		    <a href="#" data-open="open-modal-login">Войти</a>
		    @include('mwtour.layouts.headers.includes.modal_login')

		</div>
	</div>
</header>
