/* ----------------------------------------------------------------------------
 * @webSchedulr - Online Appointment Scheduler
 *
 * @author      N.N Cara <nilo.cara@frontend.co.za>
 * @copyright   Copyright (c) Nilesh Cara
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://webschedulr.co.za
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * App global namespace object.
 *
 * This script should be loaded before the other modules in order to define the global application namespace.
 */
window.App = (function () {
    function onAjaxError(event, jqXHR, textStatus, errorThrown) {
        console.error('Unexpected HTTP Error: ', jqXHR, textStatus, errorThrown);

        let response;

        try {
            response = JSON.parse(jqXHR.responseText); // JSON response
        } catch (error) {
            response = {message: jqXHR.responseText}; // String response
        }

        if (!response || !response.message) {
            return;
        }

        if (App.Utils.Message) {
            App.Utils.Message.show(lang('unexpected_issues'), lang('unexpected_issues_message'));

            $('<div/>', {
                'class': 'card',
                'html': [
                    $('<div/>', {
                        'class': 'card-body overflow-auto',
                        'html': response.message,
                    }),
                ],
            }).appendTo('#message-modal .modal-body');
        }
    }

    $(document).ajaxError(onAjaxError);

    $(function () {
        if (window.moment) {
            window.moment.locale(vars('language_code'));
        }
    });

    return {
        Components: {},
        Http: {},
        Layouts: {},
        Pages: {},
        Utils: {},
    };
})();
