<div class="reveal" id="modal-create" data-reveal data-close-on-click="false">

    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>Добавление пункта каталога</h5>
        </div>
    </div>

    {{ Form::open(['id' => 'form-create', 'data-abide', 'novalidate']) }}

    @include('catalogs_items.form', ['submit_text' => 'Добавить', 'class' => 'submit-create'])

    {{ Form::close() }}

    <div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>
