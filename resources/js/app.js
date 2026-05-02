import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// ── Reverb / Laravel Echo Setup ───────────────────────────────────────────────
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key:         import.meta.env.VITE_REVERB_APP_KEY,
    wsHost:      import.meta.env.VITE_REVERB_HOST,
    wsPort:      import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort:     import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS:    (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
});

// ── Toast Utility ─────────────────────────────────────────────────────────────
/**
 * @param {string} type       - 'approaching' | 'overdue' | 'done'
 * @param {string} motorCode
 * @param {string} scheduleDate
 */
function showReverbToast(type, motorCode, scheduleDate) {
    const container = document.getElementById('reverb-toasts');
    if (!container) return;

    const icons = {
        approaching: 'bi-clock-history',
        overdue:     'bi-exclamation-triangle-fill',
        done:        'bi-check-circle-fill',
    };

    const titles = {
        approaching: '⚠ Upcoming Schedule',
        overdue:     '🔴 Schedule Overdue!',
        done:        '✅ Maintenance Completed',
    };

    const texts = {
        approaching: `<strong>${motorCode}</strong><br>Scheduled: ${scheduleDate}`,
        overdue:     `<strong>${motorCode}</strong><br>Was due: ${scheduleDate}`,
        done:        `<strong>${motorCode}</strong><br>Inspected on: ${scheduleDate}`,
    };

    const toast = document.createElement('div');
    toast.className = `reverb-toast toast-${type}`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="reverb-toast__icon">
            <i class="bi ${icons[type] || 'bi-bell-fill'}"></i>
        </div>
        <div class="reverb-toast__body">
            <div class="reverb-toast__title">${titles[type] || 'Alert'}</div>
            <div class="reverb-toast__text">${texts[type] || ''}</div>
        </div>
        <button class="reverb-toast__close" aria-label="Close">
            <i class="bi bi-x-lg"></i>
        </button>
    `;

    // Close on button click
    toast.querySelector('.reverb-toast__close').addEventListener('click', () => {
        toast.style.animation = 'none';
        toast.style.opacity   = '0';
        toast.style.transform = 'translateX(40px)';
        toast.style.transition = 'opacity 0.25s, transform 0.25s';
        setTimeout(() => toast.remove(), 300);
    });

    container.appendChild(toast);

    // Auto-remove after 8 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.style.opacity   = '0';
            toast.style.transform = 'translateX(40px)';
            toast.style.transition = 'opacity 0.4s, transform 0.4s';
            setTimeout(() => toast.remove(), 400);
        }
    }, 8000);
}

// ── Subscribe to public schedule-alerts channel ───────────────────────────────
window.Echo.channel('schedule-alerts')
    .listen('.schedule.alert', (data) => {
        showReverbToast(
            data.type,
            data.motor_code,
            data.schedule_date
        );
    });

// ── Auto-dismiss Bootstrap alerts after 5s ────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.alert.alert-success, .alert.alert-danger').forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 5000);
    });
});
