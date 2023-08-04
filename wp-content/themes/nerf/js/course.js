(function ($) {
    "use strict";

    $.extend($.apusThemeCore, {
        /**
         *  Initialize
         */
        course_init: function() {
            var self = this;
            
            self.coursesFilter();

            self.courseDetail();

            self.mixesFc();
            
            self.wishlistInit();


            var scrollspy_top = 0;
            var nav_height = 0;

            scrollspy_top = nav_height = self.courseChangeMarginTopAffix();
            
            if ($(window).width() >= 1200) {
                if ($('.main-sticky-header').length > 0) {
                    header_height = $('.main-sticky-header').outerHeight();
                    scrollspy_top = nav_height + header_height;
                }
            } else {
                header_height = 0;
                scrollspy_top = nav_height + header_height;
            }
            scrollspy_top = scrollspy_top + 10;

            setTimeout(function(){
                $('body').scrollspy({
                    target: "#ourse-tabs-spy",
                    offset: scrollspy_top
                });
            }, 100);

            $('.tutor-nav-item-tab a').on('click', function(e){
                var parent = $(this).closest('.tutor-nav');
                parent.find('.tutor-nav-item-tab a').removeClass('is-active');
                $(this).addClass('is-active');
            });
        },
        courseChangeMarginTopAffix: function() {
            var affix_height = 0;
            if ( $('#course-tabs-spy').length > 0 ) {
                affix_height = $('#course-tabs-spy').height();
            }
            return affix_height;
        },
        coursesFilter: function() {
            var self = this;

            $(document).on('change', '.filter-categories-widget form input, .filter-categories-widget form select', function(){
                $(this).closest('.filter-categories-widget form').trigger('submit');
            });
            $(document).on('change', '.filter-instructors-widget form input, .filter-instructors-widget form select', function(){
                $(this).closest('.filter-instructors-widget form').trigger('submit');
            });
            $(document).on('change', '.filter-price-widget form input, .filter-price-widget form select', function(){
                $(this).closest('.filter-price-widget form').trigger('submit');
            });
            $(document).on('change', '.filter-levels-widget form input, .filter-levels-widget form select', function(){
                $(this).closest('.filter-levels-widget form').trigger('submit');
            });
            $(document).on('change', '.filter-rating-widget form input, .filter-rating-widget form select', function(){
                $(this).closest('.filter-rating-widget form').trigger('submit');
            });

            $(document).on('click', '.filter-offcanvas-btn', function(e){
                e.preventDefault();
                $('.filter-offcanvas-sidebar').addClass('active');
                $('.filter-offcanvas-sidebar-overlay').addClass('active');
            });
            $(document).on('click', '.filter-top-btn', function(e){
                e.preventDefault();
                $('.tutor-filter-top-sidebar').toggle(300);
            });
            $(document).on('click', '.filter-offcanvas-sidebar-overlay', function(e){
                e.preventDefault();
                $('.filter-offcanvas-sidebar').removeClass('active');
                $('.filter-offcanvas-sidebar-overlay').removeClass('active');
            });


            $('select.tutor-filter-select').on('change', function(){
                $(this).closest('form').trigger('submit');
            });
        },
        courseDetail: function() {
            var self = this;
            
            if ( $('.comment-form-rating').length > 0 ) {

                var $star = $('.comment-form-rating .filled');
                var $review = $('#apus_input_rating');
                $star.find('li').on('mouseover', function () {
                    $(this).nextAll().addClass('active');
                    $(this).prevAll().removeClass('active');
                    $(this).removeClass('active');
                });
                $star.on('mouseout', function(){
                    var current = $review.val() - 1;
                    var current_e = $star.find('li').eq(current);

                    current_e.nextAll().addClass('active');
                    current_e.prevAll().removeClass('active');
                    current_e.removeClass('active');
                });
                $star.find('li').on('click', function () {
                    $(this).nextAll().addClass('active');
                    $(this).prevAll().removeClass('active');
                    $(this).removeClass('active');
                    
                    $review.val($(this).index() + 1);
                } );
            }

            $(document).on('click', '.share-wrapper .course-share', function(){
                $(this).parent().toggleClass('active');
            });
        },
        mixesFc: function() {
            var self = this;
            $( '.courses-ordering' ).on( 'change', 'select.orderby', function() {
                $( this ).closest( 'form' ).submit();
            });

            $('.apus-search-form-course .search-button, .apus-search-form-course .close-search, .apus-search-form-course .over-dark').on('click', function(){
                $(this).closest('.apus-search-form-course').find('.search-form-popup').toggle('100');
                $(this).closest('.apus-search-form-course').find('.over-dark').toggleClass('active');
            });
        },
        wishlistInit: function() {
            var self = this;
            // wishlist
            $( document ).on( "click", ".apus-wishlist-add", function( e ) {
                e.preventDefault();

                var $self = $(this);
                $self.addClass('loading');
                $.ajax({
                    url: nerf_course_opts.ajaxurl,
                    type:'POST',
                    data: {
                        action: 'nerf_add_wishlist',
                        post_id: $(this).data('id'),
                        security: nerf_course_opts.ajax_nonce,
                    },
                    dataType: 'json',
                }).done(function(reponse) {
                    if (reponse.status === 'success') {
                        $self.removeClass('apus-wishlist-add').addClass('apus-wishlist-added');
                        $self.find('.wishlist-text').html(reponse.text);
                    }
                    $self.removeClass('loading');
                });
            });
            
            // wishlist remove
            $( document ).on( "click", ".apus-wishlist-added", function( e ) {
                e.preventDefault();

                var $self = $(this);
                $self.addClass('loading');
                $.ajax({
                    url: nerf_course_opts.ajaxurl,
                    type:'POST',
                    data: {
                        action: 'nerf_remove_wishlist',
                        post_id: $(this).data('id'),
                        security: nerf_course_opts.ajax_nonce,
                    },
                    dataType: 'json',
                }).done(function(reponse) {
                    if (reponse.status === 'success') {
                        $self.removeClass('apus-wishlist-added').addClass('apus-wishlist-add');
                        $self.find('.wishlist-text').html(reponse.text);
                    }
                    $self.removeClass('loading');
                });
            });
            $( document ).on( "click", ".apus-wishlist-remove", function( e ) {
                e.preventDefault();

                var $self = $(this);
                var post_id = $(this).data('id');
                $self.addClass('loading');
                $.ajax({
                    url: nerf_course_opts.ajaxurl,
                    type:'POST',
                    data: {
                        action: 'nerf_remove_wishlist',
                        post_id: post_id,
                        security: nerf_course_opts.ajax_nonce,
                    },
                    dataType: 'json',
                }).done(function(reponse) {
                    $self.addClass('loading');
                    if (reponse.status === 'success') {
                        var parent = $('#wishlist-course-' + post_id).parent();
                        if ( $('.my-course-item-wrapper', parent).length <= 1 ) {
                            location.reload();
                        } else {
                            $('#wishlist-course-' + post_id).remove();
                        }
                    }
                });
            });
        }
    });

    $.apusThemeExtensions.course = $.apusThemeCore.course_init;
    
})(jQuery);

