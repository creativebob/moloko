<process-compositions-component
    :categories='@json($servicesCategories)'
    :items='@json($services->setAppends([
        'length',
    ]))'
    :item-items='@json($process->services->setAppends([
        'length',
    ]))'
    name="services"
    @if($process->draft == 0)
    :disabled="true"
    @endif
></process-compositions-component>
