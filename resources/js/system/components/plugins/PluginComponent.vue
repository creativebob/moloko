<template>

    <tr class="item">
        <td>{{ plugin.account.source_service.source.name }}.{{ plugin.account.source_service.name }}</td>
        <td>
            <span
                @click="editPlugin = !editPlugin"
                v-if="!editPlugin"
            >{{ curCode }}</span>
            <textarea
                v-else
                v-model="code"
                v-focus
                @keydown.enter.prevent="updatePlugin"
                @focusout="editPlugin = false"
            ></textarea>
        </td>
        <td class="td-delete">
            <a
                @click="openModalRemove"
                class="icon-delete sprite"
                data-open="modal-delete-plugin"
            ></a>
        </td>
    </tr>


</template>

<script>
	export default {
	    props: {
	        plugin: Object
        },
        data() {
            return {
                curCode: this.plugin.code,
                code: this.plugin.code,
                editPlugin: false
            }
        },
        methods: {
            updatePlugin() {
                this.editPlugin = false;
                axios
                    .patch('/admin/plugins/' + this.plugin.id, {
                        code: this.code,
                    })
                    .then(response => {
                        this.$emit('update', response.data);
                        this.curCode = response.data.code;
                        this.code = response.data.code;
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            openModalRemove() {
                this.$emit('open-modal-remove', this.plugin);
            },
        },
        directives: {
            focus: {
                inserted: function (el) {
                    el.focus()
                }
            }
        },

	}
</script>
