// Custom Modern Alert System (SweetAlert-style)

// Show success alert
function showSuccessAlert(title, message, callback) {
    const alertHTML = `
        <div class="custom-alert-overlay" id="customAlert">
            <div class="custom-alert-box success-alert">
                <div class="alert-icon-wrapper">
                    <div class="alert-icon success-icon">
                        <div class="success-icon-circle">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
                <h2 class="alert-title">${title}</h2>
                <p class="alert-message">${message}</p>
                <button class="alert-btn success-btn" onclick="closeCustomAlert(${callback ? 'true' : 'false'})">
                    <span>OK</span>
                    <i class="fas fa-check"></i>
                </button>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHTML);
    
    // Store callback if provided
    if (callback) {
        window.customAlertCallback = callback;
    }
    
    // Animate in
    setTimeout(() => {
        document.getElementById('customAlert').classList.add('show');
    }, 10);
}

// Show error alert
function showErrorAlert(title, message) {
    const alertHTML = `
        <div class="custom-alert-overlay" id="customAlert">
            <div class="custom-alert-box error-alert">
                <div class="alert-icon-wrapper">
                    <div class="alert-icon error-icon">
                        <div class="error-icon-circle">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>
                </div>
                <h2 class="alert-title">${title}</h2>
                <p class="alert-message">${message}</p>
                <button class="alert-btn error-btn" onclick="closeCustomAlert()">
                    <span>Try Again</span>
                    <i class="fas fa-redo"></i>
                </button>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHTML);
    
    // Animate in
    setTimeout(() => {
        document.getElementById('customAlert').classList.add('show');
    }, 10);
}

// Show warning alert
function showWarningAlert(title, message) {
    const alertHTML = `
        <div class="custom-alert-overlay" id="customAlert">
            <div class="custom-alert-box warning-alert">
                <div class="alert-icon-wrapper">
                    <div class="alert-icon warning-icon">
                        <div class="warning-icon-circle">
                            <i class="fas fa-exclamation"></i>
                        </div>
                    </div>
                </div>
                <h2 class="alert-title">${title}</h2>
                <p class="alert-message">${message}</p>
                <button class="alert-btn warning-btn" onclick="closeCustomAlert()">
                    <span>OK</span>
                    <i class="fas fa-check"></i>
                </button>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHTML);
    
    // Animate in
    setTimeout(() => {
        document.getElementById('customAlert').classList.add('show');
    }, 10);
}

// Show info alert
function showInfoAlert(title, message) {
    const alertHTML = `
        <div class="custom-alert-overlay" id="customAlert">
            <div class="custom-alert-box info-alert">
                <div class="alert-icon-wrapper">
                    <div class="alert-icon info-icon">
                        <div class="info-icon-circle">
                            <i class="fas fa-info"></i>
                        </div>
                    </div>
                </div>
                <h2 class="alert-title">${title}</h2>
                <p class="alert-message">${message}</p>
                <button class="alert-btn info-btn" onclick="closeCustomAlert()">
                    <span>OK</span>
                    <i class="fas fa-check"></i>
                </button>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHTML);
    
    // Animate in
    setTimeout(() => {
        document.getElementById('customAlert').classList.add('show');
    }, 10);
}

// Show confirmation alert
function showConfirmAlert(title, message, onConfirm, onCancel) {
    const alertHTML = `
        <div class="custom-alert-overlay" id="customAlert">
            <div class="custom-alert-box confirm-alert">
                <div class="alert-icon-wrapper">
                    <div class="alert-icon confirm-icon">
                        <div class="confirm-icon-circle">
                            <i class="fas fa-question"></i>
                        </div>
                    </div>
                </div>
                <h2 class="alert-title">${title}</h2>
                <p class="alert-message">${message}</p>
                <div class="alert-buttons-group">
                    <button class="alert-btn cancel-btn" onclick="handleConfirmCancel()">
                        <span>Cancel</span>
                        <i class="fas fa-times"></i>
                    </button>
                    <button class="alert-btn confirm-btn" onclick="handleConfirmOk()">
                        <span>Confirm</span>
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHTML);
    
    // Store callbacks
    window.confirmAlertCallbacks = {
        onConfirm: onConfirm,
        onCancel: onCancel
    };
    
    // Animate in
    setTimeout(() => {
        document.getElementById('customAlert').classList.add('show');
    }, 10);
}

// Close alert
function closeCustomAlert(executeCallback = false) {
    const alert = document.getElementById('customAlert');
    if (alert) {
        alert.classList.remove('show');
        setTimeout(() => {
            alert.remove();
            
            // Execute callback if provided
            if (executeCallback && window.customAlertCallback) {
                window.customAlertCallback();
                window.customAlertCallback = null;
            }
        }, 300);
    }
}

// Handle confirm OK
function handleConfirmOk() {
    if (window.confirmAlertCallbacks && window.confirmAlertCallbacks.onConfirm) {
        window.confirmAlertCallbacks.onConfirm();
    }
    closeCustomAlert();
    window.confirmAlertCallbacks = null;
}

// Handle confirm Cancel
function handleConfirmCancel() {
    if (window.confirmAlertCallbacks && window.confirmAlertCallbacks.onCancel) {
        window.confirmAlertCallbacks.onCancel();
    }
    closeCustomAlert();
    window.confirmAlertCallbacks = null;
}

// Close on overlay click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('custom-alert-overlay')) {
        closeCustomAlert();
    }
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCustomAlert();
    }
});
