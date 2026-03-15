import $ from 'jquery';
import {init} from '@tk-base/js/initStack.js';

/**
 * Enable the table row "select all" feature
 */
init.add('.tk-table', function () {
    const table = this;
    $('.trs-head', this).on('change', function (e) {
        let trs = $(this);
        let name = trs.data('trsName');
        let list = $(`input[name^="${name}"]`, table);
        list.prop('checked', trs.prop('checked'));
    }).trigger('change');
});
