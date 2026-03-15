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
      console.warn('a selector is not defined');
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
export const init = new InitStack();

