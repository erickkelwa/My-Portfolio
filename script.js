document.addEventListener('DOMContentLoaded', () => {
    // Navigation Toggle
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    const overlay = document.querySelector('.nav-overlay');
    const links = document.querySelectorAll('.nav-links li a');

    const toggleMenu = () => {
        navLinks.classList.toggle('active');
        hamburger.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.style.overflow = navLinks.classList.contains('active') ? 'hidden' : 'initial';
    };

    hamburger.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', toggleMenu);

    // Close menu when clicking a link
    links.forEach(link => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('active');
            hamburger.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = 'initial';
        });
    });

    // Header Scroll Effect
    const header = document.querySelector('header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            header.style.boxShadow = '0 10px 30px -10px rgba(2, 12, 27, 0.7)';
            header.style.background = 'rgba(10, 25, 47, 0.95)';
            header.style.padding = '0.5rem 0'; // Slight shrink
        } else {
            header.style.boxShadow = 'none';
            header.style.background = 'transparent';
            header.style.padding = '0';
        }
    });

    // Smooth Scroll for Safari/Older Browsers (optional as CSS handles this modernly)
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Simple Scroll Animation (Reveal on Scroll)
    const observerOptions = {
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);


    // Basic Fade-in Animation
    const sections = document.querySelectorAll('section');
    sections.forEach(section => {
        section.classList.add('fade-in-section');
        observer.observe(section);
    });

    // --- XAMPP CONTACT FORM INTEGRATION ---
    const portfolioContactForm = document.getElementById('contact-form');

    // Create Toast styling dynamically if not in CSS
    const style = document.createElement('style');
    style.textContent = `
        #toast-container {
            position: fixed;
            top: 2rem;
            right: 2rem;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .toast {
            background: #112240;
            color: #ccd6f6;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(2,12,27,0.5);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transform: translateX(120%);
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            min-width: 280px;
            border-left: 4px solid #64ffda;
        }
        .toast.show { transform: translateX(0); }
        .toast-success i { color: #64ffda; }
        .toast-error { border-left-color: #ff4d4d; }
        .toast-error i { color: #ff4d4d; }
    `;
    document.head.appendChild(style);

    // Toast Container
    const toastContainer = document.createElement('div');
    toastContainer.id = 'toast-container';
    document.body.appendChild(toastContainer);

    const showNotification = (message, type = 'success') => {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        toast.innerHTML = `<i class="fas ${icon}"></i><span>${message}</span>`;
        toastContainer.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, 3500);
    };

    if (portfolioContactForm) {
        const submitBtn = portfolioContactForm.querySelector('.btn');

        submitBtn.addEventListener('click', async (e) => {
            e.preventDefault();

            const nameInput = portfolioContactForm.querySelector('input[type="text"]');
            const emailInput = portfolioContactForm.querySelector('input[type="email"]');
            const messageInput = portfolioContactForm.querySelector('textarea');

            if (!nameInput.value || !emailInput.value || !messageInput.value) {
                showNotification('Please fill in all fields', 'error');
                return;
            }

            const formData = {
                name: nameInput.value,
                email: emailInput.value,
                message: messageInput.value
            };

            const originalBtnText = submitBtn.innerHTML;

            try {
                // --- CRITICAL CHECK ---
                if (window.location.protocol === 'file:') {
                    showNotification('ERROR: You must use http://localhost/ (XAMPP). PHP will not work if you double-click the file!', 'error');
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

                const response = await fetch('contact.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });

                // Try to parse JSON even if status is not 200
                const result = await response.json().catch(() => ({ status: 'error', message: 'PHP script failed to return valid JSON. Check your PHP logs.' }));

                if (response.ok && result.status === 'success') {
                    showNotification(result.message, 'success');
                    portfolioContactForm.reset();
                } else {
                    throw new Error(result.message || 'Unknown Server Error');
                }
            } catch (error) {
                // Determine if it is a network error or a PHP error
                const errorMsg = error.message.includes('fetch') ? 'Network Error: Is Apache running?' : error.message;
                showNotification(errorMsg, 'error');
                console.error('Portfolio Error:', error);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    }
});
