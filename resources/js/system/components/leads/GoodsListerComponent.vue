<template>
    <div class="grid-x">
        <div class="cell small-12 lister">
            <span
                class="title"
                @click="focusInput"
            >Товары</span>
            <ul class="menu">
                <li v-for="(item, index) in currentItems">
                    <input
                        type="hidden"
                        name="goods[]"
                        :value="item.id"
                    >
                    <span>{{ item.article.name }}<span v-if="item.archive == 1"> (архивный)</span></span>
                    <span
                        class="remove"
                        @click="removeItem(index)"
                    >x</span>
                </li>
            </ul>
            <div
                v-if="currentItems.length"
                class="reset"
                @click="reset"
            >Очистить</div>
        </div>
        <div class="cell small-12">
            <div class="input-icon">
                <input
                    type="text"
                    ref="enter"
                    v-model="text"
                    @input="resetInput"
                    @keydown.enter.prevent="onEnter"
                >
                <div
                    class="sprite-input-right"
                    :class="status"
                    @click="clearInput"
                >
                </div>
            </div>

            <table class="content-table-search table-over">
                <tbody>

                <template v-if=search>
                    <tr v-for="item in results">
                        <td>
                            <a @click="addItem(item)">{{ item.article.name }}<span v-if="item.archive == 1"> (архивный)</span></a>
                        </td>
                    </tr>
                </template>

                <tr v-if=error class="no-city">
                    <td>Ничего не найдено...</td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            goods: {
                type: Array,
                default: []
            },
            items: {
                type: Array,
                default: []
            },
        },
        mounted() {
            if (this.items && this.goods) {
                var $vm = this;
                this.items.forEach(id => {
                    let found = $vm.goods.find(curGoods => curGoods.id == id);
                    if (found) {
                        $vm.currentItems.push(found);
                    }
                });
            }
        },
        data() {
            return {
                text: '',
                currentItems: [],
                results: [],
                search: false,
                error: false,
            }
        },
        computed: {
            status() {
                let result;
                if (this.text) {
                    result = 'sprite-16 icon-error'
                }
                return result;
            },
        },
        methods: {
            focusInput() {
                this.$refs.enter.focus();
            },
            check() {
                // console.log('Ищем введеные данные в наших городах (подгруженных), затем от результата меняем состояние на поиск или ошибку');
                this.results = this.goods.filter(item => {
                    return item.article.name.toLowerCase().includes(this.text.toLowerCase());
                });

                this.search = (this.results.length > 0)
                this.error = (this.results.length == 0)
            },
            addItem(item) {
                if (this.text.length > 1) {
                    if (! this.currentItems) {
                        this.currentItems = [];
                    }
                    let found = this.currentItems.find(obj => obj.id == item.id);
                    if (! found) {
                        this.currentItems.push(item);
                    }
                    this.text = '';
                    this.error = false;
                    this.search = false;
                    this.results = [];
                }
            },
            onEnter() {
                if (this.results.length == 1) {
                    this.addItem(this.results[0]);
                }
            },
            removeItem(index) {
                this.currentItems.splice(index, 1);
            },
            resetInput() {
                this.error = false;
                this.search = false;
                this.results = [];

                if (this.text.length > 1) {
                    this.check();
                }
            },
            clearInput() {
                this.text = '';
                this.search = false;
                this.error = false;
                this.results = [];
            },
            reset() {
                this.currentItems = [];
            },
        }
    }
</script>
