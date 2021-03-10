<template>
    <fieldset class="fieldset-access">
        <legend>Загрузка файлов</legend>
        <div class="grid-x">
            <div class="cell small-12">
                <a
                    class="button"
                    :data-open="'modal-add-file-' + alias"
                >Добавить файл</a>
            </div>

            <div class="cell small-12">
                <table class="table-compositions">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Название</th>
                        <th>Ссылка</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>

                    <draggable
                        v-model="files"
                        tag="tbody"
                        id="table-prices"
                        handle=".td-drop"
                        @input="sort"
                    >
                    <file-component
                        v-for="file in files"
                        :file="file"
                        :alias="alias"
                        :key="file.id"
                        @update="openModalRemove"
                        @remove="remove"
                    ></file-component>
                    </draggable>
                </table>
            </div>

            <modal-store-component
                :alias="alias"
                :id="id"
                @add="add"
            ></modal-store-component>

            <modal-update-component
                :alias="alias"
                :item="updatingItem"
                @update="update"
            ></modal-update-component>
        </div>


    </fieldset>
</template>

<script>
import draggable from 'vuedraggable'

export default {
    components: {
        'modal-store-component': require('./modals/StoreComponent'),
        'file-component': require('./FileComponent'),
        draggable,
        'modal-update-component': require('./modals/UpdateComponent'),
    },
    props: {
        alias: String,
        id: Number,
        itemFiles: {
            type: Array,
            default: () => {
                return [];
            }
        }
    },
    data() {
        return {
            files: this.itemFiles,
            updatingItem: {
                name: null,
                description: null,
                title: null,
            },
        }
    },
    methods: {
        sort(items) {
            let sortedItems = [];
            items.forEach(item => {
                sortedItems.push(item.id);
            });
            axios
                .post('/admin/sort/files', {
                    files: sortedItems
                })
                .then(response => {
                    // console.log(response.data);
                })
                .catch(error => {
                    console.log(error)
                });
        },
        add(file) {
            this.files.push(file);
        },
        openModalRemove(file = null) {
            this.updatingItem = file;
        },
        update(file) {
            let index = this.files.findIndex(obj => obj.id === file.id);
            Vue.set(this.files, index, file);
        },
        remove(id) {
            const index = this.files.findIndex(obj => obj.id === id);
            this.files.splice(index, 1);
        },
    },
}
</script>
