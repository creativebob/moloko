<div class="reveal" id="modal-edit" data-reveal data-close-on-click="false">
	<div class="grid-x">
		<div class="small-12 cell modal-title">
			<h5>Редактирование отдела</h5>
		</div>
	</div>

	{{ Form::model($department, ['route' => ['departments.update', $department->id], 'id' => 'form-edit', 'data-abide', 'novalidate']) }}
	@method('PATCH')
	@include('system.pages.hr.departments.department.form', ['submitButtonText' => 'Редактировать', 'class' => 'submit-edit'])

	{{ Form::close() }}

	<div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>



