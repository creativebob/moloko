<template>
    <div
        class="wrap-comment"
    >
<!--        @click.self="editComment = false"-->
        <span

            class="icon-comment"
            @click="editComment = !editComment"
        ></span>
        <!--        :class="[{ 'hide-comment' : ! showComment }]"-->
        <!--        :data-toggle="'comment-dropdown-' + item.id"-->
        <!--        data-tooltip-->
        <!--            tabindex="1"-->
        <!--            :title="comment"-->
        <!--        <div-->
        <!--            class="dropdown-pane"-->
        <!--            :id="'comment-dropdown-' + item.id"-->
        <!--            data-dropdown-->
        <!--            data-auto-focus="true"-->
        <!--            v-dropdown-->
        <!--        >-->
        <!--            data-close-on-click="true"-->
        <div
            class="dropdown-comment dropdown-pane"
            v-if="editComment"
        >
                <textarea
                    @keydown.enter.prevent="updateComment"
                    v-model="curComment"
                    @blur="alert(1)"
                >{{ curComment }}</textarea>
            <a
                class="button"
                @click="updateComment"
            >Сохранить</a>
            <!--                <a-->
            <!--                    v-show="showComment"-->
            <!--                    class="button"-->
            <!--                    @click="changeEditComment"-->
            <!--                >Отменить</a>-->
            <!--            <p-->
            <!--                v-else-->
            <!--                @click="changeEditComment"-->
            <!--            >{{ item.comment }}</p>-->
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            comment: String,
        },
        data() {
            return {
                curComment: this.comment,
                editComment: false,
            }
        },
        // created() {
        //     document.addEventListener('click', () => this.editComment = false);
        // },
        // mounted() {
        // Foundation.reInit($('#comment-dropdown-' + this.item.id));

        // if (this.item.comment == null) {
        //     this.editComment = true
        // }
        // },
        // computed: {
        //     showComment() {
        //         if (this.item.comment != null) {
        //             return this.item.comment.length > 0;
        //         } else {
        //             return false;
        //         }
        //     },
        // },
        methods: {
            // changeEditComment() {
            //     if (this.item.comment != null) {
            //         this.editComment = !this.editComment;
            //     }
            // },
            updateComment() {
                this.editComment = false;
                this.$emit('update', this.curComment);
            },
        },
        // directives: {
        //     'dropdown': {
        //         bind: function (el) {
        //             new Foundation.Dropdown($(el))
        //         },
        //     },
        //     focus: {
        //         inserted: function (el) {
        //             el.focus()
        //         }
        //     }
        // },
    }
</script>
