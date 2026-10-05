<?php

use App\Http\Controllers\Admin\ElasticsearchController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdvertiseController;
use App\Http\Controllers\AdvertiseReportController;
use App\Http\Controllers\AdvertiseVideoController;
use App\Http\Controllers\AffilateController;
use App\Http\Controllers\BabakController;
use App\Http\Controllers\BlogCommentController;
use App\Http\Controllers\BlogCommentLikeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogLikeController;
use App\Http\Controllers\BlogVideoController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CategoryCommentController;
use App\Http\Controllers\CategoryCommentLikeController;
use App\Http\Controllers\CategoryFeatureController;
use App\Http\Controllers\CategoryFeatureItemController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\EditorImageController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\InputImagesController;
use App\Http\Controllers\ItemImageController;
use App\Http\Controllers\MigrateToMongoController;
use App\Http\Controllers\ModelDatailController;
use App\Http\Controllers\MongoItemTagController;
use App\Http\Controllers\MongoItemTelNumberController;
use App\Http\Controllers\PageErrorController;
use App\Http\Controllers\ProductCommentController;
use App\Http\Controllers\ProductCommentLikeController;
use App\Http\Controllers\QuestionAnswerController;
use App\Http\Controllers\QuestionAnswerLikeController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionEmailController;
use App\Http\Controllers\QuestionLikeController;
use App\Http\Controllers\QuestionVideoController;
use App\Http\Controllers\SiteCategoryController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SuggestPageController;
use App\Http\Controllers\SuggestProductController;
use App\Http\Controllers\SurveyOptionController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserMessageController;
use App\Http\Controllers\UserNotificationController;
use App\Http\Controllers\UserOrderController;
use App\Http\Controllers\UserPasswordController;
use App\Http\Controllers\UserSearchController;
use App\Http\Controllers\UserWorkController;
use App\Http\Controllers\VideoCommentController;
use App\Http\Controllers\VideoCommentLikeController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\VideoLikeController;
use App\Http\Controllers\WebScraperController;
use App\Http\Controllers\WorkController;
use App\Models\MongoBlog;
use App\Models\MongoQuestion;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('elastic-init', [ElasticsearchController::class, 'initial']);

Route::get('/getCities', [IndexController::class, 'getCities'])->name('get.cities');
Route::get('/getCitiesCreate', [IndexController::class, 'getCitiesCreate'])->name('get.cities.create');
// Route::get('/get-cats/{pid?}', [SiteCategoryController::class, 'getCats'])->name('get.cats');
// Route::get('/index-back-cats/{id?}', [SiteCategoryController::class, 'adIndexBackCats'])->name('ad.index.back.cats');
Route::get('/get-ostans', [SiteCategoryController::class, 'getOstans'])->name('get.ostans.ad.index');
Route::get('/get-cities/{ostan_id}', [SiteCategoryController::class, 'getCities'])->name('get.cities.ad.index');
Route::get('/get-feature-children/{iid}/{fid}', [SiteCategoryController::class, 'getFeatureChildren'])->name('get.feature.children');
Route::get('/get-feature-filter-ad-children/{iid}/{fid}', [SiteCategoryController::class, 'getFeatureFilterAdChildren'])->name('get.feature.filter.adchildren');
Route::get('/parent-feature/{pid}', [SiteCategoryController::class, 'getParentFeature'])->name('get.parent.feature');

Route::group(['middleware' => 'throttle:35,1'], function () {
    Route::get('/', [IndexController::class, 'home'])->name('home');

    Route::get('/ads/{category_slug?}', [AdvertiseController::class, 'getAds'])->name('ads.index');
    Route::get('/ads/{category_slug}/{ad_slug}', function ($category_slug) {
        return redirect(route('ads.index', $category_slug));
    });
    Route::get('ads/{slug}', [AdvertiseController::class, 'show'])->name('ad.show');

    // Route::get('market/{category_slug?}', [AdvertiseController::class, 'getAds'])->name('ads.index');
    // Route::get('market-item/{slug}', [AdvertiseController::class, 'show'])->name('ad.show');

    //------------------------------------------admin routes----------------------------
    Route::get('/admin/login_admin', [AdminController::class, 'login'])->name('admin.login');
    Route::post('/admin/login_admin', [AdminController::class, 'loginSend'])->name('admin.login.send');
});

