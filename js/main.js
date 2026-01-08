(function ($) {
    "use strict";
    
    // Removed Dropdown on mouse hover for Bootstrap 5 compatibility
    
    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        dots: true,
        loop: true,
        items: 1
    });
    
  
      
  document.addEventListener("DOMContentLoaded", function() {
      const counters = document.querySelectorAll('.counter');
  
      counters.forEach(counter => {
          const target = +counter.getAttribute('data-target');
          const increment = target / 100;
  
          const updateCounter = () => {
              const current = +counter.innerText;
              if (current < target) {
                  counter.innerText = Math.ceil(current + increment);
                  setTimeout(updateCounter, 20);
              } else {
                  counter.innerText = target;
              }
          };
  
          updateCounter();
      });
});
});
