<template>
    <div
        class="reveal"
        :id="'modal-add-file-' + alias"
        data-reveal
        data-close-on-click="false"
    >
        <div class="grid-x">
            <div class="cell small-12 modal-title">
                <h5>Добавление файла</h5>
            </div>
        </div>
        <div class="grid-x grid-padding-x align-center modal-content inputs">
            <div class="cell small-12">
                <div class="grid-x grid-padding-x">
                    <div class="cell small-12">
                        <label>Название
                            <string-component
                                v-model="name"
                                ref="nameComponent"
                            ></string-component>
                        </label>
                    </div>
                    <div class="cell small-12">
                        <label>Пометка для SEO
                            <string-component
                                v-model="title"
                                ref="titleComponent"
                            ></string-component>
                        </label>
                    </div>
                    <div class="cell small-12">
                        <label>Описание
                            <textarea-component
                                v-model="description"
                                ref="descriptionComponent"
                            ></textarea-component>
                        </label>
                    </div>
                    <div class="cell small-12">
                        <label>Файл
                            <input
                                name="file"
                                type="file"
                                ref="fileComponent"
                                @change="onChange"
                            >
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-x align-center">
            <div class="cell small-6 medium-4">
                <button
                    @click="add"
                    class="button modal-button"
                    :disabled="disabledButton"
                >Загрузить</button>
            </div>
        </div>
        <div
            @click="reset"
            data-close
            class="icon-close-modal sprite button-modal-close"
            :disabled="disabledButton"
        ></div>
    </div>
</template>

<script>

export default {
    components: {
        'string-component': require('../../inputs/StringComponent'),
        'textarea-component': require('../../inputs/TextareaComponent'),
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
            file: null,

            disabledButton: false,
        }
    },
    methods: {
        onChange() {
            this.file = this.$refs.fileComponent.files[0];
        },
        add() {
            if (this.name && this.name.length && this.file) {
                this.disabledButton = true;

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
                        $('#modal-add-file-' + this.alias).foundation('close');
                        this.$emit('add', response.data);
                        this.reset();
                        this.disabledButton = false;
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
