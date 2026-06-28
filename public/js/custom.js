// to get current year
function getYear() {
    var currentDate = new Date();
    var currentYear = currentDate.getFullYear();
    document.querySelector("#displayYear").innerHTML = currentYear;
}

getYear();


// Dynamic Category Filter with standard CSS flexbox collapse (prevents blank spaces)
$(window).on('load', function () {
    $('.filters_menu li').click(function () {
        if ($(this).hasClass('see-all-categories-btn')) {
            return;
        }

        $('.filters_menu li').removeClass('active');
        $(this).addClass('active');

        // Center the clicked category tab in the horizontal scroll container
        const container = document.querySelector('.filters_menu');
        if (container) {
            const activeEl = this;
            const offsetLeft = activeEl.offsetLeft - (container.clientWidth / 2) + (activeEl.clientWidth / 2);
            container.scrollTo({ left: offsetLeft, behavior: 'smooth' });
        }

        var data = $(this).attr('data-filter');
        if (data === '*') {
            $('.grid > div').show();
        } else {
            $('.grid > div').hide();
            $('.grid > div' + data).show();
        }
    });
});

// nice select
$(document).ready(function() {
    $('select').niceSelect();
  });

/** google_map js **/
function myMap() {
    var mapProp = {
        center: new google.maps.LatLng(40.712775, -74.005973),
        zoom: 18,
    };
    var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
}

// client section owl carousel
$(".client_owl-carousel").owlCarousel({
    loop: true,
    margin: 0,
    dots: false,
    nav: true,
    navText: [],
    autoplay: true,
    autoplayHoverPause: true,
    navText: [
        '<i class="fa fa-angle-left" aria-hidden="true"></i>',
        '<i class="fa fa-angle-right" aria-hidden="true"></i>'
    ],
    responsive: {
        0: {
            items: 1
        },
        768: {
            items: 2
        },
        1000: {
            items: 2
        }
    }
});


(function(){
  const els = document.querySelectorAll('.restaurant-card');
  const obs = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('in');
        obs.unobserve(e.target);
      }
    });
  }, { threshold: .12 });
  els.forEach(el => obs.observe(el));
})();

document.addEventListener('DOMContentLoaded', function() {
    // Universal robust dropdown system for both notifications and profile menu
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        if (toggle && menu) {
            // Toggle dropdown on click
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const isShown = menu.classList.contains('show');
                
                // Close all other dropdowns
                document.querySelectorAll('.dropdown-menu.show').forEach(openMenu => {
                    if (openMenu !== menu) {
                        openMenu.classList.remove('show');
                        openMenu.parentElement.classList.remove('show');
                    }
                });
                
                // Toggle current dropdown state
                if (isShown) {
                    menu.classList.remove('show');
                    dropdown.classList.remove('show');
                    toggle.setAttribute('aria-expanded', 'false');
                } else {
                    menu.classList.add('show');
                    dropdown.classList.add('show');
                    toggle.setAttribute('aria-expanded', 'true');
                }
            });
            
            // Handle dropdown item clicks to auto-close menu
            menu.addEventListener('click', function(e) {
                if (e.target.classList.contains('dropdown-item') && e.target.tagName !== 'BUTTON') {
                    menu.classList.remove('show');
                    dropdown.classList.remove('show');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
    
    // Close any open dropdowns when clicking outside (100% reliable on mobile touch viewports)
    document.addEventListener('click', function(e) {
        let clickedInsideDropdown = false;
        
        dropdowns.forEach(dropdown => {
            if (dropdown.contains(e.target)) {
                clickedInsideDropdown = true;
            }
        });
        
        if (!clickedInsideDropdown) {
            document.querySelectorAll('.dropdown-menu.show').forEach(openMenu => {
                openMenu.classList.remove('show');
                openMenu.parentElement.classList.remove('show');
                const toggle = openMenu.parentElement.querySelector('.dropdown-toggle');
                if (toggle) toggle.setAttribute('aria-expanded', 'false');
            });
        }
    });
    
    // Close dropdowns on Escape key press
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.dropdown-menu.show').forEach(openMenu => {
                openMenu.classList.remove('show');
                openMenu.parentElement.classList.remove('show');
                const toggle = openMenu.parentElement.querySelector('.dropdown-toggle');
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                    toggle.focus();
                }
            });
        }
    });
    
    // Alternative: If you have Bootstrap JavaScript loaded
    if (typeof bootstrap !== 'undefined') {
        // Bootstrap 5 syntax
        const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
        const dropdownList = [...dropdownElementList].map(dropdownToggleEl => 
            new bootstrap.Dropdown(dropdownToggleEl));
    } else if (typeof $ !== 'undefined' && $.fn.dropdown) {
        // Bootstrap 4 with jQuery
        $('.dropdown-toggle').dropdown();
    }

    // Make entire food card (.box) clickable to go to the food detail page
    if (typeof $ !== 'undefined') {
        $(document).on('click', '.food_section .box', function (e) {
            // Prevent redirecting if the user clicks the "Add to Cart" button or any elements inside it
            if ($(e.target).closest('.add-to-cart-btn').length > 0) {
                return;
            }
            
            var detailUrl = $(this).attr('data-detail-url');
            if (detailUrl) {
                window.location.href = detailUrl;
            }
        });
    }
});

