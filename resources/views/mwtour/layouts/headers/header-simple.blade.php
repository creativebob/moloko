<header class="cell small-12 simple">

	<div class="grid-x">
		<div class="cell smll-12 medium-12 large-6 block-logo">
			<a href="/" title="На главную!">
				<img src="/img/mwtour/logo.svg">
			</a>
		</div>
		<div class="cell small-12 medium-12 large-6 block-nav align-right">
			<div class="grid-x">
				<div class="cell small-12 medium-12 large-auto wrap-social">
					@include('project.composers.navigations.navigation_by_align', ['align' => 'top'])
				</div>
				<div class="cell small-12 medium-12 large-shrink wrap-phone">
					<a href="tel:89041248598">8 (904) 124-85-98</a>
				</div>
			</div>

		    {{-- Навигация --}}

			<div class="grid-x">
				<div class="cell auto">
				    @include('project.composers.navigations.navigation_by_align', ['align' => 'right'])
				</div>
				<div class="cell shrink wrap-profile-link">
					<ul class="navigation">
						<li>
							@auth
								<a href="/profile" class="profile-link">Личный кабинет</a>
							@else
				    			<a href="#" class="profile-link" data-open="open-modal-login">Войти</a>
				    		@endauth
				    	</li>
				    </ul>
				    @include('mwtour.layouts.headers.includes.modal_login')
				</div>
			</div>
		</div>
	</div>

</header>
