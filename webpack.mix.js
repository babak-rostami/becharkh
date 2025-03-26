const mix = require("laravel-mix");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.minify(
    [
        "public/assets/js/forum/index.js",
        "public/assets/js/item/follow.js",
        "public/assets/js/item/price.js",
        "public/assets/js/tabs/top-tab.js",
        "public/assets/js/bslider/index.js",
        "public/assets/js/gallery/show.js",
        "public/assets/js/affilate/show-box.js",
        // "public/assets/js/pages/fifilter.js",
        "public/assets/js/pages/share-page.js"
    ],
    "public/mixassets/js/forum/index.min.js"
);

mix.minify(
    [
        "public/js/rtable/create.js",
        "public/assets/js/pages/comment-box.js",
        "public/assets/js/survey/create.js"
    ],
    "public/mixassets/js/forum/create.min.js"
);

mix.minify(
    ["public/js/rtable/edit.js", "public/assets/js/pages/comment-box.js"],
    "public/mixassets/js/forum/edit.min.js"
);

mix.minify(
    ["public/js/rtable/admin-edit.js",
        "public/assets/js/pages/comment-box.js",
        "public/assets/js/pages/forum/sasf-edit.js"],
    "public/mixassets/js/forum/admin-edit.min.js"
);

mix.minify(
    [
        "public/js/rtable/create.js",
        "public/assets/js/pages/comment-box.js",
        "public/assets/js/survey/create.js",
        "public/assets/js/admin/check-fake-user.js"
    ],
    "public/mixassets/js/forum/admin-create.min.js"
);

mix.minify(
    [
        "public/assets/js/category/comment/index.js",
        "public/assets/js/item/follow.js",
        "public/assets/js/item/price.js",
        "public/assets/js/tabs/top-tab.js",
        "public/assets/js/bslider/index.js",
        "public/assets/js/pages/btns.js",
        "public/assets/js/pages/comment-box.js",
        "public/assets/js/survey/create.js",
        "public/assets/js/survey/show.js",
        "public/assets/js/gallery/show.js",
        "public/assets/js/affilate/show-box.js",
        // "public/assets/js/pages/fifilter.js",
        "public/assets/js/pages/share-page.js"
    ],
    "public/mixassets/js/category/comment/index.min.js"
);
mix.minify(
    [
        "public/assets/js/category/comment-create.js",
        "public/assets/js/pages/comment-box.js",
        "public/assets/js/survey/create.js",
        "public/assets/js/admin/check-fake-user.js"
    ],
    "public/mixassets/js/category/comment-create.min.js"
);

mix.minify(
    [
        "public/assets/js/advertise/index.js",
        "public/assets/js/item/follow.js",
        "public/assets/js/item/price.js",
        "public/assets/js/bslider/index.js",
        "public/assets/js/tabs/top-tab.js",
        "public/assets/js/gallery/show.js",
        "public/assets/js/affilate/show-box.js",
        // "public/assets/js/pages/fifilter.js",
        "public/assets/js/pages/share-page.js"
    ],
    "public/mixassets/js/advertise/index.min.js"
);

mix.minify(
    [
        "public/assets/js/blog/index.js",
        "public/assets/js/item/follow.js",
        "public/assets/js/item/price.js",
        "public/assets/js/tabs/top-tab.js",
        "public/assets/js/bslider/index.js",
        // "public/assets/js/pages/fifilter.js"
    ],
    "public/mixassets/js/blog/index.min.js"
);

mix.minify(
    [
        "public/assets/js/blog/show.js",
        "public/assets/js/item/follow.js",
        "public/assets/js/item/price.js",
        "public/assets/js/tabs/top-tab.js",
        "public/assets/js/bslider/index.js",
        "public/assets/js/gallery/show.js",
        "public/assets/js/affilate/show-box.js",
        "public/assets/js/pages/share-page.js"
    ],
    "public/mixassets/js/blog/show.min.js"
);

