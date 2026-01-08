// Testimonials API Integration

// Load testimonials for homepage
async function loadTestimonials() {
    const container = document.getElementById('testimonialsContainer');
    if (!container) return;

    const response = await API.get('/testimonials/list.php');

    if (response.success && response.data && response.data.length > 0) {
        displayTestimonials(response.data);
    } else {
        container.innerHTML = '<p class="no-testimonials">No testimonials available</p>';
    }
}

// Display testimonials
function displayTestimonials(testimonials) {
    const container = document.getElementById('testimonialsContainer');
    if (!container) return;

    container.innerHTML = testimonials.map(testimonial => `
        <div class="testimonial-card" data-aos="fade-up">
            <div class="testimonial-header">
                ${testimonial.photo_url ? 
                    `<img src="../${testimonial.photo_url}" alt="${testimonial.parent_name}" class="testimonial-image">` :
                    `<div class="testimonial-avatar"><i class="fas fa-user"></i></div>`
                }
                <div class="testimonial-info">
                    <h4 class="testimonial-name">${testimonial.parent_name}</h4>
                    <p class="testimonial-relation">${testimonial.relationship} of ${testimonial.student_name}</p>
                </div>
            </div>
            <div class="testimonial-rating">
                ${generateStars(testimonial.rating)}
            </div>
            <p class="testimonial-text">"${testimonial.testimonial_text}"</p>
        </div>
    `).join('');
}

// Generate star rating
function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<i class="fas fa-star"></i>';
        } else {
            stars += '<i class="far fa-star"></i>';
        }
    }
    return stars;
}

// Initialize testimonials on homepage
if (window.location.pathname.includes('index-redesign.html')) {
    document.addEventListener('DOMContentLoaded', () => {
        loadTestimonials();
    });
}
