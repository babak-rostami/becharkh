<?php

use App\Http\Controllers\CategoryCommentController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SiteCategoryController;
use App\Http\Controllers\VideoController;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::name('api.')->group(function () {
    Route::get('get-comments-page/{category_id}/{item_id}/{tag_id}/{page}/{lastId}/{cri?}', [IndexController::class, 'getCommentsPaginatePage'])->name('get.comments.page');

    Route::get('get-user-videos/{user_id}/{id?}/{forr?}', [VideoController::class, 'getUserVideos'])->name('get.user.videos');

    Route::get('last-videos/{b_slug?}/{m_slug?}', [VideoController::class, 'getLastVideos']);

    Route::get('get-cat-fis', [SiteCategoryController::class, 'getCategoryFeaturesItems'])->name('get.cat.fis');
    Route::get('get-cat-fis-ad', [SiteCategoryController::class, 'getCategoryFeaturesItemsForAd'])->name('get.cat.fis.for.ad');
});
