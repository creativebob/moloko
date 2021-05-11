<div class="reveal" id="modal-create" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>Добавление потока</h5>
        </div>
    </div>
    {{ Form::open(['route' => $pageInfo->alias.'.store', 'id' => 'form-create', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x align-center modal-content inputs">
        <div class="small-10 cell">

            <div class="grid-x grid-margin-x">

                <div class="cell small-12">
                    <select-processes-categories-component
                        :select-categories='@json($categoriesTree)'
                        :select-categories-items='@json($items)'
                    ></select-processes-categories-component>
                </div>

                <div class="cell small-12">
                    @if(session("access.all_rights.index-{$pageInfo->alias}-allow.filials_for_user")->count() > 1)
                        <label>Филиал:
                            {!! Form::select('filial_id', session("access.all_rights.index-{$pageInfo->alias}-allow.filials_for_user")->pluck('name', 'id'), $flow->filial_id) !!}
                        </label>
                    @else
                        <input
                            type="hidden"
                            name="filial_id"
                            value="{{ session("access.all_rights.index-{$pageInfo->alias}-allow.filials_for_user")->first()->id }}"
                        >
                    @endif
                </div>

            </div>
        </div>
    </div>
    <div class="grid-x align-center">
        <div class="small-6 medium-4 cell">
            {{ Form::submit('Добавить', ['class' => 'button modal-button']) }}
        </div>
    </div>
    {{ Form::close() }}
    <div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>
