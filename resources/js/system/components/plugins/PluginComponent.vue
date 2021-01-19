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
        <td class="display">
            <div
                :class="'sprite icon-display-' + display"
                @click="changeDisplay"
            ></div>
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
        computed: {
	        display() {
	            if (this.plugin.display == 1) {
                    return 'show';
                } else {
                    return 'hide';
                }
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
            changeDisplay() {
                const action = this.plugin.display == 1 ? 0 : 1;
                axios
                    .post('/admin/display', {
                    id: this.plugin.id,
                    action: action,
                    entity_alias: 'plugins'
                    })
                    .then(response => {
                        if (response.data) {
                            this.plugin.display = action;
                        }
                    })
                    .catch(error => {
                        console.log(error)
                    });
            }
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
