<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VesselsController;
use App\Http\Controllers\FeeCategoryController;
use App\Http\Controllers\FixedFeeController;
use App\Http\Controllers\VesselInvoicesController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TariffCategoryController;
use App\Http\Controllers\PricingRuleController;
use App\Http\Controllers\ConditionTypeController;

// اختبارات الترجمة
// use Illuminate\Support\Facades\App;
// $locale = 'ar';
// if (! in_array($locale, ['ar', 'en', 'es', 'fr'])) {
//     abort(400);
// }
// App::setLocale($locale);

// صفحة تأكيد كلمة المرور
Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
    ->middleware(['auth'])
    ->name('password.confirm');
// معالجة التأكيد
Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
    ->middleware(['auth']);
// مسار صفحة التحقق من المصادقة الثنائية
Route::get('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'create'])
    ->middleware(['auth'])
    ->name('two-factor.login');
// معالجة التحقق من المصادقة الثنائية
Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
    ->middleware(['auth']);







// صفحة الترحيب
Route::get('/', function () {
    return view('welcome');
});






// مجموعة المسارات التي تتطلب تسجيل الدخول
Route::middleware('auth')->group(function () {
    // مسارات خاصة بكل اليوزرات
    Route::middleware([RoleMiddleware::class . ':admin,editor,contributor,guest'])->group(function () {
        // ملف البروفايل
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // -------------------------------------------------------------------

    // مسارات خاصة بكل بالأدمن والمحرر والمساهم
    Route::middleware([RoleMiddleware::class . ':admin,editor,contributor'])->group(function () {
        // مسار الداش بورد
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // مسارات تقرير السفينة
        Route::get('/pdf/vesselReport/{id}/{clickOption?}', [VesselsController::class, 'generateSailingReportPdf'])
            ->name('pdf.vesselReport.vessel_report');

        // مسارات توليد فاتورة بروفورما
        Route::get('/pdf/proformaInvoice/{id}/{clickOption?}', [VesselsController::class, 'generateProformaInvoicePdf'])
            ->name('pdf.proformaInvoice.proforma_invoice');

        // مسارات السفن
        Route::resource('vessels', VesselsController::class);

        // مسارات الفواتير 
        Route::resource('invoices', InvoiceController::class);

        // مسار فواتير سفينة محددة
        Route::resource('vessels.invoices', VesselInvoicesController::class);

        // مسار الفواتير عامة
        // Route::resource('invoices', InvoiceController::class);

        // مسار العملاء
        Route::resource('clients', ClientController::class);




        // طرق الخدمات
        Route::resource('services', ServiceController::class);
        Route::patch('services/{service}/toggle-active', [ServiceController::class, 'toggleActive'])->name('services.toggle-active');

        // طرق فئات التعريفة
        Route::resource('tariff-categories', TariffCategoryController::class);
        Route::patch('tariff-categories/{tariffCategory}/toggle-active', [TariffCategoryController::class, 'toggleActive'])->name('tariff-categories.toggle-active');

        // طرق قواعد التسعير
        Route::resource('pricing-rules', PricingRuleController::class);
        Route::patch('pricing-rules/{pricingRule}/toggle-active', [PricingRuleController::class, 'toggleActive'])->name('pricing-rules.toggle-active');
        Route::post('pricing-rules/{pricingRule}/test', [PricingRuleController::class, 'testRule'])->name('pricing-rules.test');

        // طرق أنواع الشروط
        Route::resource('condition-types', ConditionTypeController::class);
        Route::patch('condition-types/{conditionType}/toggle-active', [ConditionTypeController::class, 'toggleActive'])->name('condition-types.toggle-active');

        // طرق إضافية للفواتير
        Route::get('vessels/{vessel}/applicable-rules', [PricingRuleController::class, 'getApplicableRules'])->name('vessels.applicable-rules');
        // إضافة طرق لاختبار قواعد التسعير والحصول على القواعد المطبقة لسفينة معينة
        // Route::get('/test-pricing-rules', [VesselInvoicesController::class, 'testPricingRules']);
        // Route::get('/vessels/{vessel}/applicable-rules', [VesselInvoicesController::class, 'getApplicableRulesForVessel']);
    });



    // -------------------------------------------------------------------

    // مسارات خاصة بكل بالأدمن والمحرر
    Route::middleware([RoleMiddleware::class . ':admin,editor'])->group(function () {
        // مسارات فئات الرسوم
        Route::resource('fee_categories', FeeCategoryController::class);

        // مسارات الرسوم الثابتة
        Route::resource('fixed_fees', FixedFeeController::class);

        // مسار تفاصيل الرسوم الثابتة
        Route::get('/fixed_fees/{id}', [FixedFeeController::class, 'getFeeDetails']);
    });

    // -------------------------------------------------------------------

    // مسارات خاصة بكل بالأدمن
    Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
        // مجموعة المسارات التي تتطلب المصادقة وصلاحيات الادمن
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('users', UserController::class);
        });
    });


    // -------------------------------------------------------------------
});

require __DIR__ . '/auth.php';
