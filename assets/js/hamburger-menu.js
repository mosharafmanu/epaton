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

        // Inject submenu toggle buttons into parent items
        $('.mobile-menu .menu-item-has-children > a').each(function() {
            var $link = $(this);
            if (!$link.siblings('.submenu-toggle').length) {
                $link.after('<button type="button" class="submenu-toggle" aria-label="Toggle submenu"><span></span></button>');
            }
        });

        // Handle submenu toggle - click on toggle button
        $('.mobile-menu').on('click', '.submenu-toggle', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $btn = $(this);
            var $parentLi = $btn.closest('.menu-item-has-children');
            var $submenu = $parentLi.children('.sub-menu');

            $parentLi.toggleClass('submenu-open');
            $submenu.slideToggle(300);

            // Close sibling submenus
            $parentLi.siblings('.menu-item-has-children').each(function() {
                var $sibling = $(this);
                $sibling.removeClass('submenu-open');
                $sibling.children('.sub-menu').slideUp(300);
            });
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
