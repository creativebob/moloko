<template>
    <tr>
        <td>{{ file.name }}</td>
        <td>
            <a
                :href="file.path"
                target="_blank"
            >{{ file.path}}</a>
        </td>
        <td class="td-delete">
            <div
                @click="remove"
                class="icon-delete sprite"
            ></div>
        </td>
    </tr>
</template>

<script>

    export default {
        props: {
            file: Object,
        },
        methods: {
            remove() {
                axios
                    .delete('/admin/files/' + this.file.id, {

                    })
                    .then(response => {
                        console.log(response.data);
                        if (response.data) {
                            this.$emit('remove', this.file.id);
                        }
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
        },
        filters: {
            decimalPlaces(value) {
                return parseFloat(value).toFixed(2);
            },
            decimalLevel: function (value) {
                return parseFloat(value).toLocaleString();
            },
        }
    }
</script>
