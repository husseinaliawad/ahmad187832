document.addEventListener('DOMContentLoaded', () => {
    const scrollTopBtn = document.getElementById('scrollTopBtn');
    if (scrollTopBtn) {
        window.addEventListener('scroll', () => {
            const show = document.documentElement.scrollTop > 120 || document.body.scrollTop > 120;
            scrollTopBtn.style.display = show ? 'block' : 'none';
        });

        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    const darkModeToggle = document.getElementById('darkModeToggle');
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
    }

    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
            } else {
                localStorage.removeItem('darkMode');
            }
        });
    }

    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        const feedback = document.getElementById('contactFeedback');

        contactForm.addEventListener('submit', (event) => {
            event.preventDefault();

            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const message = document.getElementById('message').value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!name || !email || !message) {
                feedback.className = 'alert alert-danger';
                feedback.textContent = 'يرجى تعبئة جميع الحقول المطلوبة.';
                feedback.classList.remove('d-none');
                return;
            }

            if (!emailPattern.test(email)) {
                feedback.className = 'alert alert-warning';
                feedback.textContent = 'صيغة البريد الإلكتروني غير صحيحة.';
                feedback.classList.remove('d-none');
                return;
            }

            feedback.className = 'alert alert-success';
            feedback.textContent = 'تم إرسال الرسالة بنجاح. شكرًا لتواصلك معنا.';
            feedback.classList.remove('d-none');
            contactForm.reset();
        });
    }

    const shareButton = document.getElementById('shareEventBtn');
    if (shareButton) {
        shareButton.addEventListener('click', async () => {
            const title = shareButton.dataset.title || document.title;
            const url = shareButton.dataset.url || window.location.href;
            if (navigator.share) {
                try {
                    await navigator.share({ title, url });
                } catch (error) {
                    // User cancelled share action.
                }
            } else {
                navigator.clipboard.writeText(url);
                alert('تم نسخ رابط الفعالية للحافظة.');
            }
        });
    }
});
