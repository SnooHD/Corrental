(function($){
    "use strict";
    
    $(window).on('elementor/frontend/init', function () {

        // Product Ajax Filter
        elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_ajax_filter.default', function() {
            ovabrwCardImages();
            ovabrwPagination();

            $('.ovabrw-product-ajax-filter .categories-filter .item-term').on( 'click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                var ajaxFilter = $(this).closest('.ovabrw-product-ajax-filter');

                ajaxFilter.find('.item-term').removeClass('active');
                $(this).addClass('active');
                ajaxFilter.find('.page-numbers').removeClass('current');
                ajaxFilter.find('.page-numbers[data-paged="1"]').addClass('current');

                ovabrwAjaxFilter(ajaxFilter);
            });

            // Card images
            function ovabrwCardImages() {
                $('.ovabrw-gallery-popup .ovabrw-gallery-slideshow').each( function() {
                    var that    = $(this);
                    var options = that.data('options') ? that.data('options') : {};
                    
                    that.owlCarousel({
                        autoWidth: options.autoWidth,
                        margin: options.margin,
                        items: options.items,
                        loop: options.loop,
                        autoplay: options.autoplay,
                        autoplayTimeout: options.autoplayTimeout,
                        center: options.center,
                        lazyLoad: options.lazyLoad,
                        nav: options.nav,
                        dots: options.dots,
                        autoplayHoverPause: options.autoplayHoverPause,
                        slideBy: options.slideBy,
                        smartSpeed: options.smartSpeed,
                        rtl: options.rtl,
                        navText:[
                            '<i aria-hidden="true" class="'+ options.nav_left +'"></i>',
                            '<i aria-hidden="true" class="'+ options.nav_right +'"></i>'
                        ],
                        responsive: options.responsive,
                    });

                    that.find('.gallery-fancybox').off('click').on('click', function() {
                        var index = $(this).data('index');
                        var gallery_data = $(this).closest('.ovabrw-gallery-popup').find('.ovabrw-data-gallery').data('gallery');

                        Fancybox.show(gallery_data, {
                            Image: {
                                Panzoom: {
                                    zoomFriction: 0.7,
                                    maxScale: function () {
                                        return 3;
                                    },
                                },
                            },
                            startIndex: index,
                        });
                    });
                });
            }
            // End
            
            // Ajax Filter
            function ovabrwAjaxFilter( that = null ) {
                if ( that ) {
                    var loader = that.find('.ovabrw-loader');
                    loader.show();

                    var termID          = that.find('.categories-filter .item-term.active').data('term-id');
                    var inputData       = that.find('input[name="ovabrw-data-ajax-filter"]');
                    var template        = inputData.data('template');
                    var postsPerPage    = inputData.data('posts-per-page');
                    var orderby         = inputData.data('orderby');
                    var order           = inputData.data('order');
                    var pagination      = inputData.data('pagination');
                    var paged           = that.find('.page-numbers.current').data('paged');

                    $.ajax({
                        url: ajax_object.ajax_url,
                        type: 'POST',
                        data: ({
                            action: 'ovabrw_product_ajax_filter',
                            term_id: termID,
                            template: template,
                            posts_per_page: postsPerPage,
                            orderby: orderby,
                            order: order,
                            pagination: pagination,
                            paged: paged,
                        }),
                        success: function(response) {
                            if ( response ) {
                                response = JSON.parse( response );

                                that.find('.ovabrw-result .ovabrw-product-list').empty();
                                that.find('.ovabrw-result .ovabrw-product-list').append(response.result).fadeOut(300).fadeIn(500);

                                that.find('.ovabrw-pagination').empty();
                                that.find('.ovabrw-pagination').append(response.pagination).fadeOut(300).fadeIn(500);

                                ovabrwCardImages();
                                ovabrwPagination();
                                loader.hide();
                            }
                        }
                    });
                }
            }

            // Pagination
            function ovabrwPagination() {
                $('.ovabrw-product-ajax-filter .ovabrw-pagination .page-numbers').on( 'click', function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    var ajaxFilter = $(this).closest('.ovabrw-product-ajax-filter');

                    ajaxFilter.find('.page-numbers').removeClass('current');
                    $(this).addClass('current');

                    ovabrwAjaxFilter(ajaxFilter);
                });
            }
        });
        // End
        
        // Product Filter
        elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_filter.default', function() {
            // Card images
            $('.ovabrw-product-filter .ovabrw-product-filter-slide').each( function() {
                var that    = $(this);
                var options = that.data('options') ? that.data('options') : {};
                
                that.owlCarousel({
                    autoWidth: options.autoWidth,
                    margin: options.margin,
                    items: options.items,
                    loop: options.loop,
                    autoplay: options.autoplay,
                    autoplayTimeout: options.autoplayTimeout,
                    center: options.center,
                    lazyLoad: options.lazyLoad,
                    nav: options.nav,
                    dots: options.dots,
                    autoplayHoverPause: options.autoplayHoverPause,
                    slideBy: options.slideBy,
                    smartSpeed: options.smartSpeed,
                    rtl: options.rtl,
                    navText:[
                        '<i aria-hidden="true" class="'+ options.nav_left +'"></i>',
                        '<i aria-hidden="true" class="'+ options.nav_right +'"></i>'
                    ],
                    responsive: options.responsive,
                });

                that.find('.gallery-fancybox').off('click').on('click', function() {
                    var index = $(this).data('index');
                    var gallery_data = $(this).closest('.ovabrw-gallery-popup').find('.ovabrw-data-gallery').data('gallery');

                    Fancybox.show(gallery_data, {
                        Image: {
                            Panzoom: {
                                zoomFriction: 0.7,
                                maxScale: function () {
                                    return 3;
                                },
                            },
                        },
                        startIndex: index,
                    });
                });
            });
            // End
        });
        // End
        
        // Search Map
        elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_search_map.default', function() {
            window.BrwFrontendJS.ova_search_map();
        });
        // End
        
        // Product Image
        elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_images.default', function() {
            window.BrwFrontendJS.ova_slide_image();
        });
        // End
        
        // Product Calendar
        elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_calendar.default', function() {
            window.BrwCalendar();
        });
        // End
        
        // Product Form
        elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_booking_form.default', function() {
            window.BrwFrontendJS.ova_modern_product();
        });
        // End
        
        // Product Map
        elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_map.default', function() {
            if ( $('.ovabrw-product-map').length > 0 ) {
                $('.ovabrw-product-map').each(function() {
                    var that        = $(this);
                    var input       = that.find('#pac-input')[0];
                    var address     = that.find('.ovabrw-data-product-map');
                    var latitude    = address.attr('latitude');
                    var longitude   = address.attr('longitude');
                    var zoom        = address.data('zoom');

                    if ( ! zoom ) zoom = 17;
                    
                    if ( typeof google !== 'undefined' && latitude && longitude ) {
                        var map = new google.maps.Map( $('#ovabrw-show-map')[0], {
                            center: {
                                lat: parseFloat(latitude),
                                lng: parseFloat(longitude)
                            },
                            zoom: zoom,
                            gestureHandling: 'cooperative',
                        });

                        var autocomplete = new google.maps.places.Autocomplete(input);

                        autocomplete.bindTo('bounds', map);

                        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                        var mapIWcontent = $('#pac-input').val();
                        var infowindow = new google.maps.InfoWindow({
                           content: mapIWcontent,
                        });

                        var marker = new google.maps.Marker({
                           map: map,
                           position: map.getCenter(),
                        });

                        marker.addListener('click', function() {
                           infowindow.open(map, marker);
                        });
                    }
                });
            }
        });
        // End
        
    });
})(jQuery);