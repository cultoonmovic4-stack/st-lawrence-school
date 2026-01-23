// Contact Form API Integration

// Handle contact form submission
if (document.getElementById('contactForm')) {
    document.getElementById('contactForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        
        // Get form data
        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone')?.value || '',
            subject: document.getElementById('subject').value,
            message: document.getElementById('message').value,
            form_type: 'Contact'
        };
        
        // Validate
        if (!formData.name || !formData.email || !formData.subject || !formData.message) {
            showFormError('Please fill in all required fields');
            return;
        }
        
        // Show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        
        try {
            console.log('Submitting contact form to:', API_BASE_URL + '/public/submit_contact.php');
            console.log('Form data:', formData);
            const response = await API.post('/public/submit_contact.php', formData);
            console.log('Response:', response);
            
            if (response.success) {
                showFormSuccess('Thank you! Your message has been sent successfully. We will get back to you soon.');
                this.reset();
            } else {
                showFormError(response.message || 'Failed to send message. Please try again.');
            }
        } catch (error) {
            showFormError('Connection error. Please check your internet and try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });
}

// Show form success message
function showFormSuccess(message) {
    showSuccessAlert('Success!', message);
}

// Show form error message
function showFormError(message) {
    showErrorAlert('Oops!', message);
}
