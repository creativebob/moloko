<template>

    <div class="grid-x grid-padding-x">
        <div class="small-12 medium-7 cell">
            <table id="table-plugins" class="hover unstriped">

                <thead>
                    <tr>
                        <th>Аккаунт</th>
                        <th>Код</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    <plugin-component
                        v-for="(plugin, index) in plugins"
                        :plugin="plugin"
                        :index="index"
                        :key="plugin.id"
                        @update="updatePlugin"
                        @open-modal-remove="openModalRemove"
                    ></plugin-component>
                </tbody>

            </table>
        </div>
        <div class="small-12 medium-5 cell">

            <plugin-form-component
                :accounts="accounts"
                :domain="domain"
                @add="addPlugin"
            ></plugin-form-component>

        </div>

        <div class="reveal rev-small" id="modal-delete-plugin" data-reveal data-close-on-click="false">
            <div class="grid-x">
                <div class="small-12 cell modal-title">
                    <h5>Удаление плагина</h5>
                </div>
            </div>
            <div class="grid-x align-center modal-content ">
                <div class="small-10 cell text-center">
                    <p>Удаляем плагин "{{ deletingPlugin.account.source_service.source.name }}.{{ deletingPlugin.account.source_service.name }}", вы уверены?</p>
                </div>
            </div>
            <div class="grid-x align-center grid-padding-x">
                <div class="small-6 medium-4 cell">
                    <button
                        data-close
                        class="button modal-button metric-delete-button"
                        @click="removePlugin"
                    >Удалить</button>
                </div>
                <div class="small-6 medium-4 cell">
                    <button data-close class="button modal-button" id="save-button" type="submit">Отменить</button>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
	export default {
        components: {
            'plugin-component': require('./PluginComponent.vue'),
            'plugin-form-component': require('./PluginFormComponent.vue')
        },
        props: {
            domain: Object,
            accounts: Array,
        },
		data() {
			return {
                plugins: this.domain.plugins,
                deletingPlugin: {
                    account: {
                        source_service: {
                            name: '',
                            source: {
                                name: ''
                            }
                        }
                    }
                },
			}
		},

        computed: {
            pluginsList() {
                return this.$store.state.lead.goodsItems;
            },

            // actualMetrics() {
            //     return this.metrics;
            // }
        },


        methods: {
            openFormCreate() {
                this.createPlugin = true;
                this.editPlugin = false;
            },
            openFormEdit() {
                this.editPlugin = true;
                this.createPlugin = false;
            },
            closeForm() {
                this.createPlugin = false;
                this.editPlugin = false;
            },
            addPlugin(plugin) {
                this.plugins.push(plugin);
            },
            updatePlugin(plugin) {
                let index = this.plugins.find(obj => obj.id === plugin.id);
                Vue.set(this.plugins, index, plugin);
            },
            removePlugin() {
                axios
                    .delete('/admin/plugins/' + this.deletingPlugin.id)
                    .then(response => {
                        if (response.data > 0) {
                            let index = this.plugins.findIndex(plugin => plugin.id === this.deletingPlugin.id);
                            this.plugins.splice(index, 1);
                            this.deletingPlugin = {
                                account: {
                                    source_service: {
                                        name: '',
                                            source: {
                                            name: ''
                                        }
                                    }
                                }
                            };
                        }
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            openModalRemove(metric) {
                this.deletingPlugin = metric;
            },

        }
	}
</script>
