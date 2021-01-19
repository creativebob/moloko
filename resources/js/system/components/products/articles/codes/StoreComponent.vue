<template>
    <div class="cell small-12">
        <label>Название
            <div class="input-group">
                <input
                    type="text"
                    v-model="name"
                    class="input-group-field"
                    @keydown.enter.prevent="add"
                >
                <div class="input-group-button">
                    <a
                        @click.prevent="add"
                        class="button"
                    >+</a>
                </div>
            </div>
        </label>
    </div>
</template>

<script>
export default {
    props: {
        articleId: Number
    },
    data() {
        return {
            name: null,
        }
    },
    methods: {
        add() {
            if (this.name && this.name.length > 0) {
                const buttons = $('.button');
                buttons.prop('disabled', true);

                axios
                    .post('/admin/article_codes', {
                        name: this.name,
                        article_id: this.articleId
                    })
                    .then(response => {
                        this.$emit('add', response.data);
                        buttons.prop('disabled', false);
                        this.name = null;
                    })
                    .catch(error => {
                        console.log(error)
                    });
            }
        }
    },
}
</script>
