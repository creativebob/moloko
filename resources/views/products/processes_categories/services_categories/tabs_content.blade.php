@can('index', App\Workflow::class)
<div class="tabs-panel" id="tab-workflows">
	@include('products.processes_categories.services_categories.workflows.workflows', ['category' => $category])
</div>
@endcan

