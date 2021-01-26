<template>
    <div class="grid-x grid-padding-x">
        <div class="cell medium-6">
            <label>Название
                <string-component
                    v-model="name"
                    name="scheme_name"
                    ref="nameComponent"
                ></string-component>
            </label>
        </div>
        <div class="cell medium-6">
            <label>Пометка для SEO
                <string-component
                    v-model="title"
                    name="title"
                    ref="titleComponent"
                ></string-component>
            </label>
        </div>
        <div class="cell medium-6">
            <label>Описание
                <textarea-component
                    v-model="description"
                    name="scheme_description"
                    ref="descriptionComponent"
                ></textarea-component>
            </label>
        </div>
        <div class="cell medium-6">
            <label>Файл
                <input
                    name="file"
                    type="file"
                    ref="fileComponent"
                    @change="onChange"
                >
            </label>
            <a
                class="button"
                @click="add"
            >Добавить</a>
        </div>
    </div>
</template>

<script>

export default {
    components: {
        'string-component': require('../inputs/StringComponent'),
        'textarea-component': require('../inputs/TextareaComponent'),
    },
    props: {
        alias: String,
        id: Number,
    },
    data() {
        return {
            name: null,
            description: null,
            title: null,
            file: null
        }
    },
    methods: {
        onChange() {
            this.file = this.$refs.fileComponent.files[0];
        },
        add() {
            if (this.name && this.name.length && this.file) {
                let fd = new FormData();

                fd.append('alias', this.alias);
                fd.append('id', this.id);

                fd.append('name', this.name);
                fd.append('description', this.description);
                fd.append('title', this.title);
                fd.append('file', this.file);

                axios
                    .post('/admin/files', fd)
                    .then(response => {
                        this.$emit('add', response.data);
                        this.reset();
                    })
                    .catch(error => {
                        console.log(error)
                    });
            }
        },
        reset() {
            this.name = null;
            this.$refs.nameComponent.update();

            this.description = null;
            this.$refs.descriptionComponent.update();

            this.title = null;
            this.$refs.titleComponent.update();

            this.file = null;
            this.$refs.fileComponent.value = null;
        },
    },
}
</script>
