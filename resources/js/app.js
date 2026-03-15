import './bootstrap';

import $ from 'jquery';
window.jQuery = window.$ = $;

import htmx from 'htmx.org';
window.htmx = htmx;

import * as bootstrap from 'bootstrap';

import '@tk-base/js/functions.js';

import '@tk-base/js/form.js';
import '@tk-base/js/table.js';

// global JS config object
window.appConfig = {
    isProd: false,
};



