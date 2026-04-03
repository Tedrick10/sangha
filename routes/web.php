<?php

use App\Http\Controllers\Admin\CustomFieldController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ExamTypeController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\MonasteryController;
use App\Http\Controllers\Admin\MonasteryMessageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SanghaController;
use App\Http\Controllers\Admin\ScoreController;
use App\Http\Controllers\Admin\SiteImagesController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TranslationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WebsiteController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\MonasteryLoginController;
use App\Http\Controllers\Auth\StudentLoginController;
use App\Http\Controllers\Monastery\DashboardController as MonasteryDashboardController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\PassSanghaController;
use App\Http\Controllers\Public\RegistrationController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/preferences/theme', function (Request $request) {
    $theme = $request->input('theme', 'system');
    if (in_array($theme, ['light', 'dark', 'system'], true)) {
        sync_app_theme($theme);
    }
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json(['ok' => true, 'theme' => resolved_app_theme()]);
    }

    return redirect()->back();
})->name('app.set-theme');

Route::post('/preferences/locale', function (Request $request) {
    $code = $request->input('locale', 'en');
    if ($code === 'en' || Language::where('code', $code)->where('is_active', true)->exists()) {
        sync_app_locale($code);
        app()->setLocale($code);
    }
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json(['ok' => true, 'locale' => app()->getLocale()]);
    }

    return redirect()->back();
})->name('app.set-locale');

// Admin login (guest only)
Route::middleware('admin.locale')->group(function () {
    Route::get('admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('admin/login', [AdminLoginController::class, 'login']);
});

// Admin panel - must be before website catch-all /{slug}
Route::prefix('admin')->name('admin.')->middleware(['admin.locale', 'admin.auth'])->group(function () {
    Route::bind('user', fn (string $value) => User::findOrFail($value));
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('monasteries', MonasteryController::class);
    Route::get('monastery-requests', [MonasteryMessageController::class, 'index'])->name('monastery-requests.index');
    Route::get('monastery-requests/{monastery}', [MonasteryMessageController::class, 'show'])->name('monastery-requests.show');
    Route::post('monastery-requests/{monastery}/reply', [MonasteryMessageController::class, 'reply'])->name('monastery-requests.reply');
    Route::get('sanghas/{sangha}/exams/{exam}', [SanghaController::class, 'examScores'])->name('sanghas.exam-scores');
    Route::resource('sanghas', SanghaController::class);
    Route::resource('custom-fields', CustomFieldController::class);
    Route::post('custom-fields/reorder', [CustomFieldController::class, 'reorder'])->name('custom-fields.reorder');
    Route::resource('subjects', SubjectController::class);
    Route::resource('scores', ScoreController::class);
    Route::post('scores/generate-pass-list', [ScoreController::class, 'generatePassList'])->name('scores.generate-pass-list');
    Route::post('scores/{score}/decision', [ScoreController::class, 'updateDecision'])->name('scores.decision');
    Route::post('scores/top20/reorder', [ScoreController::class, 'reorderTop20'])->name('scores.top20.reorder');
    Route::resource('exam-types', ExamTypeController::class);
    Route::resource('exams', ExamController::class);
    Route::get('websites', [WebsiteController::class, 'index'])->name('websites.index');
    Route::get('websites/{website}/edit', [WebsiteController::class, 'edit'])->name('websites.edit');
    Route::put('websites/{website}', [WebsiteController::class, 'update'])->name('websites.update');
    Route::resource('languages', LanguageController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::get('languages/{language}/translations', [TranslationController::class, 'edit'])->name('translations.edit');
    Route::put('languages/{language}/translations', [TranslationController::class, 'update'])->name('translations.update');
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('site-images', [SiteImagesController::class, 'edit'])->name('site-images.edit');
    Route::put('site-images', [SiteImagesController::class, 'update'])->name('site-images.update');
    Route::delete('site-images/{key}', [SiteImagesController::class, 'destroy'])->name('site-images.destroy');
    Route::post('logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('admin.login');
    })->name('logout');
});

// Monastery login & dashboard
Route::get('monastery/login', [MonasteryLoginController::class, 'showLoginForm'])->name('monastery.login');
Route::post('monastery/login', [MonasteryLoginController::class, 'login']);
Route::post('monastery/logout', [MonasteryLoginController::class, 'logout'])->name('monastery.logout');
Route::middleware(['website.locale', 'auth:monastery'])->prefix('monastery')->name('monastery.')->group(function () {
    Route::get('/', MonasteryDashboardController::class)->name('dashboard');
    Route::post('/sanghas', [MonasteryDashboardController::class, 'storeSangha'])->name('sanghas.store');
    Route::post('/messages', [MonasteryDashboardController::class, 'storeMessage'])->name('messages.store');
});

// Sangha login & dashboard
Route::get('sangha/login', [StudentLoginController::class, 'showLoginForm'])->name('sangha.login');
Route::post('sangha/login', [StudentLoginController::class, 'login']);
Route::post('sangha/logout', [StudentLoginController::class, 'logout'])->name('sangha.logout');
Route::middleware('auth:student')->prefix('sangha')->name('sangha.')->group(function () {
    Route::get('/', StudentDashboardController::class)->name('dashboard');
});

// Website (public) routes - /{slug} catch-all must be last
Route::middleware('website.locale')->group(function () {
    Route::get('/', [PageController::class, 'home'])->name('website.home');
    Route::get('/login', [PageController::class, 'login'])->name('website.login');
    Route::get('/register', [RegistrationController::class, 'show'])->name('website.register');
    Route::get('/register/monastery', function () {
        return redirect()->route('website.login', ['type' => 'monastery', 'mode' => 'register']);
    });
    Route::get('/pass-sanghas', [PassSanghaController::class, 'index'])->name('website.pass-sanghas');
    Route::post('/register/monastery', [RegistrationController::class, 'storeMonastery'])->name('website.register.monastery');
    Route::post('/register/sangha', [RegistrationController::class, 'storeSangha'])->name('website.register.sangha');
    Route::get('/{slug}', [PageController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('website.page');
});
