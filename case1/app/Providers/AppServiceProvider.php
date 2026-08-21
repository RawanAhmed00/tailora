<?php

namespace App\Providers;

// Interfaces & Repositories

use App\Repo\Interfaces\IWeatherRepo;
use App\Repo\Class\WeatherClass;
use App\Repo\Interfaces\IAnalyticsRepository;
use App\Repo\Interfaces\IBookingRepository;
use App\Repo\Interfaces\HotelRepositoryInterface;
use App\Repo\Interfaces\RestaurantRepositoryInterface;
use App\Repo\Class\AttractionRepository;
use App\Repo\Class\CategoryRepository;
use App\Repo\Class\CityRepository;
use App\Repo\Class\TripDayClass;
use App\Repo\Class\UserClass;
use App\Repo\Interfaces\IAttractionRepository;
use App\Repo\Interfaces\ICategoryRepository;
use App\Repo\Interfaces\ICityRepository;
use App\Repo\Interfaces\ITripDayRepo;
use App\Repo\Interfaces\UserInterface;
use App\Repo\Interfaces\IAvailabilityRepo;
use App\Repo\Class\HotelRepository;
use App\Repo\Class\RestaurantRepository;

use App\Repo\Class\AnalyticsRepository;
use App\Repo\Class\BookingRepository;
use App\Repo\Class\FavoriteRepository;
use App\Repo\Class\PaymentRepository;
use App\Repo\Class\TourGuideRequestRepository;
use App\Repo\Class\TripDayRepository;
use App\Repo\Class\TripRepository;
use App\Repo\Interfaces\FavoriteRepositoryInterface;
use App\Repo\Interfaces\IPaymentRepository;
use App\Repo\Interfaces\ITourGuideRequestRepository;
use App\Repo\Interfaces\ITripDayRepository;
use App\Repo\Interfaces\ITripRepository;
use App\Services\Class\AiService;
use App\Services\Class\AnalyticsService;
use App\Services\Class\BookingService;
use App\Repo\Class\AvailabilityClass;
use App\Repo\Class\ReviewRepo as ClassReviewRepo;
use App\Repo\Class\TourGuideRepo;
use App\Repo\Interfaces\TourGuideRepositoryInterface;
// Services & Interfaces
use App\Services\Class\AttractionService;
use App\Services\Class\CategoryService;
use App\Services\Class\CityService;
use App\Services\Class\CountryService;
use App\Services\Class\FavoriteService;
use App\Services\Class\FlightService;
use App\Services\Class\PaymentService;
use App\Services\Class\PaymobService;
use App\Services\Class\TripDayService;
use App\Services\Class\UserService;
use App\Services\Interfaces\IAttractionService;
use App\Services\Interfaces\ICategoryService;
use App\Services\Interfaces\ICityService;
use App\Services\Interfaces\ICountryService;
use App\Services\Interfaces\IFlightService;
use App\Services\Interfaces\ITripDayService;
use App\Services\Interfaces\IUserService;
use App\Services\Interfaces\IBookingService;
use App\Services\Interfaces\IAnalyticsService;
use App\Services\Interfaces\IAvailabilityService;
use App\Services\Class\TourGuideRequestService;
use App\Services\Class\TripService;
use App\Services\Class\WeatherService;
use App\Services\Interfaces\FavoriteServiceInterface;
use App\Services\Interfaces\IAiService;
use App\Services\Interfaces\IPaymentService;
use App\Services\Interfaces\IPaymobService;
use App\Services\Interfaces\ITourGuideRequestService;
use App\Services\Interfaces\ITripService;
use App\Services\Interfaces\IWeatherService;
use App\Services\Class\AvailabilityService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use App\Repo\ReviewRepo;

use App\Services\TourGuideService;
// Stashed changes

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
//  Updated upstream
        // Services
        $this->app->bind( FavoriteServiceInterface::class, FavoriteService::class);
        $this->app->bind(ICategoryService::class, CategoryService::class);
        $this->app->bind(IFlightService::class, FlightService::class);
        $this->app->bind(ICountryService::class, CountryService::class);
        $this->app->bind(ICityService::class, CityService::class);
        $this->app->bind(IAttractionService::class, AttractionService::class);
        $this->app->bind(IUserService::class, UserService::class);
        $this->app->bind(ITripDayService::class, TripDayService::class);
        $this->app->bind(IAnalyticsService::class,AnalyticsService::class);
        $this->app->bind(IBookingService::class,BookingService::class);
        $this->app->bind(IWeatherService::class, WeatherService::class);

        $this->app->bind(IPaymentService::class,PaymentService::class);
        $this->app->bind(IPaymobService::class,PaymobService::class);
        $this->app->bind( ITourGuideRequestService::class, TourGuideRequestService::class );
        $this->app->bind(ITripService::class,TripService::class);
        $this->app->bind(IAvailabilityService::class, AvailabilityService::class);

       // Repositories
        $this->app->bind(FavoriteRepositoryInterface::class, FavoriteRepository::class);
        $this->app->bind(ICategoryRepository::class, CategoryRepository::class);
        $this->app->bind(HotelRepositoryInterface::class, HotelRepository::class);
        $this->app->bind(RestaurantRepositoryInterface::class, RestaurantRepository::class);
        $this->app->bind(ICityRepository::class, CityRepository::class);
        $this->app->bind(IAttractionRepository::class, AttractionRepository::class);
        $this->app->bind(\App\Repo\Interfaces\ReviewRepositoryInterface::class, ClassReviewRepo::class);
        $this->app->bind(UserInterface::class, UserClass::class);
        $this->app->bind(ITripDayRepository::class, TripDayRepository::class);
        $this->app->bind(IAnalyticsRepository::class,AnalyticsRepository::class);
        $this->app->bind(IBookingRepository::class,BookingRepository::class);
        $this->app->bind(IWeatherRepo::class, WeatherClass::class);
        $this->app->bind(IPaymentRepository::class, PaymentRepository::class);
        $this->app->bind( ITourGuideRequestRepository::class, TourGuideRequestRepository::class );
        $this->app->bind(ITripRepository::class,TripRepository::class);
        $this->app->bind(IAiService::class,AiService::class);
        $this->app->bind(IAvailabilityRepo::class, AvailabilityClass::class);
        $this->app->bind(TourGuideRepositoryInterface::class,TourGuideRepo::class);

    }

    /**
     * Bootstrap any application services.
     */
     function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $user, string $token) {
            return config('app.frontend_url')
                . 'reset-password?token=' . $token
                . '&email=' . urlencode($user->email);
        });

        RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(1000)->by(
            $request->user()?->id ?: $request->ip()
        );
        });
    }

}