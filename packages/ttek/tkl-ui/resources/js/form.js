import {init} from '@tkl-ui/js/initStack.js';

/**
 * @param form
 */
export function clearForm(form) {
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
 * Bootstrap 5 form validation
 */
init.add('.tk-form', function () {
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
});
