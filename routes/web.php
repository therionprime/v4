<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\JetStreamController;
use App\Http\Controllers\MetricsController;
use App\Http\Controllers\MobileConfigController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ShortcutController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
//Auth::routes();

require __DIR__.'/auth.php';

//Route::group(['prefix' => "{locale?}", "where" => ["locale" => "(en|jp)?"]], function () {

//    require "./localized.php";

//});

//require "./localized.php";

// Route::get('/themetest', [StaticPageController::class, 'getThemesPage']);

Route::get('/geoCountry', function () {
    return response()->json(geoCountry());
});

Route::get('/test', function () {
    return view('test');
});

Route::post('/locale', function () {
    $locale = request()->get('locale');
    if (isset($locale) && array_key_exists($locale, config('localization.supportedLocales'))) {
        session()->put('locale', $locale);
    }

    return back();
});

Route::get('/avatar/{value}/{size?}', [AvatarController::class, 'api']);

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->middleware('admin');

Route::get('/tl/{view}', [StaticPageController::class, 'template']);

Route::get('/tutorials/{view}', [StaticPageController::class, 'tutorial']);

// Route::get('/profile', [HomeController::class, 'index'])->name('profile');
// Route::post('/profile/color', [HomeController::class, 'color']);
// Route::post('/auth/toggleEditing', [HomeController::class, 'toggleEditing']);

Route::prefix('blog')->group(function () {
    Route::get('/', [PostsController::class, 'index'])->name('blog.listing');
    Route::get('/tag/{tag}', [PostsController::class, 'showTag'])->name('blog.tag');
    Route::get('/{slug}_{post}', [PostsController::class, 'show'])->name('blog.reader');
});

Route::prefix('plist')->group(function () {
    Route::get('{any}', [StaticPageController::class, 'plist'])->where('any', '.*');
});
// Route::get('/plist/{name}', [StaticPageController::class, 'plist']);

Route::prefix('user')->group(function () {
    Route::get('/settings', [UserController::class, 'getSettings']);
    Route::post('/settings', [UserController::class, 'postSettings']);
    Route::get('/notifications', [UserController::class, 'getNotifications']);
    Route::get('/badges', [UserController::class, 'getBadges']);
    Route::get('/password', [UserController::class, 'getPassword']);
    Route::post('/password', [UserController::class, 'postPassword'])->name('postPassword');
    Route::post('/update-password', [UserController::class, 'updatePassword'])
        ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
        ->name('update-password');
});

Route::prefix('image')->group(function () {
    Route::get('/', [ImageController::class, 'index']);
});

Route::get('/tutubox/cert', [StaticPageController::class, 'tutubox']);

Route::prefix('shortcuts')->middleware('tab:Shortcuts', 'back:Shortcuts')->group(function () {
    Route::get('/{tag?}', [ShortcutController::class, 'page'])->name('shortcuts');
});

Route::get('/altstore/burrito/apps.json', [AppController::class, 'burrito']);
Route::get('/altstore/apps.json', [AppController::class, 'showAltstoreJson']);

Route::prefix('providers')->middleware('tab:Providers', 'back:Providers')->group(function () {
    Route::get('/edit', [ProviderController::class, 'edit']);
    Route::post('/update', [ProviderController::class, 'update']);
    Route::post('/destroy/{provider}', [ProviderController::class, 'destroy']);
    // Route::get('/{name}', [ProviderController::class, 'status'])
});

Route::prefix('cashier')->group(function () {
    Route::get('/setup', [CashierController::class, 'setup']);
});

Route::get('/plist', [HomeController::class, 'getPlist']);
Route::post('/plist', [HomeController::class, 'postPlist']);

Route::get('/contact/index', [ContactController::class, 'view'])->middleware('tab:Contact');
Route::get('/contact/{type}', [ContactController::class, 'view'])->middleware('back:Contact');
Route::post('/contact/{type}', [ContactController::class, 'send']);
// Route::get('/contact/{type?}', function () {
//   return abort(500, 'Sorry for the inconvenience, but this page is under maintenance.');
// });
Route::any('/site.mobileconfig', [MobileConfigController::class, 'webapp']);

Route::get('/dashboard', [StaticPageController::class, 'getDashboard'])->middleware('auth');
Route::get('/install', [StaticPageController::class, 'chooseInstall']);
Route::get('/light', [StaticPageController::class, 'lightTheme']);
Route::get('/dark', [StaticPageController::class, 'darkTheme']);
Route::post('/theme', [StaticPageController::class, 'postTheme']);
// Route::get('/test', [StaticPageController::class, 'getTestPage']);
Route::get('/search', [AppController::class, 'getSearchPage'])
    ->middleware('tab:Search', 'back:Search')
    ->name('search');
Route::get('/credits', [StaticPageController::class, 'getCreditsPage']);
Route::get('/faq', [StaticPageController::class, 'getFaqPage']);
Route::get('/cydia', [StaticPageController::class, 'getCydiaPage']);
Route::get('/betas', [StaticPageController::class, 'getBetasPage']);
Route::get('/jailbreak', [StaticPageController::class, 'getJailbreakPage']);
Route::get('/aboutUs', [StaticPageController::class, 'getAboutUsPage']);
Route::get('/fight-for-net-neutrality', [StaticPageController::class, 'getFightForNetNeutrality']);
// Route::get('/shortcuts', [StaticPageController::class, 'getShortcutsPage']);

Route::post('/close_announcement', [StaticPageController::class, 'closeAnnouncement']);
Route::post('/payment/add-funds/paypal', [PaymentController::class, 'payWithPaypal'])->name('paywithpaypal');
Route::get('/payment/add-funds/paypal/status', [PaymentController::class, 'getPaymentStatus'])->name('ppStatus');
Route::post('/paypal-log', [PaymentController::class, 'logPayment']);
Route::get('/skin/{uuid}', [PaymentController::class, 'downloadSkin'])->name('skin');
Route::get('/skin/affiliate/{uuid}', [PaymentController::class, 'affiliateSkin'])->name('skin.ref');

Route::post('/add/iosgods/plist', [StaticPageController::class, 'addIGPlist']);

Route::get('/home', [HomeController::class, 'index'])->name('home');

//Route::get('/me', function () {
//    return Inertia::render('Welcome', [
//        'canLogin' => Route::has('login'),
//        'canRegister' => Route::has('register'),
//        'laravelVersion' => Application::VERSION,
//        'phpVersion' => PHP_VERSION,
//    ]);
//});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/me', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::put('/team/metrics', [MetricsController::class, 'handle'])
        ->name('team.metrics');
    Route::delete('/teams/{team}/profile-photo', [JetStreamController::class, 'destroyTeamPhoto'])
        ->name('current-team-photo.destroy');
});

//Route::inertia('/custom-reset-link', function () {})->name('custom-password-reset');
