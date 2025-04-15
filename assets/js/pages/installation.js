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

/**
 * Installation page.
 *
 * This module implements the functionality of the installation page.
 */
App.Pages.Installation = (function () {
    const MIN_PASSWORD_LENGTH = 7;
    const $install = $('#install');
    const $alert = $('.alert');
    const $loading = $('#loading');
    const $firstName = $('#first-name');
    const $lastName = $('#last-name');
    const $email = $('#email');
    const $phoneNumber = $('#phone-number');
    const $username = $('#username');
    const $password = $('#password');
    const $passwordConfirm = $('#password-confirm');
    const $language = $('#language');
    const $companyName = $('#company-name');
    const $companyEmail = $('#company-email');
    const $companyLink = $('#company-link');

    $(document).ajaxStart(() => {
        $loading.removeClass('d-none');
    });

    $(document).ajaxStop(() => {
        $loading.addClass('d-none');
    });

    /**
     * Event: Install @webSchedulr Button "Click"
     */
    $install.on('click', () => {
        if (!validate()) {
            return;
        }

        const url = App.Utils.Url.siteUrl('installation/perform');

        const data = {
            csrf_token: vars('csrf_token'),
            admin: getAdminData(),
            company: getCompanyData(),
        };

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
        }).done(() => {
            $alert
                .text('@webSchedulr has been successfully installed!')
                .addClass('alert-success')
                .prop('hidden', false);

            setTimeout(() => {
                window.location.href = App.Utils.Url.siteUrl('calendar');
            }, 1000);
        });
    });

    /**
     * Validates the user input.
     *
     * Use this before executing the installation procedure.
     *
     * @return {Boolean} Returns the validation result.
     */
    function validate() {
        try {
            const $fields = $('input');

            $alert.removeClass('alert-danger').prop('hidden', true);

            $fields.removeClass('is-invalid');

            // Check for empty fields.
            let missingRequired = false;

            $fields.each((index, field) => {
                if (!$(field).val()) {
                    $(field).addClass('is-invalid');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(lang('fields_are_required'));
            }

            // Validate Passwords
            if ($password.val() !== $passwordConfirm.val()) {
                $password.addClass('is-invalid');
                $passwordConfirm.addClass('is-invalid');
                throw new Error(lang('passwords_mismatch'));
            }

            if ($password.val().length < MIN_PASSWORD_LENGTH) {
                $password.addClass('is-invalid');
                $passwordConfirm.addClass('is-invalid');
                throw new Error(lang('password_length_notice').replace('$number', MIN_PASSWORD_LENGTH));
            }

            // Validate Email
            if (!App.Utils.Validation.email($email.val())) {
                $email.addClass('is-invalid');
                throw new Error(lang('invalid_email'));
            }

            if (!App.Utils.Validation.email($companyEmail.val())) {
                $companyEmail.addClass('is-invalid');
                throw new Error(lang('invalid_email'));
            }

            return true;
        } catch (error) {
            $alert.addClass('alert-danger').text(error.message).prop('hidden', false);

            return false;
        }
    }

    /**
     * Get the admin data as an object.
     *
     * @return {Object}
     */
    function getAdminData() {
        return {
            first_name: $firstName.val(),
            last_name: $lastName.val(),
            email: $email.val(),
            phone_number: $phoneNumber.val(),
            username: $username.val(),
            password: $password.val(),
            language: $language.val(),
        };
    }

    /**
     * Get the company data as an object.
     *
     * @return {Object}
     */
    function getCompanyData() {
        return {
            company_name: $companyName.val(),
            company_email: $companyEmail.val(),
            company_link: $companyLink.val(),
        };
    }

    // Validate the base URL setting (must not contain any trailing slash).
    if (vars('base_url').slice(-1) === '/') {
        App.Utils.Message.show(
            'Invalid Configuration Detected',
            'Please remove any trailing slashes from your "BASE_URL" setting of the root "config.php" file and try again.',
        );
        $install.prop('disabled', true).fadeTo('0.4');
    }

    return {};
})();

$(document).ready(function() {
    $('#installation-form').on('submit', function(e) {
        e.preventDefault();
        $('#install').prop('disabled', true).text('Installing...');
        $('.alert-danger').hide();

        // Collect form data
        const formData = {
            first_name: $('#first-name').val(),
            last_name: $('#last-name').val(),
            email: $('#email').val(),
            // ... other fields ...
            db_host: $('#db-host').val(), // Assuming you add DB fields
            // ...
            csrf_token: $('input[name="csrf_token"]').val() // Include CSRF token
        };

        $.ajax({
            url: '<?= site_url("installation/process") ?>', // New controller method
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.next_step === 'create_env_file') {
                        // Display the .env content and instructions
                        $('#installation-form').hide();
                        $('#env-instructions').show(); // A new div to show instructions
                        $('#env-content').text(response.dotenv_content);
                        // Add a button for the user to click once they've created the file
                        // This button would trigger another AJAX call to finalize
                    } else if (response.next_step === 'complete') {
                        // Installation finished
                        alert('Installation Successful! Redirecting...');
                        window.location.href = '<?= site_url("login") ?>'; // Redirect to login
                    } else {
                         // Handle other potential steps
                    }
                } else {
                    // Show specific error from backend
                    $('.alert-danger').text(response.message || 'An unknown error occurred.').show();
                    $('#install').prop('disabled', false).text('Install');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Show generic error
                $('.alert-danger').text('AJAX Error: ' + textStatus + ' - ' + errorThrown).show();
                $('#install').prop('disabled', false).text('Install');
            }
        });
    });

    // Add handler for the "I have created the .env file" button if using that approach
    $('#confirm-env-button').on('click', function() {
         // Make another AJAX call to a 'finalize' method in the controller
         // This method would create tables, user, flag file etc.
    });
});
