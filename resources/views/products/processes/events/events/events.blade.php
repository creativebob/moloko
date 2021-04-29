<process-compositions-component
    :categories='@json($eventsCategories)'
    :items='@json($events->setAppends([
        'length',
    ]))'
    :item-items='@json($process->events->setAppends([
        'length',
    ]))'
    name="events"
    @if($process->draft == 0)
    :disabled="true"
    @endif
></process-compositions-component>
