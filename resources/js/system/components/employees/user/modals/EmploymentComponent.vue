<template>
    <div class="reveal" id="modal-employment" data-reveal data-close-on-click="false">
        <div class="grid-x">
            <div class="small-12 cell modal-title">
                <h5>Трудоустройство сотрудника</h5>
            </div>
        </div>
        <form action="/admin/employee_employment" method="post"  id="form-employment" data-abide novalidate>
            <slot></slot>
        <div class="grid-x grid-padding-x align-center modal-content inputs">
            <div class="small-12 cell">

                <div class="grid-x grid-margin-x">
                    <div class="small-12 cell">
                        <h2>{{ user.name }}</h2><br>
                    </div>

                    <div class="small-12 medium-8 cell">
                        <label>Вакантная должность:
                            <select
                                name="staffer_id"
                            >
                                <option
                                    :value="null"
                                >Выберите должность</option>
                                <option
                                    v-for="vacancy in vacancies"
                                    :value="vacancy.id"
                                    >{{ vacancy.position.name }}</option>
                            </select>
                        </label>
                    </div>

                    <div class="small-12 medium-6 cell">
                        <label>Дата приема
                            <pickmeup-component
                                name="employment_date"
                                :required="true"
                            ></pickmeup-component>
                        </label>
                    </div>
                </div>
                <input
                    type="hidden"
                    name="user_id"
                    :value="user.id"
                >
            </div>
        </div>
        <div class="grid-x align-center">
            <div class="small-6 medium-4 cell">
                <button class="button modal-button">Устроить</button>
            </div>
        </div>
        </form>
        <div data-close class="icon-close-modal sprite close-modal employment-item"></div>
    </div>
</template>

<script>
export default {
    props: {
        user: Object,
        vacancies: Array,
    },
}
</script>
