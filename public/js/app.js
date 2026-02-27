
document.addEventListener("DOMContentLoaded", function() {
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (() => {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        const forms = document.querySelectorAll('.needs-validation');
        // Loop over them and prevent submission
        Array.from(forms).forEach(form => {
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
    })();
});






