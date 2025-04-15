<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * @webSchedulr - Online Appointment Scheduler
 *
 * @package     @webSchedulr - Online Appointments
 * @author      N N.Cara <nilo.cara@frontend.co.za>
 * @copyright   Copyright (c) Nilo Cara
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://webschedulr.co.za
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     * Uses getenv() for better reliability across different server configurations.
     *
     * Example:
     *
     * $debug = env('DEBUG_MODE', false);
     *
     * @param string $key Environment key.
     * @param mixed|null $default Default value if the environment variable is not set or empty.
     *
     * @return mixed The value of the environment variable or the default value.
     *
     * @throws InvalidArgumentException If the key is empty.
     */
    function env(string $key, mixed $default = null): mixed
    {
        if (empty($key)) {
            throw new InvalidArgumentException('The environment variable key cannot be empty.');
        }

        // Use getenv() which is generally more reliable
        $value = getenv($key);

        // getenv() returns false if the variable doesn't exist.
        // Check for false explicitly and return default in that case.
        if ($value === false) {
            return $default;
        }

        // Handle boolean strings
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        // Remove quotes if value is wrapped in them (optional, but common for .env)
        if (strlen($value) > 1 && $value[0] === '"' && $value[strlen($value) - 1] === '"') {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
