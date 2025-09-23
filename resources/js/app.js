// resources/js/app.js
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.css';
import intersect from '@alpinejs/intersect'

Alpine.plugin(intersect)

// window.Alpine = Alpine;
// Alpine.start();

// Optional: if you want global initialization
window.flatpickr = flatpickr;
document.addEventListener("DOMContentLoaded", function ()
{
    const container = document.querySelector('#flash-message-container');
    if (!container) return;

    const alerts = container.querySelectorAll('.alert');
    alerts.forEach(alert =>
    {
        setTimeout(() =>
        {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

 
