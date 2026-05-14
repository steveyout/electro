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
        dots: true,
        nav: false,
        autoplayHoverPause: true
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

            if (typeof window.AppConfig === 'undefined') {
                console.error("AppConfig missing from header!");
                return;
            }

            let productId = $(this).data('id');
            let btn = $(this);
            let originalContent = btn.html();

            let qtyInput = btn.closest('.single-product').find('#qty-input');
            let quantity = qtyInput.length ? qtyInput.val() : 1;

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
                        // 1. Update Header Counters
                        $('.cart-count').text(response.cart_count);

                        // 2. Update the Drawer Content Smartly
                        if (response.cart_html) {
                            // Create a virtual element to parse the returned HTML
                            let htmlData = $(response.cart_html);

                            // Extract just the items list
                            let newItems = htmlData.find('#mini-cart-list').html();
                            $('#mini-cart-list').html(newItems);

                            // Update the totals in the footer
                            let newTotal = htmlData.find('.cart-total-display').first().html();
                            $('.cart-total-display').html(newTotal);
                        }

                        // 3. Show Success Feedback
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

                        // 4. Open Mini-Cart Drawer
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
                    btn.prop('disabled', false).html(originalContent);
                }
            });
        });


        // 3. Remove from Cart Logic
        $(document).on('click', '.remove-cart-item', function(e) {
            e.preventDefault();

            let btn = $(this);
            let itemId = btn.data('id');
            let row = btn.closest('.cart-item-row');

            // Optional: Add a subtle loading state to the row
            row.css('opacity', '0.5');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                // Ensure this URL matches your Bagisto route for removing items
                url: "./checkout/cart/remove/" + itemId,
                method: "DELETE", // Bagisto standard is GET for remove, but check your routes
                data: {
                    _token: window.AppConfig.csrfToken,
                    id:itemId
                },
                success: function(response) {
                    if (response.status === 'success' || response.message) {
                        // 1. Update Header Counters
                        $('.cart-count').text(response.cart_count);
                        $('.cart-total-display').html(response.cart_total);

                        // 2. Smoothly remove the row or refresh the list
                        row.fadeOut(300, function() {
                            $(this).remove();

                            // If no items left, show the empty cart message
                            if ($('#mini-cart-list .cart-item-row').length === 0) {
                                location.reload(); // Hard refresh to show empty state or:
                                /* $('#mini-cart-list').html(`
                                    <div class="text-center py-5">
                                        <i class="fas fa-shopping-basket fa-4x text-light mb-3"></i>
                                        <p class="text-muted">Your cart is currently empty.</p>
                                        <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-dismiss="offcanvas">Start Shopping</button>
                                    </div>
                                `);
                                */
                            }
                        });

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Removed',
                                text: 'Item removed from cart',
                                timer: 1000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    }
                },
                error: function(xhr) {
                    row.css('opacity', '1');
                    btn.prop('disabled', false).html('<i class="fas fa-trash-alt"></i>');
                    console.error("Remove Error:", xhr.responseText);
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

