<?php

use App\Http\Controllers\Admin\AppearanceController;
use App\Http\Controllers\Admin\CleanPassController;
use App\Http\Controllers\Admin\CustomFieldController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ExamTypeController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\MandatoryScoreEntryController;
use App\Http\Controllers\Admin\MonasteryChatController as AdminMonasteryChatController;
use App\Http\Controllers\Admin\MonasteryController;
use App\Http\Controllers\Admin\MonasteryFormRequestController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
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
use App\Http\Controllers\Monastery\AccountController as MonasteryAccountController;
use App\Http\Controllers\Monastery\ChatController as MonasteryPortalChatController;
use App\Http\Controllers\Monastery\DashboardController as MonasteryDashboardController;
use App\Http\Controllers\Monastery\FormRequestController as MonasteryFormRequestPortalController;
use App\Http\Controllers\Monastery\NotificationController as MonasteryNotificationController;
use App\Http\Controllers\Public\ExamEligibleController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\PassSanghaController;
use App\Http\Controllers\Public\RegistrationController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Browsers often request /favicon.ico before HTML; redirect to project SVG (avoids default Laravel tab icon).
Route::get('/favicon.ico', function () {
    return redirect(asset('favicon.svg'), 302);
});

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
    $code = $request->input('locale', config('app.locale'));
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
Route::prefix('admin')->name('admin.')->middleware(['admin.locale', 'admin.auth', 'admin.permission'])->group(function () {
    Route::bind('user', fn (string $value) => User::findOrFail($value));
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('notifications/recent', [AdminNotificationController::class, 'recent'])->name('notifications.recent');
    Route::get('notifications/{notification}/go', [AdminNotificationController::class, 'go'])->name('notifications.go');
    Route::post('notifications/read-all', [AdminNotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::get('monasteries/{monastery}/chat', [AdminMonasteryChatController::class, 'show'])->name('monasteries.chat');
    Route::get('monasteries/{monastery}/chat/messages', [AdminMonasteryChatController::class, 'fetch'])->name('monasteries.chat.messages');
    Route::post('monasteries/{monastery}/chat/messages', [AdminMonasteryChatController::class, 'store'])->name('monasteries.chat.messages.store');
    Route::resource('monasteries', MonasteryController::class);
    Route::get('monastery-requests', [MonasteryFormRequestController::class, 'index'])->name('monastery-requests.index');
    Route::get('monastery-requests/{monasteryFormRequest}', [MonasteryFormRequestController::class, 'show'])->name('monastery-requests.show');
    Route::get('monastery-requests/{monasteryFormRequest}/file', [MonasteryFormRequestController::class, 'file'])->name('monastery-requests.file');
    Route::put('monastery-requests/{monasteryFormRequest}/status', [MonasteryFormRequestController::class, 'updateStatus'])->name('monastery-requests.update-status');
    Route::delete('monastery-requests/{monasteryFormRequest}', [MonasteryFormRequestController::class, 'destroy'])->name('monastery-requests.destroy');
    Route::get('sanghas/{sangha}/exams/{exam}', [SanghaController::class, 'examScores'])->name('sanghas.exam-scores');
    Route::post('sanghas/generate-eligible-list', [SanghaController::class, 'generateEligibleList'])->name('sanghas.generate-eligible-list');
    Route::resource('sanghas', SanghaController::class);
    Route::get('sanghas/{sangha}/custom-fields/{customField}/file', [SanghaController::class, 'customFieldFile'])->name('sanghas.custom-field-file');
    Route::resource('custom-fields', CustomFieldController::class);
    Route::post('custom-fields/reorder', [CustomFieldController::class, 'reorder'])->name('custom-fields.reorder');
    Route::resource('subjects', SubjectController::class);
    Route::get('mandatory-scores/year-options', [MandatoryScoreEntryController::class, 'yearOptions'])->name('mandatory-scores.year-options');
    Route::get('mandatory-scores/exam-options', [MandatoryScoreEntryController::class, 'examOptions'])->name('mandatory-scores.exam-options');
    Route::get('mandatory-scores/desk-options', [MandatoryScoreEntryController::class, 'deskOptions'])->name('mandatory-scores.desk-options');
    Route::post('mandatory-scores/grid-row', [MandatoryScoreEntryController::class, 'storeGridRow'])->name('mandatory-scores.grid-row');
    Route::get('mandatory-scores/grid', [MandatoryScoreEntryController::class, 'grid'])->name('mandatory-scores.grid');
    Route::get('score-moderation', [MandatoryScoreEntryController::class, 'moderation'])->name('score-moderation.index');
    Route::post('score-moderation/control', [MandatoryScoreEntryController::class, 'storeModerationControl'])->name('score-moderation.control');
    Route::post('mandatory-scores', [MandatoryScoreEntryController::class, 'store'])->name('mandatory-scores.store');
    Route::get('mandatory-scores', [MandatoryScoreEntryController::class, 'index'])->name('mandatory-scores.index');
    Route::get('clean-pass', [CleanPassController::class, 'index'])->name('clean-pass.index');
    Route::post('clean-pass/generate', [CleanPassController::class, 'generate'])->name('clean-pass.generate');
    Route::post('clean-pass/reorder', [CleanPassController::class, 'reorder'])->name('clean-pass.reorder');
    Route::post('scores/generate-pass-list', [ScoreController::class, 'generatePassList'])->name('scores.generate-pass-list');
    Route::post('scores/{score}/decision', [ScoreController::class, 'updateDecision'])->name('scores.decision');
    Route::post('scores/top20/reorder', [ScoreController::class, 'reorderTop20'])->name('scores.top20.reorder');
    Route::resource('exam-types', ExamTypeController::class);
    Route::patch('exams/{exam}/desk-number-prefix', [ExamController::class, 'updateDeskNumberPrefix'])->name('exams.desk-number-prefix');
    Route::post('exams/{exam}/generate-eligible-list', [ExamController::class, 'generateEligibleList'])->name('exams.generate-eligible-list');
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
    Route::get('appearance', [AppearanceController::class, 'edit'])->name('appearance.edit');
    Route::put('appearance', [AppearanceController::class, 'update'])->name('appearance.update');
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
    Route::get('/account', [MonasteryAccountController::class, 'edit'])->name('account.edit');
    Route::put('/account', [MonasteryAccountController::class, 'update'])->name('account.update');
    Route::get('/', MonasteryDashboardController::class)->name('dashboard');
    Route::get('chat/messages', [MonasteryPortalChatController::class, 'fetch'])->name('chat.messages');
    Route::post('chat/messages', [MonasteryPortalChatController::class, 'store'])->name('chat.messages.store');
    Route::get('notifications/recent', [MonasteryNotificationController::class, 'recent'])->name('notifications.recent');
    Route::get('notifications/{notification}/go', [MonasteryNotificationController::class, 'go'])->name('notifications.go');
    Route::post('notifications/read-all', [MonasteryNotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/sanghas', [MonasteryDashboardController::class, 'storeSangha'])->name('sanghas.store');
    Route::put('/sanghas/{sangha}', [MonasteryDashboardController::class, 'updateRejectedSangha'])->name('sanghas.update');
    Route::get('/sanghas/{sangha}/custom-fields/{customField}/file', [MonasteryDashboardController::class, 'customFieldFile'])->name('sanghas.custom-field-file');
    Route::post('/sanghas/submit-eligible', [MonasteryDashboardController::class, 'submitEligibleSanghas'])->name('sanghas.submit-eligible');
    Route::post('/messages', [MonasteryDashboardController::class, 'storeFormRequest'])->name('messages.store');
    Route::post('/exam-forms', [MonasteryDashboardController::class, 'storeExamFormSubmission'])->name('exam-forms.store');
    Route::get('/requests/{monasteryFormRequest}', [MonasteryFormRequestPortalController::class, 'show'])->name('requests.show');
    Route::get('/requests/{monasteryFormRequest}/file', [MonasteryFormRequestPortalController::class, 'file'])->name('requests.file');
});

// Sangha login & dashboard
Route::get('sangha/login', [StudentLoginController::class, 'showLoginForm'])->name('sangha.login');
Route::post('sangha/login', [StudentLoginController::class, 'login']);
Route::post('sangha/logout', [StudentLoginController::class, 'logout'])->name('sangha.logout');
Route::middleware(['website.locale', 'auth:student'])->prefix('sangha')->name('sangha.')->group(function () {
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
    Route::get('/exam-eligible', [ExamEligibleController::class, 'index'])->name('website.exam-eligible.index');
    Route::get('/exam-eligible/{exam}', [ExamEligibleController::class, 'show'])->name('website.exam-eligible.show');
    Route::post('/register/monastery', [RegistrationController::class, 'storeMonastery'])->name('website.register.monastery');
    Route::post('/register/sangha', [RegistrationController::class, 'storeSangha'])->name('website.register.sangha');
    Route::get('/{slug}', [PageController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('website.page');
});
