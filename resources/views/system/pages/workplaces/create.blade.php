<div class="reveal rev-small" id="modal-create" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>Создание рабочего места</h5>
        </div>
    </div>
    {{ Form::open(['route' => 'workplaces.store', 'id' => 'form-create', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x align-center modal-content inputs">
        <div class="small-10 cell">

            <div class="grid-x grid-margin-x">

                <div class="small-12 cell">
                    <label>Название
                        @include('includes.inputs.name', ['required' => true, 'data' => 'autofocus-target'])
                    </label>
                </div>

                <div class="small-12 cell">
                    <filials-with-outlets-component
                        entity="workplaces"
                    ></filials-with-outlets-component>
                </div>

            </div>
        </div>
        <div class="small-6 cell">
            {{ Form::submit('Добавить', ['class' => 'button modal-button']) }}
        </div>
        {{ Form::close() }}
        <div data-close class="icon-close-modal sprite close-modal add-item"></div>
    </div>
</div>
