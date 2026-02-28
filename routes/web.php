<?php

use App\Http\Controllers\AchievementsController;
// use App\Http\Controllers\alumniEventController;
use App\Http\Controllers\alumniFormController;
use App\Http\Controllers\alumniHustonController;
use App\Http\Controllers\alumniImageController;
use App\Http\Controllers\AlumniMailController;
use App\Http\Controllers\alumniPostController;
use App\Http\Controllers\Api\GeneralDonationController;
use App\Http\Controllers\PtoEventAttendeeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationBookingController;
use App\Http\Controllers\DonationImageController;
use App\Http\Controllers\EasyJoinController;
use App\Http\Controllers\FeePersonPriceController;
use App\Http\Controllers\FundraiseController;
use App\Http\Controllers\HomeMemoriesController;
use App\Http\Controllers\HomeModalController;
use App\Http\Controllers\jobAppController;
use App\Http\Controllers\jobPostController;
use App\Http\Controllers\PtoEventsController;
use App\Http\Controllers\PtoImagesController;
use App\Http\Controllers\PtoLetterGuideController;
use App\Http\Controllers\PtoSubscribeMailController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\TopAchieverController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\SponsorPackageController;
use App\Http\Controllers\SponsorPackageSubscriberController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AlumniAuthController;
use App\Http\Controllers\AlumniEventsController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\AlumniFeePersonPriceController;
use App\Http\Controllers\AlumniPortalController;
use App\Http\Controllers\AlumniEventAttendeeController as AlumniEventAttendeeWebController;
use App\Http\Controllers\Api\AlumniEventAttendeeController;
use App\Models\PtoLetterGuide;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ContactSponserController;
use App\Http\Controllers\DonationAdminController;
use App\Http\Controllers\DynamicSubscriptionController;

Route::get('/link-storage', function () {
    Artisan::call('storage:link');
    return 'Storage link created successfully!';
});

Route::get('/', function () {
    return view('auth.login');
});
//  auth controller page 
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');

Route::get('/verify-otp', [AuthController::class, 'showOtp'])->name('otp.form');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.verify');

