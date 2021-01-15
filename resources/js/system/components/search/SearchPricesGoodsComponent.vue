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
                            <a :href="'/admin/catalogs_goods/' + item.catalogs_goods_id + '/prices_goods/' + item.id + '/edit'"><span>{{ item.goods.article.name }}</span></a><br>
                            <span class="text-small">{{ item.goods.article.manufacturer.company.name }}</span>
                        </td>
                        <td  class="search-result-summa">
                            <span>{{ item.total }} руб.</span>
                        </td>
                        <td class="search-result-info">
                            <span v-if="item.goods.article.draft" class="draft">Черновик</span>
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

    export default {

        props: {
          catalogId: Number,
        },
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
            }
        },
        methods: {
            check () {
                // console.log('Поиск...');
                if (this.text.length >= 1) {
                    axios
                        .get('/admin/catalogs_goods/' + this.catalogId + '/prices_goods/search/' + this.text)
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
        }
    }
</script>
