import * as bootstrap from 'bootstrap';

export default function confirmModal(Alpine) {

    Alpine.directive('confirm', (el, { expression }, { evaluate }) => {

        el.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopImmediatePropagation();

            let message = expression;

            try {
                message = evaluate(expression);
            } catch (error) {
                // treat the raw attribute value as plain text if evaluation fails
            }

            if (typeof message !== 'string') {
                message = String(message);
            }

            const confirmed = await window.bootstrapConfirm(message);

            if (confirmed) {
                el.dispatchEvent(new CustomEvent('confirmed-action', { bubbles: true }));
            }
        });
    });

    window.bootstrapConfirm = function (message) {
        return new Promise((resolve) => {
            const modalHtml = `
                <div class="modal fade" id="alpineConfirmModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body">${message}</div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="confirmBtn">Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>`;

            document.body.insertAdjacentHTML('beforeend', modalHtml);

            const modalEl = document.getElementById('alpineConfirmModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            modalEl.querySelector('#confirmBtn').addEventListener('click', () => {
                modal.hide();
                resolve(true);
            });

            modalEl.addEventListener('hidden.bs.modal', () => {
                modalEl.remove();
                resolve(false);
            }, { once: true });
        });
    };
}
