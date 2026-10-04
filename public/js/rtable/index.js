const { createApp } = Vue;

createApp({
    data() {
        return {
            is_follow: 0,
            follow_count: 0,
            follow_btn_title: "طرفدار شدن"
        };
    },
    methods: {
        follow(fea_id, item_id) {
            $("#follow-item-btn").css("display", "none");
            setTimeout(
                () => $("#follow-item-btn").css("display", "inline"),
                1000
            );
            if (this.is_follow) {
                axios
                    .post("/unfollow_feature_item/" + fea_id + "/" + item_id)
                    .then(
                        response => (this.is_follow = 0),
                        (this.follow_count -= 1),
                        (this.follow_btn_title = "طرفدار شدن")
                    );
            } else {
                axios
                    .post("/follow_feature_item/" + fea_id + "/" + item_id)
                    .then(
                        response => (this.is_follow = 1),
                        (this.follow_count += 1),
                        (this.follow_btn_title = "طرفدار هستید")
                    );
            }
        },
        initValues() {
            if (item_follow_count != null) {
                this.follow_count = item_follow_count;
                if (is_item_follow == 1) {
                    this.is_follow = 1;
                    this.follow_btn_title = "طرفدار هستید";
                } else {
                    this.is_follow = 0;
                    this.follow_btn_title = "طرفدار شدن";
                }
            }
        }
    },
    mounted() {
        this.initValues();
    },
    components: {
        "follow-fea": {
            template: `<div v-if="ableBtn" class="col-12 col-sm-10"><button v-on:click="follow(id)" type="button"
                                        class="list-group-item mb-2 list-group-item-action">{{title}}
                                    </button></div>`,
            props: ["id", "title"],
            data() {
                return {
                    ableBtn: 1
                };
            },
            methods: {
                follow: function(fea_id) {
                    (this.ableBtn = 0),
                        setTimeout(() => (this.ableBtn = 1), 300);
                    this.$emit("select-c", cat);
                }
            }
        }
    }
}).mount("#app");
