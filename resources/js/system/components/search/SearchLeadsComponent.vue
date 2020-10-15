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
                        <td class="search-result-name">
                            <a :href="'/admin/leads/' + item.id + '/edit'"><span>{{ item.id }}</span> от {{ getFormatDate(item.created_at) }}</a><br>
                            <span class="text-small">{{ item.lead_method.name }}</span>
                        </td>
                        <td class="search-result-info">
                            <span>{{ item.name }}</span><br>
                            <span class="text-small">{{ item.company_name }}</span><br v-if="item.company_name">
                            <span class="text-small">{{ item.main_phones[0].phone }}</span><br>
                        </td>
                        <td  class="search-result-summa">
                            <span>{{ item.badget | decimalPlaces | decimalLevel }} руб.</span><br>
                            <span class="text-small">{{ item.stage.name }}</span>
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
            dedounceSearch: function() {
                let delay = 300;
                return _.debounce(this.check, delay);
            },
        },
        methods: {
            check () {
                    // console.log('Ищем введеные данные в наших городах (подгруженных), затем от результата меняем состояние на поиск или ошибку');
                    if (this.text.length >= 1) {
                        axios
                            .get('/admin/leads/search/' + this.text)
                            .then(response => {
                                this.results = response.data
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
            getFormatDate (value) {
                return moment(String(value)).format('DD.MM.YYYY');
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
            }
        },
        filters: {
            decimalPlaces(value) {
                return parseFloat(value).toFixed(2);
            },
            decimalLevel: function (value) {
                return parseFloat(value).toLocaleString();
            }

        }
    }
</script>
