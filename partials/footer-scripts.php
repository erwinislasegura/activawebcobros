<!-- Vendor js -->
<script src="assets/js/vendors.min.js"></script>

<!-- App js -->
<script src="assets/js/app.js"></script>

<script>
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
            navigator.serviceWorker.register('/sw.js');
        });
    }
</script>
