<div class="reveal" id="modal-edit" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>Редактирование пункта меню</h5>
        </div>
    </div>

    {{ Form::model($menu, ['data-abide', 'novalidate']) }}

    @include('menus.form', ['submit_text' => 'Редактировать', 'class' => 'submit-edit'])

    {{ Form::close() }}

    <div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>