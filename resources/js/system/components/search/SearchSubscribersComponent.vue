<template>
    <div id="search">
        <input
            class="search-field"
            type="search"
            id="field-search"
            name="search"
            placeholder="Поиск"
            v-model="text"
            @input="dedounceSearch"
        />
        <div id="search-result-wrap" v-if=search>

            <table class="search-result-list">
                <tr v-for="(item, index) in results">
                    <td class="search-result-info">
                        <a :href="'/admin/subscribers/' + item.id + '/edit'">{{ item.email }}</a>
                    </td>
                    <td class="search-result-id">
                        {{ item.id }}
                    </td>
                </tr>
            </table>

        </div>
    </div>
</template>

<script>
    import _ from 'lodash'
    import moment from 'moment'

    export default {
        data() {
            return {
                text: '',
                results: [],
                search: false,
                found: false,
                error: false,
            };
        },
        computed: {
            status() {
                let result;

                if (this.found) {
                    result = 'sprite-16 icon-success'
                }
                if (this.error) {
                    result = 'sprite-16 icon-error'
                }
                return result;
            },
            dedounceSearch: function () {
                let delay = 300;
                return _.debounce(this.check, delay);
            }
        },
        methods: {
            check() {
                // console.log('Ищем введеные данные в наших городах (подгруженных), затем от результата меняем состояние на поиск или ошибку');
                if (this.text.length >= 1) {
                    axios
                        .get('/admin/subscribers/search/' + this.text)
                        .then(response => {
                            this.results = response.data;
                            this.search = (this.results.length > 0);
                            this.error = (this.results.length == 0);
                        })
                        .catch(error => {
                            console.log(error)
                        });

                } else {
                    this.reset();
                }

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
            },
            getFormatDate(value) {
                return moment(String(value)).format('DD.MM.YYYY');
            }
        },
        filters: {
            decimalPlaces(value) {
                return parseFloat(value).toFixed(2);
            },
            decimalLevel: function (value) {
                return parseFloat(value).toLocaleString();
            },
        },
    }
</script>
