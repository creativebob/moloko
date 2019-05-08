@if ($workflows_categories->isNotEmpty())
@foreach($workflows_categories as $workflows_category)
@if ($workflows_category->workflows->isNotEmpty())
<li>
	<span class="parent" data-open="workflow_category-{{ $workflows_category->id }}">{{ $workflows_category->name }}</span>
	<div class="checker-nested" id="workflow_category-{{ $workflows_category->id }}">
		<ul class="checker">

			@foreach($workflows_category->workflows as $workflow)
			<li class="checkbox">
				{{ Form::checkbox(null, $workflow->id, in_array($workflow->id, $process->workflows->pluck('id')->toArray()), ['class' => 'add-workflow', 'id' => 'workflow-'.$workflow->id]) }}
				<label for="workflow-{{ $workflow->id }}">
					<span>{{ $workflow->process->name }}</span>
				</label>
			</li>
			@endforeach

		</ul>
	</div>
</li>
@endif
@endforeach
@else
<li>Ничего нет...</li>
@endif



