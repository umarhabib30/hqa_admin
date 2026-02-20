<?php

use App\Http\Controllers\AlumniEventAttendeeController;
use App\Http\Controllers\Api\AchievementsApiController;
use App\Http\Controllers\Api\AlumniEventsApiController;
use App\Http\Controllers\Api\AlumniFeePersonPriceApiController;
// use App\Http\Controllers\Api\AlumniEventApiController;
use App\Http\Controllers\Api\AlumniFormApiController;
use App\Http\Controllers\Api\AlumniHustonApiController;
use App\Http\Controllers\Api\AlumniImageApiController;
use App\Http\Controllers\Api\AlumniMailApiController;
use App\Http\Controllers\Api\AlumniPostApiController;
use App\Http\Controllers\Api\CalendarApiController;
use App\Http\Controllers\Api\DonationBookingApiController;
use App\Http\Controllers\Api\DonationImageApiController;
use App\Http\Controllers\Api\EasyJoinController;
use App\Http\Controllers\Api\FeePersonPriceApiController;
use App\Http\Controllers\Api\FundraiseApiController;
use App\Http\Controllers\Api\HomeMemoriesApiController;
use App\Http\Controllers\Api\HomeModalApiController;
use App\Http\Controllers\Api\JobPostApiController;
use App\Http\Controllers\Api\NewsApiController;
use App\Http\Controllers\Api\PtoEventsApiController;
use App\Http\Controllers\Api\PtoImagesApiController;
use App\Http\Controllers\Api\PtoLetterGuideApiController;
use App\Http\Controllers\Api\PtoSubscribeMailApiController;
use App\Http\Controllers\Api\SocialApiController;
use App\Http\Controllers\Api\TopAchieverApiController;
use App\Http\Controllers\Api\VideoApiController;
use App\Http\Controllers\Api\SponserPackageApiController;
use App\Http\Controllers\Api\DonationBookingApiController as DonationBookingCheckInController;
use App\Http\Controllers\Api\ApplyCouponController;
use App\Http\Controllers\Api\GeneralDonationController;
use App\Http\Controllers\Api\JobApplicationApiController;
use App\Http\Controllers\Api\PtoEventAttendeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactSponserController;
use App\Http\Controllers\Api\SponserApiSubscriber;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get('/hello', function () {
    return response()->json([
        'status' => true,
        'message' => 'Hello API is working 🎉'
    ]);
});


// Home Modal Api 
Route::get('/homeModal', [HomeModalApiController::class, 'index']);

// Home topachiever api 
Route::get('/topAchiever', [TopAchieverApiController::class, 'index']);

// Home memories api 
Route::get('/homeMemories', [HomeMemoriesApiController::class, 'index']);

// Home News Api 
Route::get('/news', [NewsApiController::class, 'index']);
Route::get('/news/{id}', [NewsApiController::class, 'show']);

// Home Video Api 
Route::get('/videos', [VideoApiController::class, 'index']);

// Home Social Api 
Route::get('/socials', [SocialApiController::class, 'index']);


//Donation 

// Achievements api
Route::get('/achievements', [AchievementsApiController::class, 'index']);

//Fundraise Goals
Route::get('/fundraises', [FundraiseApiController::class, 'index']);

//Donation Image
Route::get('/donationGalleries', [DonationImageApiController::class, 'index']);

//Donation Booking APi
Route::get('/donationBooking', [DonationBookingApiController::class, 'index']);
Route::post('/donationBooking/{id}/book', [DonationBookingApiController::class, 'bookSeat']);


//PTO API'S

// Events APi 
Route::get('/ptoEvents', [PtoEventsApiController::class, 'index']);
Route::get('/ptoEvents/{id}', [PtoEventsApiController::class, 'show']);

//Easy Join
Route::get('/easyJoin', [EasyJoinController::class, 'index']);
Route::post('/easyJoin/store', [EasyJoinController::class, 'store']);
Route::get('/fee-person-price', [FeePersonPriceApiController::class, 'index']);


//Subscriber Mail
Route::post('/ptoSubscribe', [PtoSubscribeMailApiController::class, 'store']);

