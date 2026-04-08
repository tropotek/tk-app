import './bootstrap';

import $ from 'jquery';
window.jQuery = window.$ = $;

import htmx from 'htmx.org';
window.htmx = htmx;

import * as bootstrap from 'bootstrap';

import '@tkl-ui/js/functions.js';

import '@tkl-ui/js/form.js';
import '@tkl-ui/js/table.js';

// global JS config object
window.appConfig = {
    isProd: false,
};

