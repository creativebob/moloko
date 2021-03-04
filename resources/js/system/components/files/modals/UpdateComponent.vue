<template>
    <div
        class="reveal"
        :id="'modal-update-file-' + alias"
        data-reveal
        data-close-on-click="false"
    >
        <div class="grid-x">
            <div class="cell small-12 modal-title">
                <h5>Редактирование файла</h5>
            </div>
        </div>
        <div
            v-if="item"
            class="grid-x grid-padding-x align-center modal-content inputs"
        >
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
                </div>
            </div>
        </div>
        <div class="grid-x align-center">
            <div class="cell small-6 medium-4">
                <button
                    @click="update"
                    class="button modal-button"
                    :disabled="disabledButton"
                >Сохранить
                </button>
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
        item: Object,
        alias: String,
    },
    watch: {
        item(item) {
            this.name = item.name;
            this.$refs.nameComponent.update(this.name);

            this.description = item.description;
            this.$refs.descriptionComponent.update(this.description);

            this.title = item.title;
            this.$refs.titleComponent.update(this.title);
        }
    },
    data() {
        return {
            name: null,
            description: null,
            title: null,

            disabledButton: false,
        }
    },
    methods: {

        update() {
            if (this.name && this.name.length) {
                this.disabledButton = true;

                axios
                    .patch('/admin/files/' + this.item.id, {
                        name: this.name,
                        description: this.description,
                        title: this.title,
                    })
                    .then(response => {
                        $('#modal-update-file-' + this.alias).foundation('close');
                        this.$emit('update', response.data);
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
        },
    },
}
</script>
