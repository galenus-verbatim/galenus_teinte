<?php declare(strict_types=1);

include_once(__DIR__ . '/vendor/autoload.php');

/** Routage */

use Psr\Log\{LogLevel};
use Oeuvres\Kit\{Route, I18n, Http, Log, LoggerWeb};

// debug routing
// Log::setLogger(new LoggerWeb(LogLevel::DEBUG));

// upload file
Route::post('/upload', __DIR__ . '/action/upload.php', [], null);
// download transformed uploaded file
Route::get('/download', __DIR__ . '/action/download.php', [], null);
// list files working
Route::get('/listing', __DIR__ . '/action/listing.php', [], null);
// Run the daemon on the work directory
Route::route(
    '/working', // path to request
    __DIR__ . '/action/working.php', // contents to include
    [], // parameters
    // null, 
    __DIR__ . '/tmpl_action.php' // template
);


// register the default template in which include content
Route::template(__DIR__ . '/tmpl.php');

// welcome page
Route::get('/', __DIR__ . '/page/accueil.php');
// try if a local php page is available
Route::get('/(.*)', __DIR__ . '/page/$1.php');
// or a local html page
Route::get('/(.*)', __DIR__ . '/page/$1.html');



// Catch all, php or html
Route::route('/404', __DIR__ . '/page/404.php');
Route::route('/404', __DIR__ . '/page/404.html');
// No Route has worked
echo "Bad routage, 404.";
