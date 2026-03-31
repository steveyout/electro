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
        nav : false, // Set to false to remove arrows
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




    $(document).ready(function() {
        // 1. Quantity Controls (Global for +/- buttons)
        $(document).on('click', '.btn-plus, .btn-minus', function(e) {
            e.preventDefault();
            let isPlus = $(this).hasClass('btn-plus');
            let input = $(this).closest('.quantity').find('input');
            let currentVal = parseInt(input.val()) || 1;

            if (isPlus) {
                input.val(currentVal + 1);
            } else if (currentVal > 1) {
                input.val(currentVal - 1);
            }
        });

        // 2. Add to Cart Logic
        $(document).on('click', '.add-to-cart-btn', function(e) {
            e.preventDefault();

            // Safety check for AppConfig
            if (typeof window.AppConfig === 'undefined') {
                console.error("AppConfig missing from header!");
                return;
            }

            let productId = $(this).data('id');
            let btn = $(this);
            let originalContent = btn.html();

            // Dynamic Quantity Logic:
            // Look for an input named 'quantity' or with class 'qty-input' near the button,
            // otherwise check the whole page, otherwise default to 1.
            let qtyInput = btn.closest('.single-product').find('#qty-input');
            let quantity = qtyInput.length ? qtyInput.val() : 1;

            // Start Loading State
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: window.AppConfig.cartAddUrl + "/" + productId,
                method: "POST",
                data: {
                    _token: window.AppConfig.csrfToken,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Update Header Counters
                        $('.cart-count').text(response.cart_count);
                        $('.cart-total-display').html(response.cart_total);

                        // Show Success Feedback
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Added!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }

                        // Open Mini-Cart Drawer (Bootstrap 5)
                        let cartEl = document.getElementById('miniCartDrawer');
                        if (cartEl) {
                            let bsOffcanvas = bootstrap.Offcanvas.getInstance(cartEl) || new bootstrap.Offcanvas(cartEl);
                            bsOffcanvas.show();
                        }
                    }
                },
                error: function(xhr) {
                    let errorMsg = xhr.responseJSON ? xhr.responseJSON.message : "Something went wrong";
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
                    } else {
                        alert(errorMsg);
                    }
                },
                complete: function() {
                    // Reset Button
                    btn.prop('disabled', false).html(originalContent);
                }
            });
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

