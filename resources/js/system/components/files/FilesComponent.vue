<template>
    <div class="grid-x">

        <div class="cell small-12">
            <store-component
                :alias="alias"
                :id="id"
                @add="add"
            ></store-component>
        </div>

        <div
            class="cell small-12"
            v-if="files.length"
        >
            <table class="table-payments">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Ссылка</th>
                        <th></th>
                     </tr>
                </thead>

                <tbody>
                    <file-component
                        v-for="file in files"
                        :file="file"
                        :key="file.id"
                        @remove="remove"
                    ></file-component>
                    </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        components: {
            'store-component': require('./StoreComponent'),
            'file-component': require('./FileComponent'),
        },
        props: {
            alias: String,
            id: Number,
            itemFiles: Array
        },
        data() {
            return {
                files: this.itemFiles,
            }
        },
        methods: {
            add(file) {
                this.files.push(file);
            },
            remove(id) {
                const index = this.files.findIndex(obj => obj.id === id);
                this.files.splice(index, 1);
            },
        },
    }
</script>
