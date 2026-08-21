<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AiController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AttractionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymobWebhookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TourGuideController;
use App\Http\Controllers\TourGuideRequestController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\TripControllerAdmin;
use App\Http\Controllers\TripDayController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\WebsiteSettingController;

/*
|--------------------------------------------------------------------------
| Public Routes 
|--------------------------------------------------------------------------
*/

// Auth Public Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forget-password', [AuthController::class, 'forgetPassword']);
    Route::post('/reset-password/{token}/{email}', [AuthController::class, 'resetPassword']);
    Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('email/resend', [AuthController::class, 'resendVerification'])->middleware('throttle:6,1');
    Route::get('email/status', [AuthController::class, 'verificationStatus']);
});


Route::post('/payments/paymob/webhook', [PaymobWebhookController::class, 'handle']);

// Website Settings Public
Route::get('website-settings', [WebsiteSettingController::class, 'index']);
Route::get('website-settings/{websiteSetting}', [WebsiteSettingController::class, 'show']);

// Countries
Route::prefix('countries')->group(function () {
    Route::get('/', [CountryController::class, 'index']);
    Route::get('/search', [CountryController::class, 'search']);
    Route::get('/region/{region}', [CountryController::class, 'region']);
    Route::get('/{name}', [CountryController::class, 'show']);
});

// Cities
Route::prefix('cities')->group(function () {
    Route::get('/', [CityController::class, 'index']);
    Route::get('/{id}', [CityController::class, 'show']);
});

// Attractions
Route::prefix('attractions')->group(function () {
    Route::get('/', [AttractionController::class, 'index']);
    Route::get('/{id}', [AttractionController::class, 'show']);
});

// Categories
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'show']);
});

// Hotels & Restaurants
Route::get('hotels', [HotelController::class, 'index']);
Route::get('hotels/{id}', [HotelController::class, 'show']);
Route::get('restaurants', [RestaurantController::class, 'index']);
Route::get('restaurants/{id}', [RestaurantController::class, 'show']);

// Public Flights Search
Route::prefix('flights')->group(function () {
    Route::get('/airports', [FlightController::class, 'searchAirports']);
    Route::post('/one-way', [FlightController::class, 'searchOneWay']);
    Route::post('/round-trip', [FlightController::class, 'searchRoundTrip']);
    Route::post('/search', [FlightController::class, 'searchFlexible']);
});


