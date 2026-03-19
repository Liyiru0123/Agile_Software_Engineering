// Global loading indicator
document.addEventListener('DOMContentLoaded', function() {
    // Add loading state to all buttons
    const buttons = document.querySelectorAll('button[type="submit"], .btn-primary, .btn-danger');
    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            if (!this.disabled) {
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';
            }
        });
    });

    // Responsive sidebar (mobile devices)
    const mediaQuery = window.matchMedia('(max-width: 992px)');
    if (mediaQuery.matches) {
        // Add sidebar toggle button for mobile devices
        const topBar = document.querySelector('.main-content .bg-white');
        if (topBar) {
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'btn btn-outline-secondary d-md-none';
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
            toggleBtn.addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('show');
            });
            topBar.querySelector('.d-flex').prepend(toggleBtn);
        }
    }
});

// Universal AJAX request wrapper
function ajaxRequest(url, method = 'GET', data = {}) {
    const headers = {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        'Content-Type': 'application/json'
    };

    const options = {
        method: method,
        headers: headers
    };

    if (method !== 'GET') {
        options.body = JSON.stringify(data);
    }

    return fetch(url, options)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .catch(error => {
            console.error('AJAX Error:', error);
            alert('Operation failed, please check network or try again');
            throw error;
        });
}