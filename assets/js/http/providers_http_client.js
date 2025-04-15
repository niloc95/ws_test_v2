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
 * Providers HTTP client.
 *
 * This module implements the providers related HTTP requests.
 */
App.Http.Providers = (function () {
    /**
     * Save (create or update) a provider.
     *
     * @param {Object} provider
     *
     * @return {Object}
     */
    function save(provider) {
        return provider.id ? update(provider) : store(provider);
    }

    /**
     * Create a provider.
     *
     * @param {Object} provider
     *
     * @return {Object}
     */
    function store(provider) {
        const url = App.Utils.Url.siteUrl('providers/store');

        const data = {
            csrf_token: vars('csrf_token'),
            provider: provider,
        };

        return $.post(url, data);
    }

    /**
     * Update a provider.
     *
     * @param {Object} provider
     *
     * @return {Object}
     */
    function update(provider) {
        const url = App.Utils.Url.siteUrl('providers/update');

        const data = {
            csrf_token: vars('csrf_token'),
            provider: provider,
        };

        return $.post(url, data);
    }

    /**
     * Delete a provider.
     *
     * @param {Number} providerId
     *
     * @return {Object}
     */
    function destroy(providerId) {
        const url = App.Utils.Url.siteUrl('providers/destroy');

        const data = {
            csrf_token: vars('csrf_token'),
            provider_id: providerId,
        };

        return $.post(url, data);
    }

    /**
     * Search providers by keyword.
     *
     * @param {String} keyword
     * @param {Number} [limit]
     * @param {Number} [offset]
     * @param {String} [orderBy]
     *
     * @return {Object}
     */
    function search(keyword, limit = null, offset = null, orderBy = null) {
        const url = App.Utils.Url.siteUrl('providers/search');

        const data = {
            csrf_token: vars('csrf_token'),
            keyword,
            limit,
            offset,
            order_by: orderBy || undefined,
        };

        return $.post(url, data);
    }

    /**
     * Find a provider.
     *
     * @param {Number} providerId
     *
     * @return {Object}
     */
    function find(providerId) {
        const url = App.Utils.Url.siteUrl('providers/find');

        const data = {
            csrf_token: vars('csrf_token'),
            provider_id: providerId,
        };

        return $.post(url, data);
    }

    return {
        save,
        store,
        update,
        destroy,
        search,
        find,
    };
})();
