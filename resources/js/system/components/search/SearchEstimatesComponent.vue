<template>

        <div id="search" class="search-estimates-component">
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
                            <a :href="'/admin/leads/' + item.number + '/edit'"><span>{{ item.number }}</span> от {{ getFormatDate(item.date) }}</a><br>
                            <span class="text-small">Продано</span>
                        </td>
                        <td  class="search-result-summa">
                            <span>{{ item.total | decimalPlaces | decimalLevel }} руб.</span>
                        </td>
                        <td class="search-result-info">
                            <span>{{ item.lead.name }}</span><br>
                            <span class="text-small">{{ item.lead.company_name }}</span>
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
                let delay = 150;
                return _.debounce(this.check, delay);
            }
        },
        methods: {
            check () {

                    // Если пользователь ввел более одного знака - начинам поиск
                    if (this.text.length >= 1) {

                        // Общий запрос поиска
                        var search_query = '/admin/estimates/search/';

                        // ПЕРЕОПРЕДЕЛЕНЕ запроса в определенных случаях (Для ускорения)

                        // СЛУЧАЙ 1: Пользователь ввел только 4 символа и все они числа
                        // Будем искать по crop телефону и в номерах сметы с 4 знаками.

                        // if((this.text.match(/^\d+$/))&&(this.text.length == 4)){
                        //     search_query = '/admin/estimates/search_crop_phone/';
                        // }

                        // Делаем запрос:
                        axios
                        .get(search_query + this.text)
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
            },
        },
    }
</script>
