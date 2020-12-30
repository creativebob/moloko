<template>
    <div class="small-12 cell search-in-catalog-panel">
        <label class="label-icon">
            <input
                type="text"
                name="search"
                placeholder="Поиск"
                maxlength="25"
                autocomplete="off"
                v-model="text"
                @input="input"
                @keydown.enter.prevent="onEnter"
            >
            <div class="sprite-input-left icon-search"></div>
            <span class="form-error"></span>
        </label>

        <table class="content-table-search table-over">
            <tbody>

            <template v-if=search>
                <tr v-for="result in results">
                    <td>
                        <a @click="add(result)">{{ result.service.process.name }}</a>
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
        prices: Array
    },
    data() {
        return {
            text: '',
            results: [],
            search: false,
        }
    },
    methods: {
        input() {
            this.found = false;
            this.search = false;
            this.results = [];

            if (this.text.length > 2) {
                this.check();
            }
        },
        check() {
            this.results = this.prices.filter(item => {
                return item.service.process.name.toLowerCase().includes(this.text.toLowerCase());
            });
            this.search = (this.results.length > 0)
            this.error = (this.results.length == 0)
        },
        add(price) {
            this.found = true;
            this.search = false;
            this.results = [];

            this.$emit('add', price);
        },
        onEnter() {
            if (this.results.length == 1) {
                this.add(this.results[0]);
            }
        }
    }

}
</script>
