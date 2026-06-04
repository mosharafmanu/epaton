/**
 * Hamburger Menu Toggle
 * Handles mobile menu open/close functionality
 *
 * @package epaton
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        /**
         * Open hamburger menu
         */
        function openMenu() {
            $('.menu-trigger').addClass('isOpen');
            $('.hamburger-overlay').addClass('isOpen');
            $('.hamburger-wrapper').addClass('isOpen');
            $('body').addClass('no-scroll');
        }

        /**
         * Close hamburger menu
         * @param {boolean} resetSubmenus - Whether to reset submenu states
         */
        function closeMenu(resetSubmenus) {
            $('.menu-trigger').removeClass('isOpen');
            $('.hamburger-overlay').removeClass('isOpen');
            $('.hamburger-wrapper').removeClass('isOpen');
            $('body').removeClass('no-scroll');

            // Reset submenu states if needed (on resize)
            if (resetSubmenus) {
                $('.mobile-menu .submenu-open').removeClass('submenu-open')
                    .find('.sub-menu').removeAttr('style');
            }
        }

        // Toggle menu - Menu trigger click
        $('.menu-trigger').on('click', function(e) {
            e.preventDefault();

            if ($('.hamburger-wrapper').hasClass('isOpen')) {
                closeMenu(false);
            } else {
                openMenu();
            }
        });

        // Close menu when clicking on overlay
        $('.hamburger-overlay').on('click', function() {
            closeMenu(false);
        });

        // Handle submenu toggle for mobile menu - clicking on parent link with submenu
        $('.mobile-menu .menu-item-has-children > a').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $parentLi = $(this).parent();
            var $submenu = $parentLi.find('> .sub-menu');

            // Toggle current submenu
            $parentLi.toggleClass('submenu-open');
            $submenu.slideToggle(300);

            // Close other submenus at the same level
            $parentLi.siblings('.menu-item-has-children').removeClass('submenu-open')
                .find('> .sub-menu').slideUp(300);
        });

        // Close menu on ESC key press
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('.hamburger-wrapper').hasClass('isOpen')) {
                closeMenu(false);
            }
        });

        // Close menu on window resize if open (when switching to desktop)
        var resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Match the CSS breakpoint: max-width: 1024px
                if ($(window).width() > 1024 && $('.hamburger-wrapper').hasClass('isOpen')) {
                    closeMenu(true); // Reset submenus on resize
                }
            }, 250);
        });

    });

})(jQuery);
