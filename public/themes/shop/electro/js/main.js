(function ($) {
    "use strict";

    // 1. GLOBAL SPINNER CONTROL
    // Hide spinner when everything (images/scripts) is fully loaded
    $(window).on('load', function () {
        if ($('#spinner').length > 0) {
            $('#spinner').removeClass('show');
        }
    });

    // Fallback: Force hide spinner after 5 seconds if it gets stuck
    setTimeout(function () {
        if ($('#spinner').hasClass('show')) {
            $('#spinner').removeClass('show');
        }
    }, 5000);

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Initialize WOW.js
    if (typeof WOW !== 'undefined') {
        new WOW().init();
    }

    // Sticky Navbar
    $(window).scroll(function () {
        if ($(this).scrollTop() > 45) {
            $('.nav-bar').addClass('sticky-top shadow-sm');
        } else {
            $('.nav-bar').removeClass('sticky-top shadow-sm');
        }
    });

    // Hero Header Carousel
    $(".header-carousel").owlCarousel({
        items: 1,
        autoplay: true,
        smartSpeed: 2000,
        loop: true,
        dots: false,
        nav : true,
        navText : ['<i class="bi bi-arrow-left"></i>', '<i class="bi bi-arrow-right"></i>']
    });

    // Main Product Carousel - 5 items (Desktop) / 2 items (Mobile)
    $(".productList-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        dots: false,
        loop: true,
        margin: 15,
        nav : true,
        navText : ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
        responsive: {
            0:{
                items: 2, // Matches col-6
                margin: 10
            },
            768:{
                items: 3,
                margin: 15
            },
            992:{
                items: 5, // Matches col-lg-2-4
                margin: 16
            }
        }
    });

    // Add to Cart Logic
    $(document).on('click', ".add-cart", function (e) {
        e.preventDefault();
        let button = $(this);
        let id = button.attr("id");
        let cartBadgeElement = $('.cart-badge').first();
        let cartBadge = parseInt(cartBadgeElement.text()) || 0;

        if (!id) return;

        $.ajax({
            url: "./api/v1/products?id=" + id + "&sort=id",
            type: "GET",
            dataType: "json",
            beforeSend: function () {
                // Preserve button width while loading
                button.css('width', button.outerWidth());
                button.html('<span class="spinner-border spinner-border-sm"></span>').attr('disabled', true);
            },
            success: function(response) {
                button.html('<i class="fas fa-check"></i>').addClass('btn-success').removeClass('btn-primary');

                // Reset button after 2 seconds
                setTimeout(function() {
                    button.html('Add to cart').removeClass('btn-success').addClass('btn-primary').attr('disabled', false).css('width', '');
                }, 2000);

                let newTotal = cartBadge + 1;
                $('.cart-badge, .cart-total').text(newTotal);
            },
            error: function() {
                button.html('Add to cart').attr('disabled', false).css('width', '');
            }
        });
    });

    // Back to top
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) { $('.back-to-top').fadeIn('slow'); }
        else { $('.back-to-top').fadeOut('slow'); }
    });

    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });

})(jQuery);

