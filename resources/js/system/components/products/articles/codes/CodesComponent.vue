<template>
    <div class="grid-x grid-padding-x">
        <store-component
            :article-id="articleId"
            @add="add"
        ></store-component>

        <div class="cell small-12">
            <table
                v-if="codes.length"
                class="table-compositions"
            >
                <thead>
                <tr>
                    <th>Название</th>
                    <th></th>
                </tr>
                </thead>

                <tbody id="table-article_codes">
                <code-component
                    v-for="code in codes"
                    :code="code"
                    :key="code.id"
                    @remove="remove"
                ></code-component>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    components: {
        'store-component': require('./StoreComponent'),
        'code-component': require('./CodeComponent'),
    },
    props: {
        articleId: Number,
        articleCodes: Array
    },
    data() {
        return {
            codes: this.articleCodes
        }
    },
    methods: {
        add(code) {
            this.codes.push(code);
        },
        remove(id) {
            const index = this.codes.findIndex(obj => obj.id === id);
            this.codes.splice(index, 1);
        }
    }

}
</script>
