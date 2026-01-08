// Events API Integration

// Load upcoming events for homepage
async function loadUpcomingEvents() {
    const container = document.getElementById('eventsContainer');
    if (!container) return;

    const response = await API.get('/events/list.php?upcoming=true');

    if (response.success && response.data && response.data.length > 0) {
        displayEvents(response.data.slice(0, 4)); // Show only first 4 events
    } else {
        container.innerHTML = '<p class="no-events">No upcoming events at the moment</p>';
    }
}

// Display events
function displayEvents(events) {
    const container = document.getElementById('eventsContainer');
    if (!container) return;

    container.innerHTML = events.map(event => {
        const eventDate = new Date(event.event_date);
        const day = eventDate.getDate();
        const month = eventDate.toLocaleString('default', { month: 'short' });
        
        return `
            <div class="event-item" data-aos="fade-right">
                <div class="event-date">
                    <span class="event-day">${day}</span>
                    <span class="event-month">${month}</span>
                </div>
                <div class="event-details">
                    <h4 class="event-title">${event.title}</h4>
                    ${event.location ? `<p class="event-location"><i class="fas fa-map-marker-alt"></i> ${event.location}</p>` : ''}
                    ${event.event_time ? `<p class="event-time"><i class="fas fa-clock"></i> ${event.event_time}</p>` : ''}
                </div>
                <span class="event-category ${event.category.toLowerCase()}">${event.category}</span>
            </div>
        `;
    }).join('');
}

// Initialize events on homepage
if (window.location.pathname.includes('index-redesign.html')) {
    document.addEventListener('DOMContentLoaded', () => {
        loadUpcomingEvents();
    });
}
