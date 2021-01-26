<template>
    <div class="grid-x grid-padding-x">
        <div
            v-if="employmentHistory.length"
            class="cell small-12"
        >
            <table class="content-table list-employees">
                <thead>
                <tr>
                    <th>Должность</th>
                    <th>Ставка</th>
                    <th>Отдел</th>
                    <th>Дата принятия</th>
                    <th>Дата увольнения</th>
                    <!--                    <th>Причина увольнения</th>-->
                    <th>Операции</th>
                </tr>
                </thead>
                <tbody id="table-raws">
                <employee-component
                    v-for="employee in employmentHistory"
                    :employee="employee"
                    :key="employee.id"
                    @open-modal="openModal"
                ></employee-component>
                </tbody>
            </table>
        </div>

        <div
            v-if="showButtonEmployment"
            class="cell small-12"
        >
            <a class="button green-button" id="employee-employment" data-open="modal-employment">Трудоустроить</a>
        </div>

        <div
            v-if="hasNotEmployee"
            class="cell small-12"
        >
            <div class="grid-x grid-padding-x inputs">
                <div class="cell small-12 medium-8">
                    <label>Вакансия:
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
                <div class="small-12 medium-4 cell">
                    <label>Дата приема
                        <pickmeup-component
                            name="employment_date"
                            :required="true"
                        ></pickmeup-component>
                    </label>
                </div>
                <input
                    type="hidden"
                    name="site_id"
                    :value="1"
                >
                <input
                    type="hidden"
                    name="user_type"
                    :value="1"
                >
            </div>
        </div>

        <modal-dismiss-component
            v-if="hasEmployee"
            :employee="actualDismissal"
        >
            <slot></slot>
        </modal-dismiss-component>

        <modal-employment-component
            v-if="showButtonEmployment"
            :user="user"
            :vacancies="vacancies"
        >
            <slot></slot>
        </modal-employment-component>

    </div>
</template>

<script>
export default {
    components: {
        'employee-component': require('./EmployeeComponent'),
        'modal-dismiss-component': require('./modals/DismissComponent'),
        'modal-employment-component': require('./modals/EmploymentComponent'),
    },
    props: {
        employee: Object,
        user: Object,
        employmentHistory: Array,
        vacancies: Array,
    },
    data() {
        return {
            dismissal: {
                id: null,
                user: {
                    name: null,
                    access_block: null
                },
                staffer: {
                    position: {
                        name: null,
                    }
                }

            }
        }
    },
    computed: {
        hasEmployee() {
            const found = this.employmentHistory.find(employee => employee.dismissal_date == null);
            return !!found;
        },
        hasNotEmployee() {
            return !this.employee.id;
        },
        showButtonEmployment() {
            let rate = 0;
            this.employmentHistory.forEach(employee => {
                if (employee.dismissal_date == null) {
                    rate += parseFloat(employee.staffer.rate);
                }
            })

            return rate < 1 && this.vacancies.length && this.employee.id;
        },
        actualDismissal() {
            return this.dismissal;
        }
    },
    methods: {
        openModal(item) {
            this.dismissal = item;
        }
    }

}
</script>
