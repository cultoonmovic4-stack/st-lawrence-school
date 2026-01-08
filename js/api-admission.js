// Admission Form API Integration

// Handle admission form submission
if (document.getElementById('admissionWizardForm')) {
    document.getElementById('admissionWizardForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('wizardSubmitBtn');
        const originalBtnText = submitBtn.innerHTML;
        
        // Collect all form data
        const formData = {
            // Student Information
            student_first_name: document.getElementById('firstName').value,
            student_last_name: document.getElementById('lastName').value,
            date_of_birth: document.getElementById('dateOfBirth').value,
            gender: document.getElementById('gender').value,
            nationality: document.getElementById('nationality').value,
            religion: document.getElementById('religion').value,
            class_to_join: document.getElementById('classToJoin').value,
            medical_conditions: document.getElementById('medicalConditions').value,
            
            // Parent Information
            parent_name: document.getElementById('parentName').value,
            parent_relationship: document.getElementById('relationship').value,
            parent_phone: document.getElementById('parentPhone').value,
            parent_email: document.getElementById('parentEmail').value,
            parent_occupation: document.getElementById('occupation').value,
            parent_nin: document.getElementById('nin').value,
            parent_address: document.getElementById('address').value,
            
            // Emergency Contact
            emergency_name: document.getElementById('emergencyName').value,
            emergency_relationship: document.getElementById('emergencyRelationship').value,
            emergency_phone: document.getElementById('emergencyPhone').value,
            emergency_email: document.getElementById('emergencyEmail').value,
            
            // Previous School
            previous_school: document.getElementById('previousSchool').value,
            last_class: document.getElementById('lastClass').value,
            reason_for_leaving: document.getElementById('reasonForLeaving').value,
            
            // Additional
            how_heard: document.getElementById('howHeard').value,
            comments: document.getElementById('comments').value
        };
        
        // Generate application ID
        const applicationId = 'APP' + Date.now();
        formData.application_id = applicationId;
        
        // Show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        
        try {
            // Submit form data
            const response = await API.post('/admissions/submit.php', formData);
            
            if (response.success) {
                // Upload documents if any
                await uploadAdmissionDocuments(response.data.id);
                
                // Show success message
                showAdmissionSuccess(applicationId);
                
                // Clear form and localStorage
                this.reset();
                localStorage.removeItem('admissionFormData');
            } else {
                showAdmissionError(response.message || 'Failed to submit application. Please try again.');
            }
        } catch (error) {
            showAdmissionError('Connection error. Please check your internet and try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });
}

// Upload admission documents
async function uploadAdmissionDocuments(applicationId) {
    const files = {
        birthCertificate: document.getElementById('birthCertificate').files[0],
        passportPhoto: document.getElementById('passportPhoto').files[0],
        previousReport: document.getElementById('previousReport').files[0]
    };
    
    for (const [key, file] of Object.entries(files)) {
        if (file) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('application_id', applicationId);
            formData.append('document_type', key);
            
            // Note: You'll need to create this endpoint
            // await fetch('../backend/api/admissions/upload.php', {
            //     method: 'POST',
            //     body: formData
            // });
        }
    }
}

// Show admission success
function showAdmissionSuccess(applicationId) {
    const message = `
        Your application ID is: <strong style="color: #0066cc; font-size: 20px; display: block; margin: 15px 0;">${applicationId}</strong>
        We have received your application and will review it shortly. You will receive a confirmation email at the address you provided.
    `;
    
    showSuccessAlert('Application Submitted Successfully!', message, () => {
        location.reload();
    });
}

// Show admission error
function showAdmissionError(message) {
    showErrorAlert('Submission Failed', message);
}

// Auto-save form data to localStorage
if (document.getElementById('admissionWizardForm')) {
    const form = document.getElementById('admissionWizardForm');
    const inputs = form.querySelectorAll('input, select, textarea');
    
    // Load saved data
    const savedData = localStorage.getItem('admissionFormData');
    if (savedData) {
        const data = JSON.parse(savedData);
        Object.keys(data).forEach(key => {
            const input = document.getElementById(key);
            if (input) input.value = data[key];
        });
    }
    
    // Save on change
    inputs.forEach(input => {
        input.addEventListener('change', () => {
            const formData = {};
            inputs.forEach(inp => {
                if (inp.id) formData[inp.id] = inp.value;
            });
            localStorage.setItem('admissionFormData', JSON.stringify(formData));
        });
    });
}
