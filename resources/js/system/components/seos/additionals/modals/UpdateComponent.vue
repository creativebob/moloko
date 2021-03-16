<template>
    <div
        class="reveal"
        id="modal-update-additional-seo"
        data-reveal
        data-close-on-click="false"
    >
        <div class="grid-x">
            <div class="cell small-12 modal-title">
                <h5>Редактирование SEO</h5>
            </div>
        </div>

        <form-component
            :columns="columns"
            :item="item"
            @change="change"
            method="update"
            ref="formComponent"
        ></form-component>

        <div
            v-if="errors.length"
            class="grid-x align-center"
        >
            <div class="cell small-6 medium-4">
                <ul>
                    <li v-for="error in errors">{{ error }}</li>
                </ul>
            </div>
        </div>

        <div class="grid-x align-center">
            <div class="cell small-6 medium-4">
                <button
                    @click="update"
                    class="button modal-button"
                >Сохранить
                </button>
            </div>
        </div>
        <div
            data-close
            class="icon-close-modal sprite button-modal-close"
        ></div>
    </div>
</template>

<script>

export default {
    components: {
        'form-component': require('./FormComponent'),
    },
    props: {
        item: Object,
        columns: Array
    },
    data() {
        return {
            data: [],
            errors: [],
        }
    },
    methods: {
        change(data) {
            this.data = data;
        },
        update() {
            this.errors = [];
            if (this.data.title && this.data.title.length && this.data.params.length >= 1) {
                $('#modal-update-additional-seo').foundation('close');
                if (this.item.id) {
                    this.data.id = this.item.id;
                }
                this.$emit('update', this.data);
                this.reset();
            } else {
                if (!this.data.title) {
                    this.errors.push('Не заполнено поле title');
                }
                if (!this.data.params || this.data.params.length == 0) {
                    this.errors.push('Нет ни одного параметра');
                }
            }
        },
        reset() {
            this.errors = [];
            this.data = [];
            this.$refs.formComponent.reset();
        }
    },
}
</script>
