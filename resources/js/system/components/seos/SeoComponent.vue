<template>
    <div class="grid-x grid-padding-x">
        <div class="cell small-12 medium-6">

            <div class="grid-x grid-padding-x">
                <div class="cell small-12">
                    <label>Название страницы (Title)
                        <string-component
                            v-model="title"
                            ref="titleComponent"
                            name="seo[title]"
                        ></string-component>
                    </label>
                </div>
                <div class="cell small-12">
                    <label>Заголовок страницы (H1)
                        <string-component
                            v-model="h1"
                            ref="h1Component"
                            name="seo[h1]"
                        ></string-component>
                    </label>
                </div>
                <div class="cell small-12">
                    <label>Описание для поисковых систем (Description)
                        <textarea-component
                            v-model="description"
                            ref="descriptionComponent"
                            name="seo[description]"
                        ></textarea-component>
                    </label>
                </div>
                <div class="cell small-12">
                    <label>Список ключевых слов (Keywords)
                        <string-component
                            v-model="keywords"
                            ref="keywordsComponent"
                            name="seo[keywords]"
                        ></string-component>
                    </label>
                </div>
                <div class="cell small-12 checkbox">
                    <input
                        type="hidden"
                        name="seo[is_canonical]"
                        value="0"
                    >
                    <input
                        id="checkbox-seo-is_canonical"
                        type="checkbox"
                        v-model="is_canonical"
                        name="seo[is_canonical]"
                        value="1"
                    >
                    <label for="checkbox-seo-is_canonical">
                        <span>Каноническая ссылка</span>
                    </label>

                </div>
                <div class="cell small-12">
                    <label>Контент</label>
                        <ckeditor
                            :editor="editor"
                            v-model="content"
                            :config="editorConfig"
                            ref="contentComponent"
                            name="seo[content]"
                        ></ckeditor>
                </div>
            </div>
        </div>
        <div class="cell small-12 medium-6">

            <additional-seos-component></additional-seos-component>
        </div>
    </div>
</template>

<script>
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

export default {
    components: {
        'string-component': require('../inputs/StringComponent'),
        'textarea-component': require('../inputs/TextareaComponent'),

        'additional-seos-component': require('./additionals/SeosComponent'),
    },
    props: {
        seo: {
            type: Object,
            default: () => {
                return {
                    title: null,
                    h1: null,
                    description: null,
                    keywords: null,
                    content: null,

                    is_canonical: false
                };
            }
        }
    },
    mounted() {
        this.$store.commit('SET_SEOS', this.seo);
    },
    data() {
        return {
            title: this.seo.title,
            h1: this.seo.h1,
            description: this.seo.description,
            keywords: this.seo.keywords,
            content: this.seo.content,

            is_canonical: this.seo.is_canonical,

            editor: ClassicEditor,
            editorConfig: this.$store.state.seo.editorConfig,
        }
    },
}
</script>

