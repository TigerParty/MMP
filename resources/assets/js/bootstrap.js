/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */
import Popper from 'popper.js/dist/umd/popper.js';

try {
    window.$ = window.jQuery = require('jquery');
    window.Popper = Popper;
    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
window.moment = require('moment');
window._ = require('lodash');

/**
 * Global variables definition
 */
let editPerm = document.head.querySelector('meta[name="edit-permission"]');
if (editPerm) {
    window.editPermission = Boolean(Number(editPerm.content));
}
