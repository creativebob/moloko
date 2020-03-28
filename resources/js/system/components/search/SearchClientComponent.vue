<template>
    <div>
        <label id="" class="label-icon">Клиент
            <input
                type="text"
                v-model="text"
                @input="dedounceSearch"
                maxlength="30"
                autocomplete="off"
                pattern="[А-Яа-яЁё0-9-_\s]{3,30}"
                :required="required"
                @keydown.enter.prevent="onEnter"
            >

            <div
                    class="sprite-input-right"
                    :class="status"
                    @click="clear"
            >
            </div>
            <span class="form-error">Уж постарайтесь, найдите клиента!</span>

        </label>

        <input
                type="hidden"
                :name="name"
                v-model="id"
                maxlength="3"
                pattern="[0-9]{3}"
        >

        <table class="content-table-search table-over">
            <tbody>

                <template v-if=search>
                    <tr v-for="(client, index) in results">
                        <td v-if="client.clientable.sex == 1 || client.clientable.sex == 0">
                            <a @click="add(index)">{{ client.clientable.first_name }}</a>
                        </td>
                        <td v-else>
                            <a @click="add(index)">{{ client.clientable.name }}</a>
                        </td>
                    </tr>
                </template>

                <tr v-if=error class="no-found">
                    <td>Клиент не найден в базе данных!</td>
                </tr>

            </tbody>
        </table>
     </div>

</template>

<script>
    import _ from 'lodash'

    export default {
        props: {
            client: {
                type: Object,
                default: function(){
                    return {
                        id: null,
                        clientable: {
                            name: null
                        }
                    }
                }
            },
            required: {
                type: Boolean,
                default: false
            },
            name: {
                type: String,
                default: 'client_id'
            }
        },
        data() {
            return {
                id: this.client.id,
                text: (this.client.clientable.sex == 1 || this.client.clientable.sex == 0) ? this.client.clientable.first_name : this.client.clientable.name,
                results: [],
                search: false,
                found: (this.client.id != null) ? true : false,
                error: false,
                cities: this.startCities
            };
        },
        computed: {
            dedounceSearch: function() {
                let delay = 300;
                return _.debounce(this.check, delay);
            },
            status() {
                let result;

                if (this.found) {
                    result = 'sprite-16 icon-success'
                }
                if (this.error) {
                    result = 'sprite-16 icon-error'
                }
                return result;
            }
        },
        methods: {
            check() {
                // console.log('Ищем введеные данные в наших городах (подгруженных), затем от результата меняем состояние на поиск или ошибку');
                if (this.text.length >= 2) {
                    axios
                        .get('/api/v1/search/clients/' + this.text)
                        .then(response => {
                            this.results = response.data;
                            this.search = (this.results.length > 0)
                            this.error = (this.results.length == 0)
                        })
                        .catch(error => {
                            console.log(error)
                        });
                } else {
                    this.reset();
                }
            },
            add(index) {
                // console.log('Клик по пришедшим данным, добавляем в инпут');
                this.id = this.results[index].id;
                this.text = (this.results[index].clientable.sex == 1 || this.results[index].clientable.sex == 0) ? this.results[index].clientable.first_name : this.results[index].clientable.name;
                this.found = true;
                this.error = false;
                this.search = false;
                this.results = [];
            },
            clear() {
                if (this.error) {
                    // console.log('Клик по иконке ошибки на инпуте, обнуляем');
                    this.text = '';
                    this.id = null;
                    this.found = false;
                    this.error = false;
                    this.results = [];
                }
            },
            reset() {
                // console.log('Изменение в инпуте, обнуляем все кроме имени, и если символов больше 2х начинаем поиск');
                this.id = null;
                this.found = false;
                this.error = false;
                this.search = false;
                this.results = [];

                if (this.text.length > 2) {
                    this.check();
                }
            },
            onEnter() {
                if (this.results.length == 1) {
                    this.add(0);
                }
            }
        }
    }
</script>
