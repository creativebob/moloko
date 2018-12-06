<div class="reveal" id="modal-create" data-reveal data-close-on-click="false">
	<div class="grid-x">
		<div class="small-12 cell modal-title">
			<h5>{{ $ajax_error['title'] }}</h5>
		</div>
	</div>
	<div class="grid-x grid-padding-x align-center modal-content">
		<div class="small-10 cell">
			<p>{{ $ajax_error['text'] }}</p>
			<br>
			<p class="text-center">
				<a class="button" href="{{ $ajax_error['link'] }}">{{ $ajax_error['title_link'] }}</a>
			</p>
		</div>
	</div>
	<div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>




