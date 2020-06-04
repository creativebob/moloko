<template>
    <div>
        <input
            type="checkbox"
            name="related[]"
            :id="'checkbox-related-' + curGoods.id"
            :value="curGoods.id"
            :checked="checkChecked"
            @change="change($event.target.checked)"
        >

        <label :for="'checkbox-related-' + curGoods.id">
            <span>{{ curGoods.article.name }}</span>
        </label>
    </div>

</template>

<script>
    export default {
        props: {
            curGoods: Object,
            startChecked: Boolean,
        },
        computed: {
            checkChecked() {
                return this.startChecked;
            }
        },
        methods: {
            change(checked) {
                if (checked) {
                    this.$emit('add', this.curGoods.id);
                    // axios
                    //     .post('/admin/ajax_get_related', {
                    //         id: this.curGoods.id,
                    //     })
                    //     .then(response => {
                    //         this.$emit('add', response.data);
                    //     })
                    //     .catch(error => {
                    //         console.log(error);
                    //     })
                } else {
                    this.$emit('del', this.curGoods.id);
                }
            }
        }
    }
</script>
