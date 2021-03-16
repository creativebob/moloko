<template>
    <div
        class="reveal"
        id="modal-add-additional-seo"
        data-reveal
        data-close-on-click="false"
    >
        <div class="grid-x">
            <div class="cell small-12 modal-title">
                <h5>Добавление SEO</h5>
            </div>
        </div>

        <form-component
            :columns="columns"
            @change="change"
            method="store"
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
                    @click="add"
                    class="button modal-button"
                >Добавить</button>
            </div>
        </div>
        <div
            @click="reset"
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
        columns: Array
    },
    data() {
        return {
            data: [],
            errors: []
        }
    },
    methods: {
        change(data) {
            this.data = data;
        },
        add() {
            this.errors = [];
            if (this.data.title && this.data.title.length && this.data.params.length >= 1) {
                $('#modal-add-additional-seo').foundation('close');
                this.$emit('add', this.data);
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
