@extends('layouts.app')

@section('title', 'Редактировать лид')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, isset($lead->case_number) ? $lead->case_number : 'нет номера'))

@section('title-content')
    {{-- <div class="top-bar head-content">
            <div class="top-bar-left">
                <h2 class="header-content">ЛИД №: <input id="show-case-number" name="show_case_number" readonly class="case_number_field" value="{{ $lead->case_number }}"> </h2>
        </div>
        <div class="top-bar-right wrap_lead_badget">
                @include('includes.inputs.digit', ['name' => 'badget', 'value' => $lead->badget, 'decimal_place'=>2])
        </div>
    </div> --}}
@endsection

@section('content')



    @php
        if ($lead->manager_id == 1){
            $disabled_leadbot = 'disabled';
        } else {
            $disabled_leadbot = '';
        }
    @endphp

    @include('leads.form', ['submitButtonText' => 'Сохранить'])

@endsection

@section('modals')
    <section id="modal"></section>
    {{-- Модалка удаления с ajax --}}
    @include('includes.modals.modal-delete-ajax')
    @include('includes.modals.modal-add-claim', ['lead' => $lead])
@endsection

@push('scripts')
    {{--    <script>--}}
    {{--        // Инициализируем Vue и Vuex с модулем сметы--}}
    {{--        console.log(new Vuex.Store(window.store.modules.estimate));--}}
    {{--        var estimate = new Vuex.Store(window.store.modules.estimate);--}}
    {{--        const app = new Vue({--}}
    {{--            el: '#app',--}}
    {{--            store: new Vuex.Store(estimate)--}}
    {{--        });--}}
    {{--    </script>--}}

    @include('leads.scripts')
    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.pickmeup-script')
    @include('includes.scripts.upload-file')

    <script>
        var lead_id = '{{ $lead->id }}';
        var lead_type_id = '{{ $lead->lead_type_id }}';
        var manager_id = '{{ $lead->manager_id }}';

        // $(document).on('dblclick', '#phone', function () {
        //
        //     // Снятие блокировки с поля номер телефона
        //     $('#phone').attr('readonly', false);
        // });

        $(document).on('click', '#lead-free', function (event) {
            event.preventDefault();

            $.get("/admin/lead_appointed_check", {manager_id: manager_id}, function (data) {

                if (data == 1) {

                    $.post("/admin/lead_appointed", {id: lead_id}, function (html) {
                        $('#modal').html(html);
                        $('#add-appointed').foundation();
                        $('#add-appointed').foundation('open');
                    });
                } else {

                    $.post("/admin/lead_free", {id: lead_id}, function (data) {
                        if (data === true) {

                            var url = '{{ url("admin/leads") }}';
                            window.location.replace(url);
                            // $(location).attr('href', );
                        } else {
                            // Выводим ошибку на страницу
                            alert(data);
                        }
                        ;
                    });
                }
            });
        });

        $(document).on('click', '#submit-appointed', function (event) {
            event.preventDefault();

            $(this).prop('disabled', true);

            $.post("/admin/lead_distribute", $(this).closest('form').serialize(), function (date) {
                let url = '{{ url("admin/leads") }}';
                window.location.replace(url);
            });
        });

        $(document).on('click', '.take-lead', function (event) {
            event.preventDefault();

            var entity_alias = $(this).closest('.item').attr('id').split('-')[0];
            var id = $(this).closest('.item').attr('id').split('-')[1];
            var item = $(this);


            /* Act on the event */
        });

        $(document).on('click', '#submit-add-claim', function (event) {
            event.preventDefault();

            $.post("/admin/claim_add", $('#form-claim-add').serialize(), function (html) {
                $('#add-claim').foundation('close')
                $('#claims-list').html(html);
                $('#form-claim-add textarea[name=body]').val('');

            });
        });

        $(document).on('click', '.finish-claim', function (event) {
            event.preventDefault();

            // Находим описание сущности, id и название удаляемого элемента в родителе
            var parent = $(this).closest('.item');
            var id = parent.attr('id').split('-')[1];
            var name = parent.data('name');

            $.post("/admin/claim_finish", {
                id: id
            }, function (data) {
                var result = $.parseJSON(data);

                if (result['error_status'] == 0) {
                    $('#claims-' + id + ' .action a').remove();
                } else {
                    alert(result['error_message']);
                }
                ;
            });
        });

        $(document).on('click', '#change-lead-type', function (event) {
            event.preventDefault();

            $.post("/admin/open_change_lead_type", {
                lead_type_id: lead_type_id,
                lead_id: lead_id
            }, function (html) {
                $('#modal').html(html);
                $('#modal-change-lead-type').foundation();
                $('#modal-change-lead-type').foundation('open');
            });
        });

        $(document).on('click', '#submit-change-lead-type', function (event) {
            event.preventDefault();

            $.post("/admin/change_lead_type", $(this).closest('form').serialize(), function (data) {
                $('#modal-change-lead-type').foundation('close');
                $('#lead-type-name').html(data['lead_type_name']);
                $('#show-case-number').val(data['case_number']);
            });
        });


    </script>

    @include('includes.scripts.notes', ['id' => $lead->id, 'entity' => "leads"])
    @include('includes.scripts.challenges', ['id' => $lead->id, 'model' => "App\Lead"])
    @include('includes.contragents.contragents', ['id' => $lead->id])

    @include('leads.pricing.pricing-script', ['id' => $lead->id, 'model' => "App\Lead"])
@endpush