// PTo Images
Route::get('/ptoGalleries', [PtoImagesApiController::class, 'index']);

// PTO NEWS LETTER GUIDE
Route::get('/ptoLetterGuides', [PtoLetterGuideApiController::class, 'index']);


//CALENDER
Route::get('/calendarEvents', [CalendarApiController::class, 'index']);

// TEACHER JOB POST 
Route::get('/jobPosts', [JobPostApiController::class, 'index']);
Route::post('/job-apply', action: [JobApplicationApiController::class, 'store']);


//ALUMNI Huston Info
Route::get('/alumniHuston', [AlumniHustonApiController::class, 'index']);

// Alumni Event 
Route::get('/alumniEvents', [AlumniEventsApiController::class, 'index']);
Route::get('/alumniEvents/{id}', [AlumniEventsApiController::class, 'show']);

//Alumni posts
Route::get('/alumniPosts', [AlumniPostApiController::class, 'index']);

// Alumni Images
Route::get('/alumniGalleries', [AlumniImageApiController::class, 'index']);

// Alumni Mails
Route::post('/alumniSubscribe', [AlumniMailApiController::class, 'store']);

// Alumni Form
Route::post('/alumniForm', [AlumniFormApiController::class, 'store']);

// Sponsors Packages
Route::get('/sponsorsPackages', [SponserPackageApiController::class, 'packages']);

// Contact Sponser
Route::post('/contactSponser', [ContactSponserController::class, 'store']);

// Apply Coupon
Route::post('/apply-coupon', [ApplyCouponController::class, 'apply']);

//Gernal Donation 
Route::post('/process-general-donation', [GeneralDonationController::class, 'processDonation']);
Route::post('/confirm-general-donation', [GeneralDonationController::class, 'confirmDonation']);

Route::post('/recurring-donation', [GeneralDonationController::class, 'recurringDonation']);
Route::post('/one-time-donation', [GeneralDonationController::class, 'oneTimeDonation']);


// Sponser Subscriber
Route::post('/sponserSubscriber', [SponserApiSubscriber::class, 'store']);
// Create subscription after SetupIntent
Route::post('/create-subscription', [GeneralDonationController::class, 'createSubscription']);
// Route to get the Client Secret
Route::post('/sponserIntent', [SponserApiSubscriber::class, 'createIntent']);

// Route to save the data after payment
Route::post('/sponserSubscriber', [SponserApiSubscriber::class, 'store']);

// PTO Event Attendees
Route::post('/pto-event-attendees', [PtoEventAttendeeController::class, 'store']);
Route::get('/pto-event-attendees', [PtoEventAttendeeController::class, 'index']);
Route::post('/pto-event-intent', [PtoEventAttendeeController::class, 'createIntent']);
Route::post('/pto-event-attendees', [PtoEventAttendeeController::class, 'store']);


// Alumni Event Attendees (similar to PTO Event Attendees)
Route::get('/alumni-event-attendees', [AlumniEventAttendeeController::class, 'index']);
Route::post('/alumni-event-attendees', [AlumniEventAttendeeController::class, 'store']);
Route::post('/alumni-event-intent', [AlumniEventAttendeeController::class, 'createIntent']); // For Stripe Payment Intent
Route::get('/alumniFee', [AlumniFeePersonPriceApiController::class, 'index']);


//Stripe Intnet
Route::post('/create-payment-intent', [App\Http\Controllers\Api\GeneralDonationController::class, 'createPaymentIntent']);
//paypal
Route::prefix('paypal')->group(function () {
    // One-Time Donation Routes
    Route::post('/create-order', [GeneralDonationController::class, 'createPaypalOrder']);
    Route::post('/capture-order', [GeneralDonationController::class, 'capturePaypalOrder']);

    // Recurring Donation Routes (Monthly/Yearly)
    Route::post('/create-subscription', [GeneralDonationController::class, 'createPaypalSubscription']);
    Route::post('/save-subscription', [GeneralDonationController::class, 'savePaypalSubscription']);
});

Route::post('/confirm-one-time-donation', [GeneralDonationController::class, 'confirmOneTimeDonation']);
