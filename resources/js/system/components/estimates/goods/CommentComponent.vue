<template>
    <td>
        {{ item.product.article.name }}<span v-if="isArchive"> (Архивный)</span>
        <span
            class="icon-comment"
            :class="[{ 'hide-comment' : ! showComment }]"
            :data-toggle="'comment-dropdown-' + item.id"
        ></span>
        <div
            class="dropdown-pane"
            :id="'comment-dropdown-' + item.id"
            data-dropdown
            data-auto-focus="true"


        >
<!--            data-close-on-click="true"-->
<!--            v-dropdown-->
            <template v-if="editComment">
                <textarea
                    name="comment"
                    @keydown.enter.prevent="updateItemComment"
                    v-model="comment"
                >{{ item.comment }}</textarea>
                <a
                    class="button"
                    @click="updateItemComment"
                >Сохранить</a>
                <a
                    v-show="showComment"
                    class="button"
                    @click="changeEditComment"
                >Отменить</a>
            </template>
            <p
                v-else
                @click="changeEditComment"
            >{{ item.comment }}</p>
        </div>
    </td>
</template>

<script>
    export default {
        props: {
            item: Object,
            isArchive: Boolean
        },
        data() {
            return {
                comment: this.item.comment,
                editComment: false,
            }
        },
        mounted() {
            // Foundation.reInit($('#comment-dropdown-' + this.item.id));

            if (this.item.comment == null) {
                this.editComment = true
            }
        },
        computed: {
            showComment() {
                if (this.item.comment != null) {
                    return this.item.comment.length > 0;
                } else {
                    return false;
                }
            },
        },
        methods: {
            changeEditComment() {
                if (this.item.comment != null) {
                    this.editComment = !this.editComment;
                }
            },
            updateItemComment() {
                axios
                    .patch('/admin/estimates_goods_items/' + this.item.id, {
                        comment: this.comment,
                    })
                    .then(response => {
                        $('comment-dropdown-' + this.item.id).foundation('close');
                        this.$store.commit('UPDATE_GOODS_ITEM', response.data);
                        this.comment = response.data.comment;
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
        },
        directives: {
            // 'dropdown': {
            //     bind: function (el) {
            //         new Foundation.Dropdown($(el))
            //     },
            // },
            focus: {
                inserted: function (el) {
                    el.focus()
                }
            }
        },
    }
</script>
