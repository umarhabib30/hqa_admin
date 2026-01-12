<?php

use App\Http\Controllers\AchievementsController;
use App\Http\Controllers\alumniEventController;
use App\Http\Controllers\alumniFormController;
use App\Http\Controllers\alumniHustonController;
use App\Http\Controllers\alumniImageController;
use App\Http\Controllers\AlumniMailController;
use App\Http\Controllers\alumniPostController;
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
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PermissionController;
use App\Models\PtoLetterGuide;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('auth.login');
});
//  auth controller page 
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');

Route::get('/verify-otp', [AuthController::class, 'showOtp'])->name('otp.form');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.verify');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');


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

Route::resource('Booking', BookingController::class);

//Pto Events Pages Routes 
Route::resource('ptoEvents', PtoEventsController::class);
Route::resource('easy-joins', EasyJoinController::class);
Route::resource('fee', FeePersonPriceController::class);

//Pto  SubscribeMails Pages Routes 
Route::resource('ptoSubscribemails', PtoSubscribeMailController::class);

//pto multiple images page routes
Route::resource('ptoImages', PtoImagesController::class);

// pto letter guide downlaod Routes page 
Route::resource('ptoLetterGuide', PtoLetterGuideController::class);



//Alumni  Huston Pages Routes 
Route::resource('alumniHuston', alumniHustonController::class);

//Alumni  Events Pages Routes 
Route::resource('alumniEvent', alumniEventController::class);

//Alumni  Posts Pages Routes 
Route::resource('alumniPosts', alumniPostController::class);


//Alumni  image Pages Routes 
Route::resource('alumniImages', alumniImageController::class);

//Alumni  Form Pages Routes 
Route::resource('alumniForm', alumniFormController::class);
Route::resource('alumniMail', AlumniMailController::class);


//calender Routes
Route::resource('calender', CalendarController::class);


//Home page Modal Rpute

Route::resource('homeModal', HomeModalController::class);

// Home topAchievers Route
Route::resource('topAchievers', TopAchieverController::class);

//Home Memories Route
Route::resource('memories', HomeMemoriesController::class);


//Home News Section Route
Route::resource('news', NewsController::class);

// Home Video Section 
Route::resource('videos', VideoController::class);

// Home Social Section 

Route::resource('socials', SocialController::class)->except(['show']);




//career job post Route
Route::resource('jobPost', jobPostController::class);
Route::resource('jobApp', jobAppController::class);

// Managers Routes (with auth middleware)
Route::middleware(['auth'])->group(function () {
    Route::resource('managers', ManagerController::class);
    Route::get('/managers/{id}/reset-password', [ManagerController::class, 'showResetPassword'])->name('managers.reset-password');
    Route::put('/managers/{id}/reset-password', [ManagerController::class, 'resetPassword'])->name('managers.reset-password.update');
});

// Sponsor Packages Routes
Route::resource('sponsor-packages', SponsorPackageController::class);

// Coupons Routes
Route::resource('coupons', CouponController::class);
Route::get('/coupons/{id}/codes', [CouponController::class, 'showCodes'])->name('coupons.codes');

// Permissions Routes (Super Admin Only)
Route::middleware(['auth'])->group(function () {
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions/update-role', [PermissionController::class, 'updateRolePermissions'])->name('permissions.update-role');
});
