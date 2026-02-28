/**
 *
 *
 *
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
        console.log(selector);
        // initially run the init callback on the document
        this._apply(document.body, selector, callback);

        // add to the callback stack
        if (!this.stack[selector]) this.stack[selector] = [];
        this.stack[selector].push(callback);
        console.log(this.stack);
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

    // TODO move to own file and add documentation
    // execute an init then push onto init stack for future execution
    // init.add('.tk-form', function(){ console.log('form 1 init called'); });
    // init.add('.tk-form', function(){ console.log('form 2 init called'); });
    // init.add('.tk-table', function(){ console.log('table 1 init called'); });
    //
    //init.execute(document.body); // execute all inits on document
    //init.execute(document.body, '.tk-form'); // execute all inits for form selector on document

}



// global javascript init stack
const init = new InitStack();
// global JS config object
let tkConfig = {};

$(function() {

    /**
     * TODO:
     *   We need a way to initialize elements:
     *     - When the page is loaded
     *     - When dynamic content is added/swapped to the page and needs js init
     *     - A function to manually init elements for non eventable cases
     */

    // bootstrap form validation
    init.add('.tk-form', function(){
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (() => {
            'use strict'

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
        })();
    });

});