mix.minify(
    ["public/js/blog/create.js", "public/assets/js/pages/comment-box.js"],
    "public/mixassets/js/blog/create.min.js"
);

mix.minify(
    ["public/js/blog/edit.js", "public/assets/js/pages/comment-box.js"],
    "public/mixassets/js/blog/edit.min.js"
);

mix.minify(
    [
        "public/assets/js/forum/show.js",
        "public/assets/js/item/follow.js",
        "public/assets/js/item/price.js",
        "public/assets/js/tabs/top-tab.js",
        "public/assets/js/bslider/index.js",
        "public/assets/js/pages/btns.js",
        "public/assets/js/pages/comment-box.js",
        "public/assets/js/survey/show.js",
        "public/assets/js/gallery/show.js",
        "public/assets/js/affilate/show-box.js",
        "public/assets/js/pages/share-page.js"
    ],
    "public/mixassets/js/forum/show.min.js"
);

mix.minify(
    ["public/assets/js/pages/comment-box.js"],
    "public/mixassets/js/forum/edit-answer-admin.min.js"
);

mix.minify(
    ["public/assets/js/pages/comment-box.js",
        "public/assets/js/admin/check-fake-user.js"],
    "public/mixassets/js/forum/answers-admin.min.js"
);

mix.minify(
    [
        "public/assets/js/advertise/show.js",
        "public/assets/js/item/follow.js",
        "public/assets/js/item/price.js",
        "public/assets/js/tabs/top-tab.js",
        "public/assets/js/bslider/index.js"
    ],
    "public/mixassets/js/advertise/show.min.js"
);

mix.minify(
    ["public/assets/js/dashboard/dash-edit.js"],
    "public/mixassets/js/user/dashboard-edit.min.js"
);

mix.minify(
    ["public/assets/js/home.js", "public/assets/js/bslider/index.js"],
    "public/mixassets/js/home.min.js"
);

mix.minify(
    ["public/js/advertise/edit.js"],
    "public/mixassets/js/advertise/edit.min.js"
);
mix.minify(
    ["public/js/advertise/create.js"],
    "public/mixassets/js/advertise/create.min.js"
);
mix.minify(
    [
        "public/assets/js/affilate/edit.js",
        "public/assets/js/pages/comment-box.js",
        "public/assets/js/pages/forum/sasf-edit.js"
    ],
    "public/mixassets/js/affilate/edit.min.js"
);
mix.minify(
    [
        "public/assets/js/affilate/create.js",
        "public/assets/js/pages/comment-box.js"
    ],
    "public/mixassets/js/affilate/create.min.js"
);

mix.minify(
    [
        "public/assets/js/affilate/show.js",
        "public/assets/js/item/follow.js",
        "public/assets/js/bslider/index.js",
        "public/assets/js/gallery/show.js",
        "public/assets/js/affilate/show-box.js",
        "public/assets/js/pages/share-page.js",
        "public/assets/js/tabs/top-tab.js",
        "public/assets/js/pages/comment-box.js"
    ],
    "public/mixassets/js/affilate/show.min.js"
);
mix.minify(
    [
        "public/assets/js/affilate/comment-create.js",
        "public/assets/js/pages/comment-box.js",
        "public/assets/js/admin/check-fake-user.js"
    ],
    "public/mixassets/js/affilate/comment-create.min.js"
);

// for css //////////////////////////////////////////

mix.minify(
    [
        "public/assets/css/affilate/create.css",
        "public/assets/css/pages/comment-box.css"
    ],
    "public/mixassets/css/affilate/create.min.css"
);
mix.minify(
    [
        "public/assets/css/affilate/edit.css",
        "public/assets/css/pages/comment-box.css",
        "public/assets/css/pages/forum/sasf-edit.css",
    ],
    "public/mixassets/css/affilate/edit.min.css"
);

mix.minify(
    ["public/assets/css/home1.css",
        "public/assets/css/bslider/index.css",
        "public/assets/css/pages/hot-pages.css"],
    "public/mixassets/css/home.min.css"
);

