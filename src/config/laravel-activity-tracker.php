<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel Activity Tracker Database Settings
    |--------------------------------------------------------------------------
    */

    'loggerDatabaseConnection'  => env('LARAVEL_ACTIVITY_TRACKER_DATABASE_CONNECTION', env('DB_CONNECTION', 'mysql')),
    'loggerDatabaseTable'       => env('LARAVEL_ACTIVITY_TRACKER_DATABASE_TABLE', 'laravel_activity_tracker'),

    /*
    |--------------------------------------------------------------------------
    | Laravel Activity Tracker Roles Settings - (laravel roles not required if false)
    |--------------------------------------------------------------------------
    */

    'rolesEnabled'   => env('LARAVEL_ACTIVITY_TRACKER_ROLES_ENABLED', false),
    'rolesMiddlware' => env('LARAVEL_ACTIVITY_TRACKER_ROLES_MIDDLWARE', 'role:admin'),

    /*
    |--------------------------------------------------------------------------
    | Laravel Activity Tracker Authentication Required
    |--------------------------------------------------------------------------
    */

    'authRequired' => env('LARAVEL_ACTIVITY_TRACKER_AUTH_REQUIRED', true),

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable Laravel Activity Tracker Middlware
    |--------------------------------------------------------------------------
    */

    'loggerMiddlewareEnabled'   => env('LARAVEL_ACTIVITY_TRACKER_MIDDLEWARE_ENABLED', true),
    'loggerMiddlewareExcept'    => array_filter(explode(',', trim((string) env('LARAVEL_ACTIVITY_TRACKER_MIDDLEWARE_EXCEPT') ?? ''))),

    /*
    |--------------------------------------------------------------------------
    | Laravel Activity Tracker Authentication Listeners Enable/Disable
    |--------------------------------------------------------------------------
    */
    'logAllAuthEvents'      => false,   // May cause a lot of duplication.
    'logAuthAttempts'       => false,   // Successful and Failed -  May cause a lot of duplication.
    'logFailedAuthAttempts' => true,    // Failed Logins
    'logLockOut'            => true,    // Account Lockout
    'logPasswordReset'      => true,    // Password Resets
    'logSuccessfulLogin'    => true,    // Successful Login
    'logSuccessfulLogout'   => true,    // Successful Logout

    /*
    |--------------------------------------------------------------------------
    | Laravel Activity Tracker Search Enable/Disable
    |--------------------------------------------------------------------------
    */
    'enableSearch'      => env('LARAVEL_ACTIVITY_TRACKER_ENABLE_SEARCH', false),

    /*
    |--------------------------------------------------------------------------
    | Laravel Activity Tracker Date Filtering Enable/Disable
    |--------------------------------------------------------------------------
    */
    'enableDateFiltering' => env('LARAVEL_ACTIVITY_TRACKER_ENABLE_DATE_FILTERING', true),

    /*
    |--------------------------------------------------------------------------
    | Laravel Activity Tracker Export Enable/Disable
    |--------------------------------------------------------------------------
    */
    'enableExport'      => env('LARAVEL_ACTIVITY_TRACKER_ENABLE_EXPORT', true),

    /*
    |--------------------------------------------------------------------------
    | Laravel Activity Tracker Search Parameters
    |--------------------------------------------------------------------------
    */
    // you can add or remove from these options [description,user,method,route,ip]
    'searchFields'  => env('LARAVEL_ACTIVITY_TRACKER_SEARCH_FIELDS', 'description,user,method,route,ip'),

    /*
    |--------------------------------------------------------------------------
    | Laravel Default Models
    |--------------------------------------------------------------------------
    */

    'defaultActivityModel' => env('LARAVEL_ACTIVITY_TRACKER_ACTIVITY_MODEL', 'Dimer47\LaravelActivityTracker\App\Models\Activity'),
    'defaultUserModel'     => env('LARAVEL_ACTIVITY_TRACKER_USER_MODEL', 'App\User'),

    /*
    |--------------------------------------------------------------------------
    | Laravel Default User ID Field
    |--------------------------------------------------------------------------
    */

    'defaultUserIDField' => env('LARAVEL_ACTIVITY_TRACKER_USER_ID_FIELD', 'id'),

    /*
    |--------------------------------------------------------------------------
    | Disable automatic Laravel Activity Tracker routes
    | If you want to customise the routes the package uses, set this to true.
    | For more information, see the README.
    |--------------------------------------------------------------------------
    */

    'disableRoutes' => env('LARAVEL_ACTIVITY_TRACKER_DISABLE_ROUTES', false),

    /*
    |--------------------------------------------------------------------------
    | Laravel Activity Tracker Pagination Settings
    |--------------------------------------------------------------------------
    */
    'loggerPaginationEnabled' => env('LARAVEL_ACTIVITY_TRACKER_PAGINATION_ENABLED', true),
    'loggerCursorPaginationEnabled' => env('LARAVEL_ACTIVITY_TRACKER_CURSOR_PAGINATION_ENABLED', false),
    'loggerPaginationPerPage' => env('LARAVEL_ACTIVITY_TRACKER_PAGINATION_PER_PAGE', 25),

    /*
    |--------------------------------------------------------------------------
    | Laravel Activity Tracker Databales Settings - Not recommended with pagination.
    |--------------------------------------------------------------------------
    */

    'loggerDatatables'              => env('LARAVEL_ACTIVITY_TRACKER_DATATABLES_ENABLED', false),
    'loggerDatatablesCSScdn'        => 'https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css',
    'loggerDatatablesJScdn'         => 'https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js',
    'loggerDatatablesJSVendorCdn'   => 'https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js',

    /*
    |--------------------------------------------------------------------------
    | Laravel Activity Tracker Dashboard Settings
    |--------------------------------------------------------------------------
    */

    'enableSubMenu'     => env('LARAVEL_ACTIVITY_TRACKER_DASHBOARD_MENU_ENABLED', true),
    'enableDrillDown'   => env('LARAVEL_ACTIVITY_TRACKER_DASHBOARD_DRILLABLE', true),

    /*
    |--------------------------------------------------------------------------
    | Laravel Activity Tracker Failed to Log Settings
    |--------------------------------------------------------------------------
    */

    'logDBActivityLogFailuresToFile' => env('LARAVEL_ACTIVITY_TRACKER_LOG_RECORD_FAILURES_TO_FILE', true),

    /*
    |--------------------------------------------------------------------------
    | Laravel Activity Tracker Flash Messages
    |--------------------------------------------------------------------------
    */

    'enablePackageFlashMessageBlade' => env('LARAVEL_ACTIVITY_TRACKER_FLASH_MESSAGE_BLADE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Blade settings
    |--------------------------------------------------------------------------
    */

    // The parent Blade file
    'loggerBladeExtended'       => env('LARAVEL_ACTIVITY_TRACKER_LAYOUT', 'layouts.app'),

    // Switch Between bootstrap 3 `panel` and bootstrap 4 `card` classes
    'bootstapVersion'           => env('LARAVEL_ACTIVITY_TRACKER_BOOTSTRAP_VERSION', '4'),

    // Additional Card classes for styling -
    // See: https://getbootstrap.com/docs/4.0/components/card/#background-and-color
    // Example classes: 'text-white bg-primary mb-3'
    'bootstrapCardClasses'      => '',

    // Blade Extension Placement
    'bladePlacement'            => env('LARAVEL_ACTIVITY_TRACKER_BLADE_PLACEMENT', 'yield'),
    'bladePlacementCss'         => env('LARAVEL_ACTIVITY_TRACKER_BLADE_PLACEMENT_CSS', 'template_linked_css'),
    'bladePlacementJs'          => env('LARAVEL_ACTIVITY_TRACKER_BLADE_PLACEMENT_JS', 'footer_scripts'),

    /*
    |--------------------------------------------------------------------------
    | Laravel Activity Tracker Dependencies - allows for easier builds into other projects
    |--------------------------------------------------------------------------
    */

    // jQuery
    'enablejQueryCDN'           => env('LARAVEL_ACTIVITY_TRACKER_JQUERY_CDN_ENABLED', true),
    'JQueryCDN'                 => env('LARAVEL_ACTIVITY_TRACKER_JQUERY_CDN_URL', 'https://code.jquery.com/jquery-3.2.1.slim.min.js'),

    // Bootstrap
    'enableBootstrapCssCDN'     => env('LARAVEL_ACTIVITY_TRACKER_BOOTSTRAP_CSS_CDN_ENABLED', true),
    'bootstrapCssCDN'           => env('LARAVEL_ACTIVITY_TRACKER_BOOTSTRAP_CSS_CDN_URL', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css'),
    'enableBootstrapJsCDN'      => env('LARAVEL_ACTIVITY_TRACKER_BOOTSTRAP_JS_CDN_ENABLED', true),
    'bootstrapJsCDN'            => env('LARAVEL_ACTIVITY_TRACKER_BOOTSTRAP_JS_CDN_URL', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js'),
    'enablePopperJsCDN'         => env('LARAVEL_ACTIVITY_TRACKER_POPPER_JS_CDN_ENABLED', true),
    'popperJsCDN'               => env('LARAVEL_ACTIVITY_TRACKER_POPPER_JS_CDN_URL', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js'),

    // Font Awesome
    'enableFontAwesomeCDN'      => env('LARAVEL_ACTIVITY_TRACKER_FONT_AWESOME_CDN_ENABLED', true),
    'fontAwesomeCDN'            => env('LARAVEL_ACTIVITY_TRACKER_FONT_AWESOME_CDN_URL', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'),

    // LiveSearch for scalability
    'enableLiveSearch'          => env('LARAVEL_ACTIVITY_TRACKER_LIVE_SEARCH_ENABLED', true),

    // GeoPlugin for IP lookup
    'enableGeoPlugin'           => env('LARAVEL_ACTIVITY_TRACKER_GEO_PLUGIN_ENABLED', true),
    'geoPluginUrl'              => env('LARAVEL_ACTIVITY_TRACKER_GEO_PLUGIN_URL', 'http://www.geoplugin.net/json.gp?ip='),

];
