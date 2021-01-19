<template>
    <tr>
        <td>{{ code.name }}</td>
        <td class="td-delete">
            <a
                class="icon-delete sprite"
                @click="remove"
            ></a>
        </td>
    </tr>
</template>

<script>
export default {
    props: {
        code: Object
    },
    methods: {
        remove() {
            const buttons = $('.button');
            buttons.prop('disabled', true);

            axios
                .delete('/admin/article_codes/' + this.code.id)
                .then(response => {
                    if (response.data) {
                        this.$emit('remove', this.code.id);
                        buttons.prop('disabled', false);
                    }
                })
                .catch(error => {
                    console.log(error)
                });
        }
    }

}
</script>
