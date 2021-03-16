<template>
    <div class="grid-x grid-padding-x">
        <div class="cell small-12">

            <div class="grid-x tabs-wrap tabs-margin-top align-center">
                <div class="small-8 cell">

                    <ul class="tabs-list" data-tabs id="tabs">
                        <li class="tabs-title is-active">
                            <a
                                :href="'#tab-additional-seo-general-' + method"
                                aria-selected="true"
                            >Основное</a>
                        </li>
                        <li class="tabs-title">
                            <a
                                :data-tabs-target="'tab-additional-seo-content-' + method"
                                :href="'#tab-additional-seo-content-' + method"
                            >Контент</a>
                        </li>
                    </ul>

                </div>
            </div>
            <div class="tabs-wrap inputs">
                <div class="tabs-content" data-tabs-content="tabs">

                    <div
                        class="tabs-panel is-active"
                        :id="'tab-additional-seo-general-' + method"
                    >
                        <div class="grid-x grid-padding-x align-center modal-content inputs">
                            <div class="cell small-12">
                                <div class="grid-x grid-padding-x">
                                    <div class="cell small-12">
                                        <label>Название страницы (Title)
                                            <string-component
                                                v-model="title"
                                                ref="titleComponent"
                                                @change="change"
                                            ></string-component>
                                        </label>
                                    </div>
                                    <div class="cell small-12">
                                        <label>Заголовок страницы (H1)
                                            <string-component
                                                v-model="h1"
                                                ref="h1Component"
                                                @change="change"
                                            ></string-component>
                                        </label>
                                    </div>
                                    <div class="cell small-12">
                                        <label>Описание для поисковых систем (Description)
                                            <textarea-component
                                                v-model="description"
                                                ref="descriptionComponent"
                                                @change="change"
                                            ></textarea-component>
                                        </label>
                                    </div>
                                    <div class="cell small-12">
                                        <label>Список ключевых слов (Keywords)
                                            <string-component
                                                v-model="keywords"
                                                ref="keywordsComponent"
                                                @change="change"
                                            ></string-component>
                                        </label>
                                    </div>
                                    <div class="cell small-12 checkbox">
                                        <input
                                            :id="'checkbox-additional-seo-is_canonical-' + method"
                                            type="checkbox"
                                            v-model="is_canonical"
                                            @change="change"
                                        >
                                        <label
                                            :for="'checkbox-additional-seo-is_canonical-' + method"
                                        >
                                            <span>Каноническая ссылка</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="cell small-12">
                                <params-component
                                    :params="curParams"
                                    @change="changeParams"
                                    ref="paramsComponent"
                                ></params-component>
                            </div>
                        </div>
                    </div>

                    <div
                        class="tabs-panel"
                        :id="'tab-additional-seo-content-' + method"
                    >
                        <div class="grid-x grid-padding-x align-center modal-content inputs">
                            <div class="cell small-12">
                                <label>Контент
                                    <ckeditor
                                        :editor="editor"
                                        v-model="content"
                                        :config="editorConfig"
                                        ref="contentComponent"
                                        @input="change"
                                    ></ckeditor>
<!--                                    <textarea-component-->
<!--                                        v-model="content"-->
<!--                                        ref="contentComponent"-->
<!--                                        @change="change"-->
<!--                                    ></textarea-component>-->
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</template>

<script>
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

export default {
    components: {
        'string-component': require('../../../inputs/StringComponent'),
        'textarea-component': require('../../../inputs/TextareaComponent'),
        'params-component': require('../params/ParamsComponent'),
    },
    props: {
        item: {
            type: Object,
            default: () => {
                return {
                    title: null,
                    h1: null,
                    description: null,
                    keywords: null,
                };
            }
        },
        columns: Array,
        method: String
    },
    data() {
        return {
            title: null,
            h1: null,
            description: null,
            keywords: null,
            content: '',
            is_canonical: false,

            params: [],

            editor: ClassicEditor,
            editorConfig:{
            }
        }
    },

    watch: {
        item(item) {
            this.columns.forEach(column => {
                if (column == 'params') {
                    this[column] = item[column]
                } else if (column == 'is_canonical') {
                    this[column] = item[column] == 1 ? true : false;
                } else if (column == 'content') {
                    this[column] = item[column] ? item[column] : '';
                } else {
                    this[column] = item[column];
                    this.$refs[column + 'Component'].update(this[column]);
                }
            })
            this.change();
        }
    },

    computed: {
        curParams() {
            return this.params;
        }
    },

    methods: {
        change() {
            let data = {};
            this.columns.forEach(column => {
                data[column] = this[column];
            });
            this.$emit('change', data);
        },
        changeParams(params) {
            this.params = params;
            this.change();
        },
        reset() {
            this.columns.forEach(column => {
                if (column == 'params') {
                    this[column] = [];
                    this.$refs.paramsComponent.reset();
                } else if (column == 'is_canonical') {
                    this[column] = false;
                } else if (column == 'content') {
                    this[column] = '';
                }  else {
                    this[column] = null;
                    let name = column + 'Component';
                    this.$refs[name].update();
                }
            });
        },
    }
}
</script>