mix.minify(
    ["public/assets/css/dashboard-edit.css"],
    "public/mixassets/css/user/dashboard-edit.min.css"
);

mix.minify(
    ["public/assets/css/user/notifs.css"],
    "public/mixassets/css/user/notifs.min.css"
);

mix.minify(
    ["public/assets/css/user/profile.css"],
    "public/mixassets/css/user/profile.min.css"
);
mix.minify(
    [
        "public/assets/css/advertise/index.css",
        "public/assets/css/video/suggest-video.css",
        "public/assets/css/item/follow.css",
        "public/assets/css/item/price.css",
        "public/assets/css/item/top-users.css",
        "public/assets/css/item/pages-suggest.css",
        "public/assets/css/item/gallery.css",
        "public/assets/css/bslider/index.css",
        "public/assets/css/tabs/top-tab.css",
        "public/assets/css/affilate/show-box.css",
        "public/assets/css/gallery/show.css",
        "public/assets/css/category/rcats.css",
        // "public/assets/css/pages/fifilter.css",
        "public/assets/css/pages/share-page.css",
        "public/assets/css/pages/hot-pages.css",

    ],
    "public/mixassets/css/advertise/index.min.css"
);

mix.minify(
    [
        "public/assets/css/forum/index.css",
        "public/assets/css/item/follow.css",
        "public/assets/css/tabs/top-tab.css",
        "public/assets/css/item/price.css",
        "public/assets/css/item/top-users.css",
        "public/assets/css/item/pages-suggest.css",
        "public/assets/css/item/gallery.css",
        "public/assets/css/bslider/index.css",
        "public/assets/css/video/suggest-video.css",
        "public/assets/css/affilate/show-box.css",
        "public/assets/css/gallery/show.css",
        "public/assets/css/category/rcats.css",
        // "public/assets/css/pages/fifilter.css",
        "public/assets/css/pages/share-page.css",
        "public/assets/css/pages/hot-pages.css",
    ],
    "public/mixassets/css/forum/index.min.css"
);

mix.minify(
    [
        "public/assets/css/rtable-create-edit.css",
        "public/assets/css/pages/comment-box.css",
        "public/assets/css/survey/create.css",
        "public/assets/css/pages/forum/sasf-edit.css"
    ],
    "public/mixassets/css/forum/create.min.css"
);

mix.minify(
    [
        "public/assets/css/category/comment/index.css",
        "public/assets/css/item/follow.css",
        "public/assets/css/tabs/top-tab.css",
        "public/assets/css/item/pages-suggest.css",
        "public/assets/css/item/price.css",
        "public/assets/css/item/top-users.css",
        "public/assets/css/item/gallery.css",
        "public/assets/css/bslider/index.css",
        "public/assets/css/video/suggest-video.css",
        "public/assets/css/open-image.css",
        "public/assets/css/affilate/show-box.css",
        "public/assets/css/gallery/show.css",
        "public/assets/css/pages/btns.css",
        "public/assets/css/pages/comment-box.css",
        "public/assets/css/pages/hot-pages.css",
        "public/assets/css/survey/create.css",
        "public/assets/css/survey/show.css",
        "public/assets/css/category/rcats.css",
        // "public/assets/css/pages/fifilter.css",
        "public/assets/css/pages/share-page.css",
        "public/assets/css/category/comment/uprof.css",
    ],
    "public/mixassets/css/category/comment/index.min.css"
);

mix.minify(
    [
        "public/assets/css/category/comment-create.css",
        "public/assets/css/pages/comment-box.css",
        "public/assets/css/survey/create.css"
    ],
    "public/mixassets/css/category/comment-create.min.css"
);

mix.minify(
    [
        "public/assets/css/blog/index.css",
        "public/assets/css/item/follow.css",
        "public/assets/css/tabs/top-tab.css",
        "public/assets/css/item/pages-suggest.css",
        "public/assets/css/item/price.css",
        "public/assets/css/item/top-users.css",
        "public/assets/css/item/gallery.css",
        "public/assets/css/bslider/index.css",
        "public/assets/css/category/rcats.css",
        // "public/assets/css/pages/fifilter.css"
    ],
    "public/mixassets/css/blog/index.min.css"
);

