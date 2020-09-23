<template>
    <div class="small-12 cell wrap-lead-company">
        <label class="label-icon">Компания
            <input
                type="text"
                name="company_name"
                v-model="name"
                @input="input"
                maxlength="50"
                autocomplete="off"
                pattern="[А-Яа-яЁё0-9-_\s]{3,50}"
                @focus="focus"
                @blur="blur"
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

        <input
            type="hidden"
            name="organization_id"
            v-model="id"
            maxlength="3"
            pattern="[0-9]{3}"
        >

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
            companies: Array,
        },
        data() {
            return {
                curOrganization: this.organization ? this.organization : null,
                id: this.organization ? this.organization.id : null,
                name: this.organization ? this.organization.name : null,
                results: [],
                search: false,
                found: !!this.organization,
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
                var $vm = this;
                this.results = this.companies.filter(company => {
                    // $vm.legalForms.forEach(legalForm => {
                    //     $vm.name = $vm.name.toLowerCase().replace(legalForm.name.toLowerCase(), "")
                    // });
                    // console.log($vm.name);
                    return company.name.toLowerCase().includes($vm.name.toLowerCase());
                });
                this.search = (this.results.length > 0)
                this.remove = (this.results.length == 0 || this.search)
                // axios
                //     .post('/api/v1/companies/search-by-name', {
                //         name: this.name
                //     })
                //     .then(response => {
                //         this.results = response.data;
                //         this.search = (this.results.length > 0)
                //         this.error = (this.results.length == 0)
                //     })
                //     .catch(error => {
                //         console.log(error)
                //     });
            },
            add(index) {
                // console.log('Клик по пришедшим данным, добавляем в инпут');
                this.curOrganization = this.results[index];
                this.id = this.results[index].id;
                this.name = this.results[index].name;
                this.found = true;
                this.remove = false;
                this.search = false;
                this.results = [];

                this.change();
            },
            updateCityId(cityId) {
                this.id = cityId;

                let city = this.cities.find(city => city.id == cityId);
                this.name = city.name;

                this.found = true;
                this.remove = false;
                this.search = false;
                this.results = [];
            },
            clear() {
                if (this.remove) {
                    // console.log('Клик по иконке ошибки на инпуте, обнуляем');
                    this.name = '';
                    this.id = null;
                    this.found = false;
                    this.remove = false;
                    this.results = [];
                }
            },
            input() {
                // console.log('Изменение в инпуте, обнуляем все кроме имени, и если символов больше 2х начинаем поиск');
                this.curOrganization = null;
                this.id = null;
                this.found = false;
                this.remove = false;
                this.search = false;
                this.results = [];

                this.change();

                if (this.name.length > 1) {
                    this.check();
                }
            },
            focus() {
                this.$emit('focus', this.name);
            },
            blur() {
                this.$emit('blur', this.name);
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
                    this.id = organization.id;
                    this.name = organization.name;
                } else {
                    this.curOrganization = null;
                    this.id = null;
                    this.name = null;
                }

                this.found = !!organization;
            },
        }
    }
</script>
