<div class="reveal" id="first-add" data-reveal data-close-on-click="false">
	<div class="grid-x">
		<div class="small-12 cell modal-title">
			<h5>ДОБАВЛЕНИЕ каталога</h5>
		</div>
	</div>
	{{ Form::open(['id'=>'form-first-add', 'data-abide', 'novalidate']) }}

	@include('catalogs.modals.first', ['submitButtonText' => 'Добавить каталог', 'class' => 'submit-add', 'disabled' => ''])

	{{ Form::close() }}
	<div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>

@include('includes.scripts.inputs-mask')
@include('includes.scripts.upload-file')