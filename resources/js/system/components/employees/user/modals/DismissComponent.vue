<template>
    <div class="reveal" id="modal-dismiss" data-reveal data-close-on-click="false">
        <div class="grid-x">
            <div class="small-12 cell modal-title">
                <h5>Уволить сотрудника</h5>
            </div>
        </div>
        <form action="/admin/employee_dismiss" method="post" id="form-dismiss" data-abide novalidate>
            <slot></slot>
            <div class="grid-x grid-padding-x align-center modal-content inputs">
                <div class="small-12 cell">

                    <div class="grid-x grid-margin-x">
                        <div class="small-12 cell">
                            <h2>{{ employee.user.name }} - {{ employee.staffer.position.name }}</h2><br>
                        </div>
                        <div class="small-12 cell">
                            <label>Укажите причину увольнения
                                <textarea-component
                                    name="dismissal_description"
                                    value=""
                                ></textarea-component>
                            </label>
                        </div>
                        <div class="small-12 medium-6 cell">
                            <label>Дата увольнения
                                <pickmeup-component
                                    name="dismissal_date"
                                    :required="true"
                                ></pickmeup-component>
                            </label>
                        </div>
                        <div class="small-12 medium-6 text-center cell checkbox">
                            <input
                                type="checkbox"
                                name="access_block"
                                id="checkbox-access-block"
                                value="1"
                                :checked="employee.user.access_block == 1"
                            >
                            <label for="checkbox-access-block"><span>Блокировать доступ</span></label>
                        </div>
                    </div>
                    <input
                        type="hidden"
                        name="employee_id"
                        :value="employee.id"
                    >
                </div>
            </div>
            <div class="grid-x align-center">
                <div class="small-6 medium-4 cell">
                    <button class="button modal-button">Уволить</button>
                </div>
            </div>
        </form>
        <div data-close class="icon-close-modal sprite close-modal dismiss-item"></div>
    </div>
</template>

<script>
export default {
    components: {
        'textarea-component': require('../../../inputs/TextareaComponent'),
    },
    props: {
        employee: Object
    }
}
</script>