/*
|--------------------------------------------------------------------------
| Authenticated Profile & Shared Routes 
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);

    // Profile Management
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    // Contact Messages
    Route::post('/contact-messages', [ContactMessageController::class, 'store']);
});


/*
|--------------------------------------------------------------------------
| User Routes 
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api','user','throttle:api'])->group(function () {

    // Payments
    Route::prefix('payments')->group(function () {

     Route::post('/', [PaymentController::class, 'store'])->middleware('idempotency');
     Route::get('/{id}', [PaymentController::class, 'show']);
     Route::post('/{id}/checkout', [PaymentController::class, 'checkout'])->middleware('idempotency');
});
    Route::prefix('weather')->group(function () {
        Route::get('/trips/{tripId}', [WeatherController::class, 'show']);
        Route::post('/trips/{tripId}', [WeatherController::class, 'store']);
    });

    // Flight Booking / Selection Actions
    Route::prefix('flights')->group(function () {
        Route::post('/select', [FlightController::class, 'selectFlight']);
        Route::post('/booking-links', [FlightController::class, 'getBookingLinks']);
    });

    // Trips
    Route::prefix('trips')->group(function () {
        Route::get('/', [TripController::class, 'index']);
        Route::post('/', [TripController::class, 'store']);
        Route::get('/{id}', [TripController::class, 'show']);
        Route::delete('/{id}', [TripController::class, 'destroy']);
        Route::post('/trips/{id}/cities',[TripController::class, 'selectCities']);
        Route::put('/trip-days/{id}/city',[TripController::class, 'updateTripDayCity']);
        Route::get('/trip-days/{id}/attractions',[TripController::class, 'getTripDayAttractions']);
        Route::post('/trip-days/{tripDayId}/attractions',[TripController::class, 'selectTripDayAttractions']);
        Route::get('/trips/{id}/full',[TripController::class, 'getFullTrip']);

    });
    // AI
    Route::prefix('ai')->group(function () {

    Route::post('/enhance', [AiController::class, 'enhancedContent']);
    Route::post('/travel', [AiController::class, 'travel']);
    Route::post('/travel/{conversationId}/plans', [AiController::class,'generatePlans']);
    Route::post('/travel/{conversationId}/choose', [AiController::class,'choosePlan']);
    Route::post('/recommendations', [AiController::class,'recommendations']);
    Route::post('/places', [AiController::class,'bestPlaces']);
    Route::post('/travel-tips', [AiController::class,'travelTips']);
    Route::post('/trip/{tripId}/recommendation', [AiController::class,'tripRecommendation']);
    });

    // Bookings - User
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index']);
        Route::post('/', [BookingController::class, 'store']);
        Route::get('/{id}', [BookingController::class, 'show']);
    });

    // Favorites
    Route::prefix('favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index']);
        Route::post('/', [FavoriteController::class, 'store']);
        Route::delete('/', [FavoriteController::class, 'destroy']);
    });

    // Reviews - User
    Route::prefix('reviews')->group(function () {
        Route::post('/', [ReviewController::class, 'store']);
        Route::get('/trip/{tripId}', [ReviewController::class, 'getTripReviewInfo']);
    });
    Route::get('/trips/{tripId}/review-info', [ReviewController::class, 'getTripReviewInfo']);

    Route::get('/statistics', [AnalyticsController::class,'statistics']);
    Route::get('/trips', [AnalyticsController::class,'savedTrips']);
    Route::get('/favorites', [AnalyticsController::class,'favorites']);
    Route::get('/bookings', [AnalyticsController::class,'bookingHistory']);
});

/*
|--------------------------------------------------------------------------
| Chat Routes - User & Tour Guide
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')
    ->prefix('chats')
    ->group(function () {

        Route::get('/', [ChatController::class, 'index']);
        Route::get('/{id}', [ChatController::class, 'getMessages']);
        Route::post('/{id}/messages', [ChatController::class, 'sendMessage']);
        Route::put('/{id}/read', [ChatController::class, 'markAsRead']);
        Route::get('/unread-count', [ChatController::class, 'unreadCount']);
        Route::delete('/messages/{id}', [ChatController::class, 'deleteMessage']);
    });


/*
|--------------------------------------------------------------------------
| Tour Guide Routes 
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api','guide','throttle:api'])->group(function () {

        Route::get('/dashboard', [AnalyticsController::class,'tourGuideDashboard']);
        Route::get('/requests', [TourGuideRequestController::class, 'index']);
        Route::get('/requests/{id}', [TourGuideRequestController::class, 'show']);
        Route::patch('/requests/{id}/accept', [TourGuideRequestController::class, 'accept']);
        Route::patch('/requests/{id}/reject', [TourGuideRequestController::class, 'reject']);

        Route::get('/availabilities', [AvailabilityController::class, 'index']);
        Route::post('/availabilities', [AvailabilityController::class, 'store']);
        Route::put('/availabilities/{id}', [AvailabilityController::class, 'update']);
        Route::delete('/availabilities/{id}', [AvailabilityController::class, 'destroy']);
     
        Route::get('/tour-guide/schedule', [TourGuideController::class, 'schedule']);


        Route::get('/tour-guide/earnings', [TourGuideController::class, 'earnings']);
        Route::get('/tour-guide/earnings/history', [TourGuideController::class, 'earningsHistory']);
        Route::get('/tour-guide/reviews', [TourGuideController::class, 'reviews']);
        Route::get('/tour-guide/rating', [TourGuideController::class, 'rating']);
    });


/*
|--------------------------------------------------------------------------
| Admin Routes 
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api','admin','throttle:api'])
    ->group(function () {

        // Dashboard Analytics
        Route::get('/analytics/dashboard', [AnalyticsController::class, 'dashboard']);

        // Website Settings
        Route::post('website-settings', [WebsiteSettingController::class, 'store']);
        Route::put('website-settings/{websiteSetting}', [WebsiteSettingController::class, 'update']);
        Route::delete('website-settings/{websiteSetting}', [WebsiteSettingController::class, 'destroy']);

        // Hotels
        Route::post('hotels', [HotelController::class, 'store']);
        Route::put('hotels/{id}', [HotelController::class, 'update']);
        Route::delete('hotels/{id}', [HotelController::class, 'destroy']);

        // Restaurants
        Route::post('restaurants', [RestaurantController::class, 'store']);
        Route::put('restaurants/{id}', [RestaurantController::class, 'update']);
        Route::delete('restaurants/{id}', [RestaurantController::class, 'destroy']);

        // Trips Admin
        Route::get('trips-admin', [TripControllerAdmin::class, 'index']);
        Route::get('trips-admin/statistics', [TripControllerAdmin::class, 'TripStatistics']);
        Route::put('trips-admin/{trip}', [TripControllerAdmin::class, 'update']);
        Route::delete('trips-admin/{trip}', [TripControllerAdmin::class, 'destroy']);

        // Attractions
        Route::post('attractions', [AttractionController::class, 'store']);
        Route::put('attractions/{id}', [AttractionController::class, 'update']);
        Route::delete('attractions/{id}', [AttractionController::class, 'destroy']);

        // Cities
        Route::post('cities', [CityController::class, 'store']);
        Route::put('cities/{id}', [CityController::class, 'update']);
        Route::delete('cities/{id}', [CityController::class, 'destroy']);

        // Categories
        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{id}', [CategoryController::class, 'update']);
        Route::delete('categories/{id}', [CategoryController::class, 'destroy']);

        // Contact Messages
        Route::get('contact-messages', [ContactMessageController::class, 'index']);
        Route::get('contact-messages/{id}', [ContactMessageController::class, 'show']);
        Route::put('contact-messages/{id}', [ContactMessageController::class, 'update']);
        Route::delete('contact-messages/{id}', [ContactMessageController::class, 'destroy']);

        // Reviews
        Route::get('reviews', [ReviewController::class, 'index']);
        Route::put('reviews/{id}/approve', [ReviewController::class, 'approve']);
        Route::put('reviews/{id}/reject', [ReviewController::class, 'reject']);
        Route::delete('reviews/{id}', [ReviewController::class, 'destroy']);

        // Users
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::patch('/users/{id}/status', [UserController::class, 'changeStatus']);

        // Bookings - Admin
        Route::get('/bookings', [BookingController::class, 'adminIndex']);
        Route::get('/bookings/{id}', [BookingController::class, 'adminShow']);
        Route::put('/bookings/{id}', [BookingController::class, 'update']);
        Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);
        Route::post('/bookings/{id}/assign-guide', [BookingController::class, 'assignTourGuide']);
        Route::get('/tour-guides/available', [BookingController::class, 'getAvailableTourGuides']);
    });