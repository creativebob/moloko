<template>
    <div class="small-12 cell wrap-lead-company">
        <label class="label-icon">Компания
            <input
                type="text"
                name="company_name"
                v-model="name"
                :disabled="disabled"
                @input="input($event.target.value)"
                maxlength="50"
                autocomplete="off"
                pattern="[А-Яа-яЁё0-9-_\s]{3,50}"
                @keydown.enter.prevent="onEnter"
            >

            <div
                class="sprite-input-right"
                :class="status"
                @click="clear"
            >
            </div>
            <!--            <span class="form-error">Уж постарайтесь, введите город!</span>-->
        </label>

        <table class="content-table-search table-over">
            <tbody>

            <template v-if=search>
                <tr v-for="(result, index) in results">
                    <td>
                        <a @click="add(index)">{{ result.name }}</a>
                    </td>
                </tr>
            </template>

            </tbody>
        </table>
    </div>

</template>

<script>
    export default {
        props: {
            organization: Object,
            legalForms: Array,
            disabled: {
                type: Boolean,
                default: false
            },
        },
        data() {
            return {
                curOrganization: this.organization,
                name: this.organization.name,

                results: [],
                search: false,
                found: !!this.organization.id,
                remove: false,
            };
        },
        computed: {
            status() {
                let result;

                if (this.found) {
                    result = 'sprite-16 icon-success'
                }
                if (this.remove) {
                    result = 'sprite-16 icon-error'
                }
                return result;
            }
        },
        methods: {
            check() {


                // Поиск
                // this.results = this.companies.filter(company => {
                //     const companyNameLowerCase = company.name.toLowerCase();
                //     return companyNameLowerCase.includes(nameLowerCase);
                // });

                    let nameLowerCase = this.name.toLowerCase();

                    // Ищем значение правовой формы в названии, введеном пользователем
                    this.legalForms.forEach(legalForm => {
                        const legalFormNameLowerCase = legalForm.name.toLowerCase();
                        if (nameLowerCase.includes(legalFormNameLowerCase)) {
                            nameLowerCase = nameLowerCase.replace(legalFormNameLowerCase + ' ', '');
                        }
                    });

                    axios
                        .get('/admin/leads/search-companies-by-name/' + nameLowerCase)
                        .then(response => {
                            this.results = response.data;
                            this.search = (this.results.length > 0);
                            this.remove = (this.results.length == 0 || this.search);
                        })
                        .catch(error => {
                            console.log(error)
                        });

            },
            add(index) {
                this.curOrganization = this.results[index];
                this.name = this.results[index].name;

                this.results = [];
                this.search = false;
                this.found = true;
                this.remove = false;

                this.change();
            },
            clear() {
                if (this.remove) {
                    // console.log('Клик по иконке ошибки на инпуте, обнуляем');
                    this.curOrganization = {
                        id: null,
                        name: null,
                    };
                    this.name = null;

                    this.results = [];
                    this.found = false;
                    this.remove = false;

                    this.$emit('input', this.name);
                }
            },
            input(value) {
                // console.log('Изменение в инпуте, обнуляем все кроме имени, и если символов больше 2х начинаем поиск');
                this.curOrganization = {
                    id: null,
                    name: value,
                };

                this.results = [];
                this.search = false;
                this.found = false;
                this.remove = false;

                this.$emit('input', value);

                this.change();

                if (this.name.length > 2) {
                    this.check();
                }
            },
            onEnter() {
                if (this.results.length == 1) {
                    this.add(0);
                }
            },
            change() {
                this.$emit('change', this.curOrganization);
            },
            update(organization) {
                if (organization) {
                    this.curOrganization = organization;
                    this.name = organization.name;
                } else {
                    this.curOrganization = {
                        id: null,
                        name: null,
                    };
                    this.name = null;
                }

                this.found = !!organization;
            },
        }
    }
</script>