// Admin / staff forgot password (users table: admin, super_admin, manager)
Route::get('/password/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/forgot', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

// Alumni Portal (separate login & layout)
Route::prefix('alumni')->name('alumni.')->group(function () {
    Route::get('/login', [AlumniAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AlumniAuthController::class, 'login'])->name('login.store');
    Route::post('/logout', [AlumniAuthController::class, 'logout'])->name('logout');

    // Alumni forgot password
    Route::get('/password/forgot', [AlumniAuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/password/forgot', [AlumniAuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [AlumniAuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [AlumniAuthController::class, 'resetPassword'])->name('password.update');

    Route::middleware(['alumni.auth'])->group(function () {
        Route::get('/dashboard', [AlumniPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile/edit', [AlumniPortalController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [AlumniPortalController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [AlumniPortalController::class, 'updatePassword'])->name('profile.update-password');
    });
});

// Admin / dashboard routes – require login (redirect to login if not authenticated)
Route::middleware(['auth'])->group(function () {
    //dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    //Donation Achievements Page Routes
    Route::resource('achievements', AchievementsController::class);

    //Donation Fundraiser Page Routes
    Route::resource('fundRaise', FundraiseController::class);
    Route::resource('donationImage', DonationImageController::class);

    //Donation Booking Page Routes
    Route::resource('donationBooking', DonationBookingController::class);
    Route::post(
        '/donation-booking/{id}/book-seat',
        [DonationBookingController::class, 'bookSeat']
    )->name('donationBooking.bookSeat');
    Route::get(
        '/donation-booking/scan',
        [DonationBookingController::class, 'scanPage']
    )->name('donationBooking.scan');
    Route::get(
        '/donation-booking/check-in',
        [DonationBookingController::class, 'checkIn']
    )->name('donationBooking.checkIn');
    Route::get(
        '/donation-booking/{id}/export/pdf',
        [DonationBookingController::class, 'exportPdf']
    )->name('donationBooking.export.pdf');

    // Donation Admin Log Route
    Route::get('/admin/donations', [DonationAdminController::class, 'index'])->name('admin.donations.index');
    Route::get('/admin/donations/export/pdf', [DonationAdminController::class, 'exportPdf'])->name('admin.donations.export.pdf');
    Route::post('/admin/donations', [DonationAdminController::class, 'store'])->name('admin.donations.store');
    Route::get('/admin/donations/{donation}/edit', [DonationAdminController::class, 'edit'])->name('admin.donations.edit');
    Route::put('/admin/donations/{donation}', [DonationAdminController::class, 'update'])->name('admin.donations.update');
    Route::delete('/admin/donations/{donation}', [DonationAdminController::class, 'destroy'])->name('admin.donations.destroy');
    Route::get('/donations/{donation}', action: [DonationAdminController::class, 'show'])->name('admin.donations.show');

    //Pto Events Pages Routes
    Route::resource('ptoEvents', PtoEventsController::class);
    Route::resource('easy-joins', controller: EasyJoinController::class);
    Route::resource('fee', FeePersonPriceController::class);
    // PTO Event Attendees Admin Routes
    Route::prefix('admin')->group(function () {
        Route::get('/pto-event-attendees', [PtoEventAttendeeController::class, 'index'])
            ->name('admin.pto-event-attendees.index');
        Route::delete('/pto-event-attendees/{id}', [PtoEventAttendeeController::class, 'destroy'])
            ->name('admin.pto-event-attendees.destroy');
    });

    //Pto SubscribeMails Pages Routes
    Route::resource('ptoSubscribemails', PtoSubscribeMailController::class);
    Route::resource('ptoImages', PtoImagesController::class);
    Route::resource('ptoLetterGuide', PtoLetterGuideController::class);

    //Alumni Huston / Events / Posts / Images / Form / Mail Routes
    Route::resource('alumniHuston', alumniHustonController::class);
    Route::resource('alumniEvent', AlumniEventsController::class);
    Route::resource('alumniPosts', AlumniPostController::class);
    Route::resource('alumniImages', alumniImageController::class);
    Route::resource('alumniForm', alumniFormController::class);
    Route::resource('alumniMail', AlumniMailController::class);

    //Calendar, Home Modal, Top Achievers, Memories, News, Videos, Socials
    Route::resource('calender', CalendarController::class);
    Route::resource('homeModal', HomeModalController::class);
    Route::resource('topAchievers', TopAchieverController::class);
    Route::resource('memories', HomeMemoriesController::class);
    Route::resource('news', NewsController::class);
    Route::resource('videos', VideoController::class);
    Route::resource('socials', SocialController::class)->except(['show']);

    //Career job post / applications
    Route::resource('jobPost', jobPostController::class);
    Route::resource('jobApp', jobAppController::class);

    // Managers Routes
    Route::resource('managers', ManagerController::class);
    Route::get('/managers/{id}/reset-password', [ManagerController::class, 'showResetPassword'])->name('managers.reset-password');
    Route::put('/managers/{id}/reset-password', [ManagerController::class, 'resetPassword'])->name('managers.reset-password.update');

    // Sponsor Packages Routes
    Route::resource('sponsor-packages', SponsorPackageController::class);
    Route::get('sponsor-package-subscribers/{subscriber}', [SponsorPackageSubscriberController::class, 'show'])
        ->name('sponsor-package-subscribers.show');

    // Coupons Routes
    Route::resource('coupons', CouponController::class);
    Route::get('/coupons/{id}/codes', [CouponController::class, 'showCodes'])->name('coupons.codes');
    Route::post('/coupon-codes/mark-copied', [CouponController::class, 'markAsCopied'])->name('coupon-codes.mark-copied');

    // Contact Sponser Routes
    Route::get('contact-sponser', [ContactSponserController::class, 'index'])->name('contact-sponser.index');
    Route::get('contact-sponser/{id}', [ContactSponserController::class, 'show'])->name('contact-sponser.show');
    Route::delete('contact-sponser/{id}', [ContactSponserController::class, 'destroy'])->name('contact-sponser.destroy');

    // Permissions Routes (Super Admin Only)
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/user/{user}', [PermissionController::class, 'editUser'])->name('permissions.edit-user');
    Route::put('/permissions/user/{user}', [PermissionController::class, 'updateUser'])->name('permissions.update-user');

    // Alumni Event Attendees Admin Routes
    Route::prefix('admin')->group(function () {
        Route::get('/alumni-event-attendees', [AlumniEventAttendeeWebController::class, 'index'])
            ->name('admin.alumni-event-attendees.index');
        Route::delete('/alumni-event-attendees/{id}', [AlumniEventAttendeeWebController::class, 'destroy'])
            ->name('admin.alumni-event-attendees.destroy');
    });

    // Alumni Fee Person Price Routes
    Route::resource('alumniFee', AlumniFeePersonPriceController::class)->parameters([
        'alumniFee' => 'fee'
    ]);
});

// Public routes (no login required)
// Recurring and one-time donation (subscribe form + API)
Route::get('/subscribe', [GeneralDonationController::class, 'show'])->name('dynsub.show');
Route::post('/subscribe', [GeneralDonationController::class, 'recurringDonation'])->name('dynsub.store');
Route::post('one-time-donation', [GeneralDonationController::class, 'oneTimeDonation'])->name('one-time-donation');

// Alumni Event Attendees Frontend Routes (for alumni users / public registration)
Route::get('/alumni-event-attendees', [AlumniEventAttendeeController::class, 'index'])
    ->name('alumni-event-attendees.index');
Route::post('/alumni-event-attendees', [AlumniEventAttendeeController::class, 'store'])
    ->name('alumni-event-attendees.store');
Route::post('/alumni-event-intent', [AlumniEventAttendeeController::class, 'createIntent'])
    ->name('alumni-event-attendees.intent');
