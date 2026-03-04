/**
 * TODO move initStack to own file and add document...
 *   execute an init then push onto init stack for future execution
 *   init.add('.tk-form', function(){ console.log('form 1 init called'); });
 *   init.add('.tk-form', function(){ console.log('form 2 init called'); });
 *   init.add('.tk-table', function(){ console.log('table 1 init called'); });
 *   init.execute(document.body); // execute all inits on document
 *   init.execute(document.body, '.tk-form'); // execute all inits for form selector on document
 *
 */
class InitStack {
    constructor() {
        this.stack = {};
    }

    add(selector, callback) {
        if (typeof callback !== 'function') {
            console.warn('init is not a function');
            return;
        }
        if (!selector) {
            console.warn('selector not defined');
            return;
        }
        // initially run the init callback on the document
        this._apply(document.body, selector, callback);

        // add to the callback stack
        if (!this.stack[selector]) this.stack[selector] = [];
        this.stack[selector].push(callback);

        return callback;
    }

    remove(selector) {
        delete this.stack[selector];
    }

    execute(target, selector) {
        if (!target) target = document.body;
        if (!(target instanceof Element)) {
            console.warn('target is not a Element');
            return;
        }

        if (!selector) {
            // execute all inits
            for(const key in this.stack) {
                const inits = this.stack[key] ?? [];
                for (let i = 0; i < inits.length; i++) {
                    this._apply(target, key, inits[i]);
                }
            }
        } else {
            // execute selected inits
            const inits = this.stack[selector] ?? [];
            for (let i = 0; i < inits.length; i++) {
                this._apply(target, selector, inits[i]);
            }
        }
    }

    getElements(target, selector) {
        // get children or use target if matches the selector
        if (target.matches(selector)) {
           return [target];
        }
        return Array.from(target.querySelectorAll(selector));
    }

    _apply(target, selector, init) {
        if (!target) target = document.body;
        if (!(target instanceof Element)) {
            console.warn('target is not a Element');
            return;
        }
        // apply all inits for the selector
        const elms = this.getElements(target, selector);
        for (let i = 0; i < elms.length; i++) {
            init.apply(elms[i], [target, selector]);
        }
    }
}

// global javascript init stack
const init = new InitStack();

// global JS config object
let tkConfig = {};



// Var dump function for debugging
function vd() {
    if (tkConfig.isProd) return;
    for (let k in arguments) console.log(arguments[k]);
}

function copyToClipboard(text) {
    if (navigator.clipboard) {
        // Modern versions of Chromium browsers, Firefox, etc.
        navigator.clipboard.writeText(text);
    } else if (window.clipboardData) {
        // Internet Explorer.
        window.clipboardData.setData('Text', text);
    } else {
        // Fallback method using Textarea.
        var textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.top = '-999999px';
        textArea.style.left = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            if (!document.execCommand('copy')) {
                console.warn('Could not copy text to clipboard');
            }
        } catch (error) {
            console.warn('Could not copy text to clipboard');
        }
        document.body.removeChild(textArea);
    }
}

function clearForm(form) {
    $(':input', form).each(function () {
        var type = this.type;
        var tag = this.tagName.toLowerCase(); // normalize case
        if (type === 'text' || type === 'password' || tag === 'textarea')
            this.value = "";
        else if (type === 'checkbox' || type === 'radio')
            this.checked = false;
        else if (tag === 'select')
            this.selectedIndex = 1;
    });
}


/**
 *
 */
const forms = function () {
    "use strict";

    /**
     *
     */
    let initFormValidation = function () {
        if (this.matches('.needs-validation') !== true) return;
        // Loop over them and prevent submission
        const form = this;
        form.addEventListener('submit', event => {
            const elements = form.querySelectorAll('input,textarea,select');
            elements.forEach(element => {
                element.classList.remove('is-invalid');
            });

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();

                form.classList.add('was-validated');
                const invalidElements = form.querySelectorAll(':invalid');
                // Add the specified class to each invalid element
                invalidElements.forEach(element => {
                    element.classList.add('is-invalid');
                });
            }
        }, false);
    };


    return {
        initFormValidation: initFormValidation,
    }
}();


/**
 *
 */
const tables = function () {
    "use strict";

    /**
     *
     */
    let initCheckboxSelect = function () {
        const table = this;
        $('.trs-head', this).on('change', function (e) {
            let trs = $(this);
            let name = trs.data('trsName');
            let list = $(`input[name^="${name}"]`, table);
            list.prop('checked', trs.prop('checked'));
        }).trigger('change');
    };


    return {
        initCheckboxSelect: initCheckboxSelect,
    }
}();


$(function() {
    // enable javascript functions
    // adding functions to the initStack if they are to be used with dynamic content
    init.add('.tk-form', forms.initFormValidation);
    init.add('.tk-table', tables.initCheckboxSelect);

});
