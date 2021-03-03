<template>
    <tr class="item">
        <td class="td-drop">
            <div class="sprite icon-drop"></div>
        </td>
        <td>{{ file.name }}</td>
        <td>
            <a
                :href="file.path"
                target="_blank"
            >{{ file.path}}</a>
        </td>
        <td class="actions-list">
            <div
                class="icon-list-edit sprite"
                data-open="modal-update-file"
                @click="update"
            ></div>
        </td>
        <td>

            <display-component
                :item="file"
                alias="files"
            ></display-component>
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
        components: {
            'display-component': require('../common/DisplayComponent'),
        },
        props: {
            file: Object,
        },
        methods: {
            update() {
                this.$emit('update', this.file)
            },
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
    }
</script>
