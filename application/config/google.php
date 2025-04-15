<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Google Calendar - Internal Configuration
|--------------------------------------------------------------------------
|
| Declare some of the global config values of the Google Calendar
| synchronization feature.
|
*/

// Read Google Sync settings from .env file
$config['google_sync_feature'] = filter_var(isset($_ENV['GOOGLE_SYNC_FEATURE']) ? $_ENV['GOOGLE_SYNC_FEATURE'] : false, FILTER_VALIDATE_BOOLEAN); // Use $_ENV
$config['google_client_id'] = isset($_ENV['GOOGLE_CLIENT_ID']) ? $_ENV['GOOGLE_CLIENT_ID'] : ''; // Use $_ENV
$config['google_client_secret'] = isset($_ENV['GOOGLE_CLIENT_SECRET']) ? $_ENV['GOOGLE_CLIENT_SECRET'] : ''; // Use $_ENV

// You might also need the redirect URI if it was previously in the old Config class
// $config['google_redirect_uri'] = getenv('GOOGLE_REDIRECT_URI') ?: '';

/* End of file google.php */
/* Location: ./application/config/google.php */