Route::prefix('admin')->middleware('admin')->group(function () {

    Route::get('clear-hot-items', [MigrateToMongoController::class, 'clearHotItems'])->name('admin.clear.hot.items');
    Route::get('scrap-new-mobiles', [WebScraperController::class, 'mobiles'])->name('admin.scrap.new.mobiles');

    Route::get('logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('events', [AdminController::class, 'events'])->name('event.all');
    Route::get('event/delete/{notif}', [AdminController::class, 'deleteNotification'])->name('admin.event.delete');

    Route::get('user-notifs', [UserNotificationController::class, 'adminUserNotifs'])->name('admin.user.notifs');
    Route::get('user-notif-destroy/{id}', [UserNotificationController::class, 'adminUserNotifDestroy'])->name('admin.user.notif.destroy');

    Route::get('users/{type?}', [AdminController::class, 'users'])->name('admin.users');
    Route::post('user-update/{id}', [UserController::class, 'updateAdmin'])->name('user.update.admin');
    Route::get('change-username-reqs', [UserController::class, 'changeUserNameReqs'])->name('admin.change.username.reqs');
    Route::delete('destroy-chun-reqs', [UserController::class, 'DestroyChunReqs'])->name('admin.destroy.chun.reqs');

    Route::get('car-detail/create/{model_id}', [ModelDatailController::class, 'create'])->name('car.detail.create');
    Route::post('car-detail/store/{model_id}', [ModelDatailController::class, 'store'])->name('car.detail.store');
    Route::get('car-detail/edit/{model_id}/{detail_id}', [ModelDatailController::class, 'edit'])->name('car.detail.edit');
    Route::put('car-detail/update/{model_id}/{detail_id}', [ModelDatailController::class, 'update'])->name('car.detail.update');
    Route::get('car-detail/all/{model_id}', [ModelDatailController::class, 'all'])->name('car.detail.all');
    Route::get('car-detail/delete/{detail_id}', [ModelDatailController::class, 'delete'])->name('car.detail.delete');
    Route::post('car-detail/priority', [ModelDatailController::class, 'priority'])->name('car.detail.priority');

    Route::get('advertise/all/{cat_slug?}', [AdvertiseController::class, 'adminAll'])->name('admin.advertise.all');
    Route::put('ad/update/{id}', [AdvertiseController::class, 'update'])->name('ad.update.admin');
    Route::get('ad/edit/{id}', [AdvertiseController::class, 'edit'])->name('ad.edit.admin');

    Route::get('accept-ad-video/{id}', [AdvertiseVideoController::class, 'acceptVideo'])->name('accept.advertise.video');
    Route::get('reject-ad-video/{id}', [AdvertiseVideoController::class, 'rejectVideo'])->name('reject.advertise.video');

    Route::get('advertise/reports', [AdvertiseReportController::class, 'all'])->name('advertise.report.all');
    Route::get('advertise-report/destroy/{id}', [AdvertiseReportController::class, 'destroy'])->name('advertise.report.destroy');

    Route::get('blogs', [BlogController::class, 'all'])->name('blog.all');
    Route::get('blog/create', [BlogController::class, 'create'])->name('blog.create');
    Route::post('blog/store/{status}/{exit}', [BlogController::class, 'store'])->name('blog.store');
    Route::delete('blog/destroy/{id}', [BlogController::class, 'destroy'])->name('blog.destroy');
    Route::get('blog/comments', [BlogCommentController::class, 'all'])->name('blog.comment.all');
    Route::post('blog-comment-store', [BlogCommentController::class, 'storeAdmin'])->name('admin.blog.comment.store');

    Route::get('new-post/{post_id?}', [BlogController::class, 'UserNewPost'])->name('user.new.post');
    Route::get('edit-post/{id}', [BlogController::class, 'UserEditPost'])->name('user.edit.post');
    Route::put('update-post/{id}', [BlogController::class, 'UserUpdatePost'])->name('user.update.post');
    Route::post('add-post', [BlogController::class, 'UserStorePost'])->name('user.store.post');
    Route::post('temp-store-post', [BlogController::class, 'UserStorePostTemprory'])->name('user.store.post.temprory');

    Route::get('accept-blog-video/{id}', [BlogVideoController::class, 'acceptVideo'])->name('accept.blog.video');
    Route::get('reject-blog-video/{id}', [BlogVideoController::class, 'rejectVideo'])->name('reject.blog.video');

    Route::get('contacts', [ContactUsController::class, 'lists'])->name('contact.list');


    Route::get('questions/{cat_slug?}', [QuestionController::class, 'indexAdmin'])->name('question.index.admin');
    Route::get('question-answers/{question_id}', [QuestionAnswerController::class, 'questionAnswersAdmin'])->name('question.answers.admin');
    Route::get('question/categories', [QuestionController::class, 'categories'])->name('question.category.admin');
    Route::post('question/category/store', [QuestionController::class, 'categoryStore'])->name('question.category.store');
    Route::delete('question/category/destroy/{category}', [QuestionController::class, 'categoryDestroy'])->name('question.category.destroy');
    Route::put('question/category/update/{category}', [QuestionController::class, 'categoryUpdate'])->name('question.category.update');

    Route::delete('question-destroy/{question}', [QuestionController::class, 'questionDestroy'])->name('question.destroy');
    Route::put('question/update/{question}', [QuestionController::class, 'questionUpdate'])->name('question.update');
    Route::get('new-question', [QuestionController::class, 'createForAdmin'])->name('admin.question.create');
    Route::post('question-store', [QuestionController::class, 'storeForAdmin'])->name('admin.question.store');
    Route::get('edit-question/{question_id}', [QuestionController::class, 'editAdmin'])->name('admin.question.edit');
    Route::put('update-question/{question_id}', [QuestionController::class, 'updateAdmin'])->name('admin.question.update');
    Route::get('question-email/{question_id}', [QuestionEmailController::class, 'index'])->name('admin.question.email');

    Route::post('question-answer-store', [QuestionAnswerController::class, 'storeAdmin'])->name('admin.question.answer.store');
    Route::get('question-answer-edit/{answer_id}', [QuestionAnswerController::class, 'questionAnswerEditAdmin'])->name('admin.question.answer.edit');
    Route::put('question-answer-update/{answer_id}', [QuestionAnswerController::class, 'questionAnswerUpdateAdmin'])->name('admin.question.answer.update');
    Route::delete('question-answer-destroy/{answer_id}', [QuestionAnswerController::class, 'questionAnswerDestroyAdmin'])->name('admin.question.answer.destroy');

    Route::get('get-cat-eusers', [QuestionEmailController::class, 'getCategoryUsers'])->name('admin.get.cat.eusers');
    Route::get('get-item-eusers', [QuestionEmailController::class, 'getItemegoryUsers'])->name('admin.get.item.eusers');
    Route::post('send-question-email', [QuestionEmailController::class, 'sendQuestionEmail'])->name('admin.send.question.email');

    Route::get('accept-question-video/{id}', [QuestionVideoController::class, 'acceptVideo'])->name('accept.question.video');
    Route::get('reject-question-video/{id}', [QuestionVideoController::class, 'rejectVideo'])->name('reject.question.video');

    Route::get('videos', [VideoController::class, 'adminIndex'])->name('admin.video.index');
    Route::get('video-destroy/{id}', [VideoController::class, 'adminDestroy'])->name('admin.destroy.video');
    Route::get('video-create', [VideoController::class, 'createAdmin'])->name('admin.create.video');
    Route::post('video-file-store', [VideoController::class, 'fileStoreAdmin'])->name('admin.video.file.store');
    Route::put('video-store', [VideoController::class, 'storeAdmin'])->name('admin.video.store');
    Route::get('video-edit/{id}', [VideoController::class, 'editAdmin'])->name('admin.edit.video');
    Route::put('video-update', [VideoController::class, 'updateAdmin'])->name('admin.video.update');

    Route::get('/orders', [UserOrderController::class, 'orders'])->name('orders');

    Route::get('car/forums', [AdminController::class, 'carForums'])->name('car.forums');

    Route::get('cats-items', [SiteCategoryController::class, 'catsItemsIndex'])->name('cats.items.admin');
    Route::get('category/{id?}', [SiteCategoryController::class, 'indexAdmin'])->name('site.category.admin');
    Route::post('category-store', [SiteCategoryController::class, 'storeAdmin'])->name('site.category.store.admin');
    Route::post('category-update/{id}', [SiteCategoryController::class, 'updateCategoryAdmin'])->name('site.category.update.admin');
    Route::get('get-category-children/{category_id}/{edit_id?}', [SiteCategoryController::class, 'getChildren'])->name('get.category.children.user');
    Route::delete('category-destroy/{id}', [SiteCategoryController::class, 'destroy'])->name('category.destroy');

    Route::get('category-features/{cat_id?}/{feature_id?}', [CategoryFeatureController::class, 'categoryFeaturesAdmin'])->name('category.features.admin');
    Route::get('category-features-create', [CategoryFeatureController::class, 'create'])->name('create.feature.admin');
    Route::post('category-feature-store', [CategoryFeatureController::class, 'storeFeatureAdmin'])->name('category.feature.store.admin');
    Route::get('category-features-edit/{id}', [CategoryFeatureController::class, 'edit'])->name('edit.feature.admin');
    Route::put('category-feature.update/{id}', [CategoryFeatureController::class, 'updateFeatureAdmin'])->name('category.feature.update.admin');
    Route::get('get-category-feature-children/{feature_id}/{edit_id?}', [CategoryFeatureController::class, 'getChildren'])->name('get.category.feature.children');

    Route::get('item-edit/{item_id}', [CategoryFeatureItemController::class, 'itemEditAdmin'])->name('item.edit.admin');
    Route::get('feature-items/{feature_id}', [CategoryFeatureItemController::class, 'getItemsAdmin'])->name('feature.items.admin');
    Route::get('item-images/{item_id}', [ItemImageController::class, 'index'])->name('item.images.admin');
    Route::post('item-image-store/{item_id}', [ItemImageController::class, 'store'])->name('item.image.store.admin');
    Route::put('item-image-update', [ItemImageController::class, 'update'])->name('item.image.update.admin');
    Route::post('feature-item-store', [CategoryFeatureItemController::class, 'storeItemAdmin'])->name('feature.item.store.admin');
    Route::put('feature-item-update/{item_id}', [CategoryFeatureItemController::class, 'updateItemAdmin'])->name('feature.item.update.admin');
    Route::get('feature-item-destroy/{item_id}', [CategoryFeatureItemController::class, 'destroyItemAdmin'])->name('feature.item.destroy.admin');
    Route::post('item-reset-suggests', [CategoryFeatureItemController::class, 'itemResetSuggests'])->name('item.reset.suggests.admin');

    Route::get('itel-numbers', [MongoItemTelNumberController::class, 'indexAdmin'])->name('item.tel.numbers.admin');
    Route::get('itel-numbers-destroy/{id}', [MongoItemTelNumberController::class, 'destroy'])->name('itel.numbers.destroy.admin');

    Route::get('item-tags/{id?}', [MongoItemTagController::class, 'index'])->name('item.tags.admin');
    Route::post('item-tag-store', [MongoItemTagController::class, 'store'])->name('item.tag.store.admin');
    Route::put('item-tag-update/{tag_id}', [MongoItemTagController::class, 'update'])->name('item.tag.update.admin');
    Route::delete('item-tag-destroy/{tag_id}', [MongoItemTagController::class, 'delete'])->name('item.tag.destroy.admin');
    Route::get('item-tag-edit/{tag_id}', [MongoItemTagController::class, 'edit'])->name('item.tag.edit.admin');

    Route::get('comment-item-tags/{comment_id}', [MongoItemTagController::class, 'commentTags'])->name('comment.item.tags.admin');
    Route::post('comment-item-tag-store', [MongoItemTagController::class, 'comTagStore'])->name('comment.item.tag.store.admin');
    Route::delete('comment-item-tag-delete', [MongoItemTagController::class, 'comTagDelete'])->name('comment.item.tag.delete.admin');

    Route::get('hot-items', [CategoryFeatureItemController::class, 'hotItemsAdmin'])->name('hot.items.admin');

    Route::get('affilates', [AffilateController::class, 'indexAdmin'])->name('affilate.index.admin');
    Route::get('affilate-create', [AffilateController::class, 'create'])->name('affilate.create.admin');
    Route::post('affilate-store', [AffilateController::class, 'store'])->name('affilate.store.admin');
    Route::get('affilate-edit/{id}', [AffilateController::class, 'edit'])->name('affilate.edit.admin');
    Route::put('affilate-update/{id}', [AffilateController::class, 'update'])->name('affilate.update.admin');
    Route::delete('affilate-destroy/{id}', [AffilateController::class, 'destroy'])->name('affilate.destroy.admin');

    Route::get('affilate-comments/{product_id}', [ProductCommentController::class, 'adminIndex'])->name('affilate.comments.admin');
    Route::get('affilate-comment-create/{product_id}', [ProductCommentController::class, 'createAdmin'])->name('admin.affilate.comment.create');
    Route::post('affilate-comment-store', [ProductCommentController::class, 'storeAdmin'])->name('admin.affilate.comment.store');
    Route::get('affilate-comment-edit/{comment_id}', [ProductCommentController::class, 'editAdmin'])->name('admin.affilate.comment.edit');
    Route::put('affilate-comment-update/{comment_id}', [ProductCommentController::class, 'updateAdmin'])->name('admin.affilate.comment.update');
    Route::delete('affilate-comment-delete', [ProductCommentController::class, 'deleteAdmin'])->name('admin.affilate.comment.delete');

    Route::get('affilate-plinks', [AffilateController::class, 'plinks'])->name('affilate.plinks.admin');
    Route::delete('affilate-delete-plink', [AffilateController::class, 'plinkDelete'])->name('affilate.delete.public.link.admin');
    Route::put('affilate-update-plink/{id}', [AffilateController::class, 'plinkUpdate'])->name('affilate.update.public.link.admin');
    Route::post('affilate-store-plink', [AffilateController::class, 'plinkStore'])->name('affilate.store.public.link.admin');

    Route::get('jobs', [WorkController::class, 'adminIndex'])->name('admin.job.index');
    Route::post('store-job', [WorkController::class, 'storeAdmin'])->name('job.store.admin');
    Route::put('update-job/{job_id}', [WorkController::class, 'updateAdmin'])->name('job.update.admin');

    Route::get('category-comments', [CategoryCommentController::class, 'adminIndex'])->name('admin.category.comment.index');
    Route::get('category-comment-create', [CategoryCommentController::class, 'createAdmin'])->name('admin.category.comment.create');
    Route::post('category-comment-store', [CategoryCommentController::class, 'storeAdmin'])->name('admin.category.comment.store');
    Route::get('category-comment-edit/{comment_id}', [CategoryCommentController::class, 'editAdmin'])->name('admin.category.comment.edit');
    Route::put('category-comment-update/{comment_id}', [CategoryCommentController::class, 'updateAdmin'])->name('admin.category.comment.update');
    Route::delete('category-comment-delete', [CategoryCommentController::class, 'deleteAdmin'])->name('admin.category.comment.delete');

    Route::get('user-searches', [UserSearchController::class, 'index'])->name('admin.user.searches');
    Route::get('destroy-user-search/{id}', [UserSearchController::class, 'delete'])->name('admin.destroy.user.search');

    Route::get('site-errors', [PageErrorController::class, 'index'])->name('admin.page.errors');
    Route::get('destroy-site-error/{id}', [PageErrorController::class, 'delete'])->name('admin.destroy.page.error');
    Route::get('destroy-site-errors', [PageErrorController::class, 'deleteAll'])->name('admin.destroy.page.errors');

    Route::get('is-fakeuser-exist', [UserController::class, 'isFakeuserExist'])->name('admin.is.fuser.exist');
});

// ---------------------------------------- login routes -------------------------------------

Route::group(['middleware' => 'throttle:35,1'], function () {
    Route::get('/login', [UserController::class, 'login'])->name('user.login');
    Route::post('/login/send', [UserController::class, 'loginSend'])->name('user.login.send');
    Route::post('/login-send', [UserController::class, 'loginSendAjax'])->name('user.login.send.ajax');
    Route::get('/register', [UserController::class, 'register'])->name('user.register');
    Route::post('/register/send', [UserController::class, 'registerSend'])->name('user.register.send');
    Route::post('/register-send', [UserController::class, 'registerSendAjax'])->name('user.register.send.ajax');
    Route::post('/check-if-email-exist', [UserController::class, 'checkIfEmailExist'])->name('check.if.email.exist');

    Route::post('user-forget-password', [UserPasswordController::class, 'forgetPassword'])->name('user.forget.password');
    Route::get('reset-password/{token}', [UserPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
    Route::post('reset-password', [UserPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');
    Route::get('active-user/{username}/{code}', [UserController::class, 'activeUser'])->name('active.user');

    Route::get('new-question/{category_slug?}', [QuestionController::class, 'create'])->name('question.create');

    Route::get('fifil-load-items', [CategoryFeatureController::class, 'fifilLoadItems'])->name('fifil.load.items');

    Route::post('item-tel-save-number', [MongoItemTelNumberController::class, 'saveNumber'])->name('item.tel.save.number');

    Route::get('/profile/{username}/{tab?}', [UserController::class, 'dashboard'])->name('user.dashboard');
});

Route::middleware(['user'])->group(function () {

    Route::get('dashboard/{tab?}', [UserController::class, 'dashboardEdit'])->name('user.dashboard.edit');
    Route::post('upload-user-image', [UserController::class, 'uploadUserImage'])->name('upload.user.image');

    Route::post('request-change-username', [UserController::class, 'requestChangeUsername'])->name('user.req.change.username');

    Route::get('your-packages', [UserController::class, 'yourPackages'])->name('user.packages');

    Route::get('logout', [UserController::class, 'logout'])->name('user.logout');
    Route::post('user-change-email', [UserController::class, 'userChangeEmail'])->name('user.change.email');

    Route::get('notifications', [UserController::class, 'notifications'])->name('user.notifications');
    Route::get('event/delete/{notif}', [AdminController::class, 'deleteNotification'])->name('event.delete');

    Route::put('user-update', [UserController::class, 'update'])->name('user.update');

    Route::get('actice-email', [UserController::class, 'activeEmail'])->name('actice.email');

    //advertise 
    Route::get('/new-ad/{cat_slug?}', [AdvertiseController::class, 'create'])->name('new.ad');
    Route::post('advertise-store', [AdvertiseController::class, 'store'])->name('ad.store');

    Route::get('click-cat-ad-create/{id}', [SiteCategoryController::class, 'clickCategoryCreateAd'])->name('ad.create.click.category');
    // Route::get('back-cats/{id}', [SiteCategoryController::class, 'backCats'])->name('ad.back.cat.click');

    Route::post('check-ad-status/{ad_id}', [AdvertiseController::class, 'checkAdStatus'])->name('check.ad.status');
    Route::post('pay-ad-acc/{advertise_id}', [AdvertiseController::class, 'payAdFromAcc'])->name('pay.ad.from.acc');
    Route::get('ad-edit/{id}', [AdvertiseController::class, 'edit'])->name('ad.edit');
    Route::put('ad-update/{id}', [AdvertiseController::class, 'update'])->name('ad.update');
    Route::get('delete-ad/{id}', [AdvertiseController::class, 'destroy'])->name('destroy.ad');

    Route::get('rocket-ad/{id}', [AdvertiseController::class, 'rocket'])->name('rocket.ad');

    // Route::get('click-cat-rtable-create/{id}', [SiteCategoryController::class, 'clickCategoryCreateRT'])->name('rt.create.click.category');

    Route::get('mylist', [UserController::class, 'myList'])->name('mylist');

    Route::post('charge-acc', [UserOrderController::class, 'chargeAccount'])->name('user.charge.account');

    Route::get('follow/{user_2}', [FollowController::class, 'follow'])->name('follow');
    Route::get('unfollow/{user_2}', [FollowController::class, 'unfollow'])->name('unfollow');

    Route::post('message-send/{chat_id}', [UserMessageController::class, 'send'])->name('user.message.send');
    Route::get('messages', [UserMessageController::class, 'messages'])->name('user.messages');
    Route::get('message/{message_id}', [UserMessageController::class, 'show'])->name('user.show.message');
    Route::get('start-chat/{user_id}', [UserMessageController::class, 'start'])->name('user.chat.start');

    Route::post('question-store', [QuestionController::class, 'store'])->name('question.store');
    Route::delete('question-destroy/{question}', [QuestionController::class, 'questionDestroy'])->name('user.question.destroy');
    Route::get('question-edit/{question_id}', [QuestionController::class, 'edit'])->name('question.edit');
    Route::put('question-update', [QuestionController::class, 'userQuestionUpdate'])->name('user.question.update');
    Route::post('question-ckeditor-upload/{id?}', [QuestionController::class, 'updateCkeditor'])->name('question.ckeditor.upload');

    Route::delete('destroy-post', [BlogController::class, 'UserdestroyPost'])->name('user.destroy.post');

    //add video to ...
    // Route::post('add-video-to-post', [BlogVideo2Controller::class, 'addVideoPost'])->name('add.video.post');
    Route::post('add-video-to-post', [BlogVideoController::class, 'addVideoPost'])->name('add.video.post');
    Route::post('add-video-to-ad', [AdvertiseVideoController::class, 'addVideoAdvertise'])->name('add.video.ad');
    Route::post('add-video-to-question', [QuestionVideoController::class, 'addVideoQuestion'])->name('add.video.question');

    Route::get('new-video/{cat_slug?}', [VideoController::class, 'create'])->name('user.new.video');
    Route::get('edit-video/{id}', [VideoController::class, 'edit'])->name('user.edit.video');
    Route::put('user-video-store', [VideoController::class, 'store'])->name('user.video.store');

    Route::post('video-like', [VideoLikeController::class, 'store'])->name('video.like');
    Route::post('video-comment/store', [VideoCommentController::class, 'store'])->name('video.comment.store');

    //if post id is null is create else for edit
    Route::post('video-file-store', [VideoController::class, 'fileStore'])->name('video.file.store');

    Route::post('blog-ckeditor-upload/{id?}', [BlogController::class, 'updateCkeditor'])->name('blog.ckeditor.upload');

    Route::post('/editor/upload-image', [BlogController::class, 'EditorUploadImage'])->name('editor-upload');

    Route::post('question-answer', [QuestionAnswerController::class, 'store'])->name('question.answer.store');
    Route::post('question-answer-store-dref', [QuestionAnswerController::class, 'storeWithoutRefresh'])->name('question.answer.store.dref');

    //ctegory comment
    Route::post('cat-comment-store', [CategoryCommentController::class, 'store'])->name('category.comment.store');
    Route::post('cat-comment-store-dref', [CategoryCommentController::class, 'storeWithoutRefresh'])->name('category.comment.store.dref');

    Route::post('input-images-store', [InputImagesController::class, 'store'])->name('input.images.store');
    Route::post('input-images-destroy', [InputImagesController::class, 'destroy'])->name('input.images.destroy');
    Route::post('input-images-update', [InputImagesController::class, 'update'])->name('input.images.update');

    //blog comment
    Route::post('blog-comment-store', [BlogCommentController::class, 'store'])->name('blog.comment.store');
    Route::put('blog-comment-update/{id}', [BlogCommentController::class, 'update'])->name('blog.comment.update');
    Route::delete('blog-comment-destroy/{id}', [BlogCommentController::class, 'destroy'])->name('blog.comment.destroy');

    //user jobs
    Route::post('user-job-store', [UserWorkController::class, 'store'])->name('user.job.store');
    Route::delete('user-job-destroy', [UserWorkController::class, 'destroy'])->name('user.job.destroy');
    Route::get('get-user-jobs', [UserWorkController::class, 'getUserJobs'])->name('get.user.jobs');

    Route::post('contactus-store', [ContactUsController::class, 'store'])->name('contactus.store');

    Route::get('favorite', [UserController::class, 'favoriteIndex'])->name('favorite.index');

    Route::post('product-comment-store', [ProductCommentController::class, 'store'])->name('product.comment.store');
});

Route::get('suggest-page-show', [SuggestPageController::class, 'show'])->name('suggestp.show');

Route::post('comment-editor-img/{page}', [EditorImageController::class, 'upload'])->name('comment.editor.img.uplaod');

Route::get('terms', [IndexController::class, 'terms'])->name('terms.create');
Route::get('about-us', [IndexController::class, 'aboutus'])->name('aboutus.create');
Route::get('contact-us', [ContactUsController::class, 'create'])->name('contactus.create');
Route::get('contactus/create', function () {
    return Redirect::to(route('contactus.create'), 301);
});

Route::get('charge-account-callback/{order_id}', [UserOrderController::class, 'chargeAccountCallback'])->name('charge.account.callback');
Route::get('callback', [UserOrderController::class, 'callback'])->name('callback');

// Route::get('getUser', [UserController::class, 'getuserapi']);
// Route::get('getUserImage/{question_id}', [UserController::class, 'getuserImageApi']);

Route::get('product/{slug}', [AffilateController::class, 'show'])->name('product.show');
Route::get('slink/{affilate_id}', [AffilateController::class, 'slink'])->name('slink');
Route::post('suggest-product', [SuggestProductController::class, 'suggestProduct'])->name('suggest.product');

Route::get('migrate', [TestController::class, 'start']);

Route::get('main-search/{type}/{value?}', [IndexController::class, 'mainSearch'])->name('main.search');
Route::get('search-category-for-create/{forr}/{value?}', [IndexController::class, 'searchCategoryForCreate']);

Route::get('get-category-children/{cat_id}/{for}', [SiteCategoryController::class, 'getCategoryChildren'])->name('get.category.children');

Route::get('get-category-children-create/{cat_id}/{for}', [SiteCategoryController::class, 'getCategoryChildrenCreate']);

Route::group(['middleware' => 'throttle:35,1'], function () {
    Route::post('download-yv-f', [VideoController::class, 'downloadFormat'])->name('download.yvideo.format');
    Route::get('download-yv/{f_id}', [VideoController::class, 'downloadYoutubeVideo'])->name('download.yv');

    Route::post('report-advertise', [AdvertiseReportController::class, 'store'])->name('report.advertise');

    Route::get('blogs/{slug?}', [BlogController::class, 'index'])->name('blog.index');
    Route::get('blogs/{category_slug}/{slug}/{random_id?}', [BlogController::class, 'show'])->name('blog.show');

    Route::get('blog/{username}/{slugrandom}', function ($username, $slugrandom) {
        $random_id = explode("-", $slugrandom);
        $random = $random_id[count($random_id) - 1];
        $blog = MongoBlog::where('random_id', $random)->first();
        if (isset($blog)) {
            return redirect()->route('blog.show', ['category_slug' => $blog->category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id]);
        } else {
            abort(404);
        }
    });

    Route::get('blog/{slug}', function ($slug) {
        $blog = MongoBlog::where('slug', $slug)->first();
        if (isset($blog)) {
            return redirect()->route('blog.show', ['category_slug' => $blog->category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id]);
        } else {
            abort(404);
        }
    });

    Route::get('forum/{category_slug?}', [QuestionController::class, 'index'])->name('question.index');
    Route::get('forum/{category}/{slug?}/{random?}', [QuestionController::class, 'show'])->name('question.show');
    Route::get('forum-sh/{id}', [QuestionController::class, 'showShortLink'])->name('question.show.shl');

    Route::get('question/{username}/{slug}', function ($slug) {
        $segments = explode('-', $slug);
        $lastSegment = array_pop($segments);
        $question = MongoQuestion::where('random_id', $lastSegment)->first();
        if (isset($question)) {
            return redirect()->route('question.show',  $question->slug2);
        } else {
            abort(404);
        }
    });

    Route::get('video/{category_slug}/{video_slug?}/{random_id?}', [VideoController::class, 'show'])->name('video.show');
    Route::get('video/embed-b/{category_slug}/{video_slug?}/{random_id?}', [VideoController::class, 'showEmbedb'])->name('video.embedb.show');
    // Route::get('video/embed/{category_slug}/{video_slug}/{random_id}', [VideoController::class, 'showEmbed'])->name('video.embed.show');

    //car page redirect
    Route::get('car-page/{brand_slug}/{model_slug?}', [CarController::class, 'carPage'])->name('car.page');

    Route::post('question-like', [QuestionLikeController::class, 'store'])->name('question.like');
    Route::post('question-answer-like', [QuestionAnswerLikeController::class, 'store'])->name('question.answer.like');

    Route::post('product-comment-like', [ProductCommentLikeController::class, 'store'])->name('product.comment.like');
    Route::post('blog-comment-like', [BlogCommentLikeController::class, 'store'])->name('blog.comment.like');
    Route::post('video-comment-like', [VideoCommentLikeController::class, 'store'])->name('video.comment.like');
    Route::post('category-comment-like', [CategoryCommentLikeController::class, 'store'])->name('category.comment.like');
    Route::post('surop-choose', [SurveyOptionController::class, 'choose'])->name('surop.choose');

    Route::post('blog-like', [BlogLikeController::class, 'store'])->name('blog.like');

    Route::get('cars/{brand?}/{model?}', function () {
        return redirect()->route('ads.index');
    });

    Route::get('/babakrostami', [BabakController::class, 'babak'])->name('babak');
});

// done
Route::get('sitemap.xml', [SitemapController::class, 'sitemap']);
Route::get('sitemap-static.xml', [SitemapController::class, 'statics']);
Route::get('sitemap-rcategory.xml', [SitemapController::class, 'rTableCategories']);

Route::get('sitemap2.xml', [SitemapController::class, 'sitemap2']);
Route::get('sitemap-question.xml', [SitemapController::class, 'questions']);

Route::get('sitemap3.xml', [SitemapController::class, 'sitemap3']);
Route::get('sitemap-ad-pages.xml', [SitemapController::class, 'adPages']);

Route::get('sitemap4.xml', [SitemapController::class, 'sitemap4']);
Route::get('sitemap-blog.xml', [SitemapController::class, 'blogs']);

Route::get('sitemap5.xml', [SitemapController::class, 'sitemap5']);
Route::get('sitemap-ccategory.xml', [SitemapController::class, 'commentCategories']);

Route::get('sitemap6.xml', [SitemapController::class, 'sitemap6']);
Route::get('sitemap-videos.xml', [SitemapController::class, 'videos']);

Route::get('sitemap7.xml', [SitemapController::class, 'sitemap7']);
Route::get('sitemap-products.xml', [SitemapController::class, 'products']);
