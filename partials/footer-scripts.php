<!-- Vendor js -->
<script src="assets/js/vendors.min.js"></script>

<!-- App js -->
<script src="assets/js/app.js"></script>

<script>
    document.querySelectorAll('form').forEach((form) => {
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach((field) => {
            const id = field.getAttribute('id');
            if (!id) {
                return;
            }
            const label = form.querySelector(`label[for="${id}"]`);
            if (label && !label.dataset.requiredMarked) {
                label.insertAdjacentHTML('beforeend', ' <span class="text-danger" aria-hidden="true">*</span>');
                label.dataset.requiredMarked = '1';
            }
        });

        form.addEventListener('submit', (event) => {
            if (!form.checkValidity()) {
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid instanceof HTMLElement) {
                    firstInvalid.focus();
                }
                return;
            }

            const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitButton instanceof HTMLButtonElement || submitButton instanceof HTMLInputElement) {
                if (submitButton.dataset.submitting === '1') {
                    event.preventDefault();
                    return;
                }

                submitButton.dataset.submitting = '1';
                submitButton.disabled = true;
                if (submitButton instanceof HTMLButtonElement) {
                    submitButton.dataset.originalText = submitButton.innerHTML;
                    submitButton.innerHTML = 'Guardando...';
                } else {
                    submitButton.dataset.originalText = submitButton.value;
                    submitButton.value = 'Guardando...';
                }
            }
        });

        form.addEventListener('keydown', (event) => {
            if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's') {
                event.preventDefault();
                form.requestSubmit();
            }
        });
    });

    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) {
            return;
        }
        const message = form.getAttribute('data-confirm');
        if (message && !window.confirm(message)) {
            event.preventDefault();
        }
    });

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('sw.js');
        });
    }
</script>
