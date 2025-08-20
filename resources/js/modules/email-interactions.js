/**
 * Email Interaction Module
 *
 * Handles email-related frontend interactions like:
 * - Copy email links
 * - Email preview
 * - Send test emails
 */

class EmailInteractions {
    constructor() {
        this.init();
    }

    init() {
        this.setupCopyEmailLinks();
        this.setupTestEmailSending();
    }

    /**
     * Setup copy email link functionality
     */
    setupCopyEmailLinks() {
        document.addEventListener('click', async (e) => {
            if (e.target.matches('[data-action="copy-email-link"]')) {
                e.preventDefault();

                const link = e.target.dataset.link;

                try {
                    await navigator.clipboard.writeText(link);

                    // Show Bootstrap toast
                    this.showToast('Email link copied to clipboard!', 'success');

                    // Visual feedback using your existing button styles
                    const originalHTML = e.target.innerHTML;
                    e.target.innerHTML = '<i class="bi bi-check me-2"></i>Copied!';
                    e.target.classList.add('btn-success');
                    e.target.classList.remove('btn-outline-primary');

                    setTimeout(() => {
                        e.target.innerHTML = originalHTML;
                        e.target.classList.remove('btn-success');
                        e.target.classList.add('btn-outline-primary');
                    }, 2000);

                } catch (error) {
                    this.showToast('Failed to copy link', 'error');
                }
            }
        });
    }

    /**
     * Setup test email sending
     */
    setupTestEmailSending() {
        const testEmailBtns = document.querySelectorAll('[data-action="send-test-email"]');

        testEmailBtns.forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();

                const emailType = btn.dataset.emailType;
                const bookingId = btn.dataset.bookingId;

                // Show loading state using your existing styles
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
                btn.disabled = true;

                try {
                    const response = await fetch('/api/v1/test-email', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            email_type: emailType,
                            booking_id: bookingId
                        })
                    });

                    if (response.ok) {
                        this.showToast('Test email sent successfully!', 'success');
                    } else {
                        throw new Error('Failed to send test email');
                    }

                } catch (error) {
                    this.showToast('Failed to send test email', 'error');
                } finally {
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }
            });
        });
    }

    /**
     * Show Bootstrap toast notification
     */
    showToast(message, type = 'info') {
        // Create toast container if it doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        // Create toast using Bootstrap classes and your color scheme
        const toastId = 'toast-' + Date.now();
        const bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-primary';

        const toastHTML = `
            <div id="${toastId}" class="toast ${bgClass} text-white border-0" role="alert">
                <div class="toast-body d-flex align-items-center">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info-circle'} me-2"></i>
                    <span class="flex-grow-1">${message}</span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        // Show toast
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
        toast.show();

        // Remove from DOM after hiding
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }
}

// Export for use in main app
export default EmailInteractions;
