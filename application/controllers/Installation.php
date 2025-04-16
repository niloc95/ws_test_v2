<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * @webSchedulr - Online Appointment Scheduler
 *
 * @package     @webSchedulr
 * @author      N.N Cara <nilo.cara@frontend.co.za>
 * @copyright   Copyright (c) Nilesh Cara
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://webschedulr.co.za
 * @since       v1.1.0
 * ---------------------------------------------------------------------------- */

/**
 * Installation controller.
 *
 * Handles the installation related operations.
 *
 * @package Controllers
 */
class Installation extends WS_Controller
{
    /**
     * Installation constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('admins_model');
        $this->load->model('settings_model');
        $this->load->model('services_model');
        $this->load->model('providers_model');
        $this->load->model('customers_model');

        $this->load->library('instance');
    }

    /**
     * Display the installation page.
     */
    public function index(): void
    {
        if (is_app_installed()) {
            redirect();
            return;
        }

        $checks = $this->_perform_environment_checks();

        $this->load->view('pages/installation', [
            'base_url' => config('base_url'),
            'checks' => $checks, // Pass checks to the view
            'show_form' => $checks['all_passed'] // Only show form if basic checks pass
        ]);
    }

    /**
     * Perform environment checks before installation.
     *
     * @return array Results of the checks.
     */
    private function _perform_environment_checks(): array
    {
        $results = [
            'php_version' => ['passed' => FALSE, 'required' => '7.4', 'current' => PHP_VERSION],
            'extensions' => [
                'mbstring' => FALSE,
                'pdo_mysql' => FALSE, // Or your specific DB driver
                'json' => FALSE,
                'gd' => FALSE, // If image processing is needed
                'intl' => FALSE, // Often needed for localization
            ],
            'writable_dirs' => [
                APPPATH . 'config/' => FALSE,
                FCPATH . 'storage/logs/' => FALSE,
                FCPATH . 'storage/cache/' => FALSE,
                FCPATH . 'storage/sessions/' => FALSE,
                // Add other necessary writable directories
            ],
            'all_passed' => TRUE, // Assume true initially
            'messages' => []
        ];

        // Check PHP Version
        if (version_compare(PHP_VERSION, $results['php_version']['required'], '>=')) {
            $results['php_version']['passed'] = TRUE;
        } else {
            $results['all_passed'] = FALSE;
            $results['messages'][] = 'PHP version ' . $results['php_version']['required'] . ' or higher is required. You have ' . PHP_VERSION . '.';
        }

        // Check Extensions
        foreach (array_keys($results['extensions']) as $ext) {
            if (extension_loaded($ext)) {
                $results['extensions'][$ext] = TRUE;
            } else {
                $results['all_passed'] = FALSE;
                $results['messages'][] = 'Required PHP extension "' . $ext . '" is not loaded.';
            }
        }

        // Check Writable Directories
        foreach (array_keys($results['writable_dirs']) as $dir) {
            if (is_dir($dir) && is_writable($dir)) {
                $results['writable_dirs'][$dir] = TRUE;
            } else {
                $results['all_passed'] = FALSE;
                $results['messages'][] = 'Directory "' . $dir . '" must be writable by the web server.';
            }
        }

        // Check if root config.php is writable (if you plan to write to it)
        // Or check if .env file exists and is writable if using that approach

        return $results;
    }

    /**
     * Installs @webSchedulr on the server.
     */
    public function perform(): void
    {
        try {
            if (is_app_installed()) {
                return;
            }

            $admin = request('admin');
            $company = request('company');

            $this->instance->migrate();

            // Insert admin
            $admin['timezone'] = date_default_timezone_get();
            $admin['settings']['username'] = $admin['username'];
            $admin['settings']['password'] = $admin['password'];
            $admin['settings']['notifications'] = true;
            $admin['settings']['calendar_view'] = CALENDAR_VIEW_DEFAULT;
            unset($admin['username'], $admin['password']);
            $admin['id'] = $this->admins_model->save($admin);

            session([
                'user_id' => $admin['id'],
                'user_email' => $admin['email'],
                'role_slug' => DB_SLUG_ADMIN,
                'language' => $admin['language'],
                'timezone' => $admin['timezone'],
                'username' => $admin['settings']['username'],
            ]);

            // Save company settings
            setting([
                'company_name' => $company['company_name'],
                'company_email' => $company['company_email'],
                'company_link' => $company['company_link'],
            ]);

            // Service
            $service_id = $this->services_model->save([
                'name' => 'Service',
                'duration' => '30',
                'price' => '0',
                'currency' => '',
                'availabilities_type' => 'flexible',
                'attendants_number' => '1',
            ]);

            // Provider
            $this->providers_model->save([
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'jane@example.org',
                'phone_number' => '+1 (000) 000-0000',
                'services' => [$service_id],
                'language' => $admin['language'],
                'timezone' => $admin['timezone'],
                'settings' => [
                    'username' => 'janedoe',
                    'password' => random_string(),
                    'working_plan' => setting('company_working_plan'),
                    'notifications' => true,
                    'google_sync' => false,
                    'sync_past_days' => 30,
                    'sync_future_days' => 90,
                    'calendar_view' => CALENDAR_VIEW_DEFAULT,
                ],
            ]);

            // Customer
            $this->customers_model->save([
                'first_name' => 'James',
                'last_name' => 'Doe',
                'email' => 'james@example.org',
                'phone_number' => '+1 (000) 000-0000',
            ]);

            // Generate a secure encryption key
            $new_encryption_key = base64_encode(random_bytes(32));

            // Prepare .env content (example)
            $dotenv_content = [
                'APP_ENV=production', // Default to production after install
                'DEBUG_MODE=false',
                'BASE_URL=' . $this->input->post('base_url'), // Assuming you collect this or derive it
                'DB_HOSTNAME=' . $this->input->post('db_host'), // Assuming you collect DB details
                'DB_USERNAME=' . $this->input->post('db_user'),
                'DB_PASSWORD=' . $this->input->post('db_pass'),
                'DB_DATABASE=' . $this->input->post('db_name'),
                'DB_DRIVER=' . $this->input->post('db_driver'),
                'ENCRYPTION_KEY=' . $new_encryption_key, // Use the generated key
                // Add other necessary variables based on user input or defaults
            ];

            // Option 1: Display to User (Recommended)
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'success' => true,
                     'message' => 'Installation data processed. Please create a `.env` file in the project root with the following content:',
                     'dotenv_content' => implode("\n", $dotenv_content),
                     'next_step' => 'create_env_file'
                 ]));
            return;

            // **Option 2: Attempt to Write (Less Secure - Requires careful permission handling)**
            /*
            $env_path = FCPATH . '.env';
            if (file_put_contents($env_path, implode("\n", $dotenv_content)) === false) {
                // Handle error - couldn't write file
                $this->output
                     ->set_content_type('application/json')
                     ->set_status_header(500)
                     ->set_output(json_encode(['success' => false, 'message' => 'Could not write .env file. Please check permissions.']));
                return;
            }
            */

            // After .env is handled (either by user confirmation or successful write):
            // - Create database tables
            // - Add admin user
            // - Add company settings
            // - Create the installation flag file
            // file_put_contents(FCPATH . 'storage/installed.flag', date('Y-m-d H:i:s'));

            // Send final success response
            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
