<div class="reveal" id="first-add" data-reveal data-close-on-click="false">
	<div class="grid-x">
		<div class="small-12 cell modal-title">
			<h5>ДОБАВЛЕНИЕ филиала</h5>
		</div>
	</div>
	
	
			{{ Form::open(['url' => 'admin/departments', 'id'=>'form-first-add', 'data-abide', 'novalidate']) }}

			@include('departments.modals.first', ['submitButtonText' => 'Добавить филиал', 'class' => 'submit-add'])

			{{ Form::close() }}
		
	<div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>

@include('includes.scripts.inputs-mask')



