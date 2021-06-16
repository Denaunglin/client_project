        <?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
     */
    'app_latest_version_ios' => env('APP_LATEST_VERSION_IOS'),
    'app_latest_version_android' => env('APP_LATEST_VERSION_ANDROID'),
    'app_latest_generation_number_ios' => env('APP_LATEST_GENERATION_NUMBER_IOS'),
    'app_latest_generation_number_android' => env('APP_LATEST_GENERATION_NUMBER_ANDROID'),
    'apexhotel_appstore_url' => env('APEXHOTEL_APPSTORE_URL'),
    'apexhotel_playstore_url' => env('APEXHOTEL_PLAYSTORE_URL'),

    'base_url' => env('APP_URL'),

    'captcha_secret_key' => env('CAPTCHA_SECRET_KEY'),
    'captcha_site_key' => env('CAPTCHA_SITE_KEY'),

    'signal_app_id' => env('SIGNAL_APP_ID'),
    'Authorization_id' => env('Authorization_id'),

    'booking_prefix' => 'Apexhotel',

    'prefix_admin_url' => env('PREFIX_ADMIN_URL', '/backend'),

    'name' => env('APP_NAME', 'phone'),

    'facilities' => [1 => '24 Hours Front Desk', 2 => 'Wi-Fi', 3 => 'Breakfast ', 4 => 'Bathroom Amenities', 5 => 'Clothes Rack', 6 => ' Air Conditioning', 7 => 'Complimentary bottle water', 8 => 'Daily Housekeeping Service', 9 => 'Desk', 10 => 'Electric kettle', 11 => 'Hair Dryer', 12 => 'Hot & Cold Shower', 13 => 'Ironing', 14 => 'Minibar', 15 => 'Restaurant', 16 => 'Seating Area', 17 => 'Slipper', 18 => 'Swimming Pool', 19 => 'TV with Satellite/Cable Channels', 20 => 'Telephone', 21 => 'Room Air Condition', 22 => 'Fully Air Condition', 23 => 'Bathrobe',
    ],
    'status' => [0 => 'Approved', 1 => 'Confirmed', 2 => 'Canceled', 3 => 'Completed'],

    'status_msg' => [
        0 => ' Thanks so much for your booking. We check your booking frequently and will try our best to respond to your booking.',
        1 => ' Thanks so much for your booking. We check your booking frequently and will try our best to respond to your booking.',
        2 => 'Your Booking have been canceled ! ',
        3 => ' Thanks so much for your booking. We check your booking frequently and will try our best to respond to your booking.',
    ],

    'payment' => [0 => 'Pending', 1 => 'Complete', 2 => 'Partial'],
    'nationality' => [0 => 'undefined', 1 => 'Myanmar', 2 => 'Foreign'],

    'floor' => [1 => 'Ground Floor', 2 => 'First Floor'],

    'pay_method' => [1 => 'Credit Card', 2 => 'KBZ Pay', 3 => 'AyA Pay', 4 => 'CB Pay', 5 => 'Wave Pay', 6 => 'Cash', 7 => 'Others'],

    'rank' => [1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => '10', 11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29', 30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39', 40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49', 50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56'],

    'month' => [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'Novenber', 12 => 'December'],
    'year' => [1 => '2020', 2 => '2021', 3 => '2022', 4 => '2023', 5 => '2024', 6 => '2025', 7 => '2026', 8 => '2027', 9 => '2028', 10 => '2029', 11 => '2030', 12 => '2031', 13 => '2032', 14 => '2033', 15 => '2034', 16 => '2035', 17 => '2036', 18 => '2037', 19 => '2038', 20 => '2039', 21 => '2040'],
    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
     */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
     */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
     */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
     */

    // 'timezone' => 'UTC',
    'timezone' => 'Asia/Yangon',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
     */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
     */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
     */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
     */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
     */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        'Msurguy\Honeypot\HoneypotServiceProvider',
        Meneses\LaravelMpdf\LaravelMpdfServiceProvider::class,
        Darryldecode\Cart\CartServiceProvider::class,
        /*
         * Package Service Providers...
         */
        // Barryvdh\DomPDF\ServiceProvider::class,
        NotificationChannels\OneSignal\OneSignalServiceProvider::class,
        Spatie\LaravelImageOptimizer\ImageOptimizerServiceProvider::class,
        Spatie\Permission\PermissionServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        // App\Providers\ViewComposerServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
     */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        // 'PDF' => Barryvdh\DomPDF\Facade::class,
        'ResponseHelper' => App\Helpers\ResponseHelper::class,
        'ImageOptimizer' => Spatie\LaravelImageOptimizer\ImageOptimizerFacade::class,
        'Honeypot' => 'Msurguy\Honeypot\HoneypotFacade',
        'PDF' => Meneses\LaravelMpdf\Facades\LaravelMpdf::class,
        'Cart' => Darryldecode\Cart\Facades\CartFacade::class,
    ],
];
