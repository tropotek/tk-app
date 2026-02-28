
$(function() {

    /**
     * TODO:
     *   We need a way to initialize elements:
     *     - When the page is loaded
     *     - When dynamic content is added/swapped to the page and needs js init
     *     - A function to manually init elements for non eventable cases
     */



    //initForms(document.body.querySelector('.tk-form'));
    initForms(document.body);

});


function initForms(elm)
{
    console.log('init forms');


    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (() => {
        'use strict'


        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        let forms;
        if (elm.matches('.needs-validation') === true) {
            forms = [elm];
        } else {
            forms = Array.from(elm.querySelectorAll('.needs-validation'));
        }

        // Loop over them and prevent submission
        forms.forEach(form => {
            form.addEventListener('submit', event => {

                const elements = form.querySelectorAll('input,textarea,select');
                elements.forEach(element => {
                    element.classList.remove('is-invalid');
                });
console.log('validity');
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
    })();

}


