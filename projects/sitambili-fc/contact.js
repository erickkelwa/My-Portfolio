
/**
 * XAMPP DATABASE & NOTIFICATION SYSTEM for Sita Mbili FC
 * 
 * Features:
 * 1. MySQL Integration: Sends data to contact.php which stores it in PHP/MySQL.
 * 2. Premium Notifications: Beautiful toast alerts for user feedback.
 */

// Notification System (Toast)
const Notification = {
    show: (message, type = 'success') => {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;

        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        toast.innerHTML = `
            <i class="fas ${icon}"></i>
            <span>${message}</span>
        `;

        container.appendChild(toast);

        // Trigger animation
        setTimeout(() => toast.classList.add('show'), 10);

        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
};

// Form Handler
document.addEventListener('DOMContentLoaded', () => {
    const contactForm = document.getElementById('contactForm');

    if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Collect Form Data
            const formData = {
                name: contactForm.querySelector('input[placeholder*="John"]').value,
                email: contactForm.querySelector('input[type="email"]').value,
                subject: contactForm.querySelector('input[placeholder*="Inquiry"]').value,
                message: contactForm.querySelector('textarea').value
            };

            const submitBtn = contactForm.querySelector('.submit-btn');
            const originalText = submitBtn.innerHTML;

            try {
                // --- CRITICAL CHECK ---
                if (window.location.protocol === 'file:') {
                    Notification.show('ERROR: You must use http://localhost/ (XAMPP). Double-clicking the file won\'t work with PHP!', 'error');
                    return;
                }

                // UI Loading State
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving to XAMPP...';

                // SEND TO XAMPP (PHP Backend)
                const response = await fetch('contact.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                // Try to parse JSON even if status is not 200
                const result = await response.json().catch(() => ({ status: 'error', message: 'PHP script failed to return valid JSON. Check your PHP logs.' }));

                if (response.ok && result.status === 'success') {
                    // Success Notification
                    Notification.show(result.message, 'success');
                    // Reset Form
                    contactForm.reset();
                } else {
                    throw new Error(result.message || 'Unknown Server Error');
                }

            } catch (error) {
                const errorMsg = error.message.includes('fetch') ? 'Network Error: Is Apache running?' : error.message;
                Notification.show(errorMsg, 'error');
                console.error('Submission Error:', error);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }
});