mix.minify(
    [
        "public/assets/css/blog/show.css",
        "public/assets/css/item/follow.css",
        "public/assets/css/tabs/top-tab.css",
        "public/assets/css/item/pages-suggest.css",
        "public/assets/css/item/price.css",
        "public/assets/css/item/top-users.css",
        "public/assets/css/bslider/index.css",
        "public/assets/css/video/suggest-video.css",
        "public/assets/css/forum/suggest-questions.css",
        "public/assets/css/affilate/show-box.css",
        "public/assets/css/gallery/show.css",
        "public/assets/css/category/rcats.css",
        "public/assets/css/pages/share-page.css",
        "public/assets/css/pages/hot-pages.css",
        "public/assets/css/category/comment/uprof.css",
    ],
    "public/mixassets/css/blog/show.min.css"
);

mix.minify(
    [
        "public/assets/css/blog/create-edit.css",
        "public/assets/css/pages/comment-box.css"
    ],
    "public/mixassets/css/blog/create-edit.min.css"
);

mix.minify(
    [
        "public/assets/css/forum/show.css",
        "public/assets/css/item/follow.css",
        "public/assets/css/tabs/top-tab.css",
        "public/assets/css/item/price.css",
        "public/assets/css/item/top-users.css",
        "public/assets/css/item/pages-suggest.css",
        "public/assets/css/bslider/index.css",
        "public/assets/css/affilate/show-box.css",
        "public/assets/css/gallery/show.css",
        "public/assets/css/pages/btns.css",
        "public/assets/css/pages/comment-box.css",
        "public/assets/css/survey/show.css",
        "public/assets/css/category/rcats.css",
        "public/assets/css/pages/share-page.css",
        "public/assets/css/pages/hot-pages.css",
        "public/assets/css/category/comment/uprof.css",
        "public/assets/css/item/top-users.css"
    ],
    "public/mixassets/css/forum/show.min.css"
);

mix.minify(
    ["public/assets/css/pages/comment-box.css"],
    "public/mixassets/css/forum/edit-answer-admin.min.css"
);

mix.minify(
    ["public/assets/css/pages/comment-box.css"],
    "public/mixassets/css/forum/answers-admin.min.css"
);

mix.minify(
    [
        "public/assets/css/advertise/show.css",
        "public/assets/css/item/follow.css",
        "public/assets/css/tabs/top-tab.css",
        "public/assets/css/item/price.css",
        "public/assets/css/item/pages-suggest.css",
        "public/assets/css/bslider/index.css"
    ],
    "public/mixassets/css/advertise/show.min.css"
);

mix.minify(
    ["public/assets/css/advertise/edit.css"],
    "public/mixassets/css/advertise/edit.min.css"
);
mix.minify(
    ["public/assets/css/advertise/create.css"],
    "public/mixassets/css/advertise/create.min.css"
);

mix.minify(
    [
        "public/assets/css/affilate/show.css",
        "public/assets/css/item/follow.css",
        "public/assets/css/tabs/top-tab.css",
        "public/assets/css/item/pages-suggest.css",
        "public/assets/css/item/top-users.css",
        "public/assets/css/bslider/index.css",
        "public/assets/css/affilate/show-box.css",
        "public/assets/css/gallery/show.css",
        "public/assets/css/category/rcats.css",
        "public/assets/css/pages/share-page.css",
        "public/assets/css/pages/comment-box.css",
        "public/assets/css/category/comment/uprof.css",
    ],
    "public/mixassets/css/affilate/show.min.css"
);

mix.minify(
    [
        "public/assets/css/affilate/comment-create.css",
        "public/assets/css/pages/comment-box.css"
    ],
    "public/mixassets/css/affilate/comment-create.min.css"
);