/**
 * Carousel and UI initialization
 *
 * Trip showcase, testimonials, why choose us, latest news, FAQ accordion,
 * and video autoplay behaviors
 *
 * @package epaton
 */

// Dynamic header offset calculation
jQuery(document).ready(function($) {
	function updateHeaderOffset() {
		const header = $('.site-header');
		if (header.length) {
			const isStatic = header.hasClass('is-static');
			const headerHeight = header.outerHeight();
			const isMobile = $(window).width() <= 767;
			const topGap = isStatic ? 0 : (isMobile ? 30 : 60);
			document.documentElement.style.setProperty('--header-offset', (headerHeight + topGap) + 'px');
		}
	}

	// Update on load
	updateHeaderOffset();

	// Update on resize (header height might change)
	let headerResizeTimer;
	$(window).on('resize', function() {
		clearTimeout(headerResizeTimer);
		headerResizeTimer = setTimeout(updateHeaderOffset, 100);
	});

	// Update after fonts load (header height might change)
	$(window).on('load', function() {
		setTimeout(updateHeaderOffset, 200);
	});
});

// Header Scroll Class
jQuery(document).ready(function($) {
	const header = $('.site-header');

	// Only run if header exists
	if (!header.length) {
		return;
	}

	const scrollThreshold = 30; // Pixels to scroll before changing header

	function handleHeaderScroll() {
		const scrollTop = $(window).scrollTop();

		if (scrollTop > scrollThreshold) {
			header.addClass('is-scrolled');
		} else {
			header.removeClass('is-scrolled');
		}
	}

	// Check on load
	handleHeaderScroll();

	// Check on scroll
	$(window).on('scroll', function() {
		handleHeaderScroll();
	});
});


// Global stage padding right - Add classes to elements with class="js-stage-padding"
jQuery(document).ready(function($) {
	function toggleStagePaddingClasses() {
		const elements = $('.js-stage-padding');
		if ($(window).width() <= 767) {
			elements.addClass('stagePaddingRight itemMargin');
		} else {
			elements.removeClass('stagePaddingRight itemMargin');
		}
	}

	// Initial check
	toggleStagePaddingClasses();

	// Update on resize
	let stagePaddingTimer;
	$(window).on('resize', function() {
		clearTimeout(stagePaddingTimer);
		stagePaddingTimer = setTimeout(toggleStagePaddingClasses, 100);
	});
});


// Stage Padding Carousel (Mobile Only)
jQuery(document).ready(function($) {

	/**
	 * Set equal height for all cards in carousel
	 */
	function setEqualHeight() {
		if (window.innerWidth < 768) {
			$('.js-stage-padding').each(function() {
				const $carousel = $(this);
				let maxHeight = 0;

				// Find cards - supports both .card and .icon-card classes
				const $cards = $carousel.find('.card, .icon-card, .product-card');

				// Reset heights first
				$cards.css('height', '');

				// Calculate max height
				$cards.each(function() {
					maxHeight = Math.max(maxHeight, $(this).outerHeight());
				});

				// Apply equal height
				$cards.css('height', maxHeight + 'px');
			});
		} else {
			// Reset heights on desktop
			$('.js-stage-padding .card, .js-stage-padding .icon-card, .js-stage-padding .product-card').css('height', '');
		}
	}

	/**
	 * Initialize stage padding carousel
	 */
	function initStagePaddingCarousel() {
		// Exclude grids that have their own carousels
		const $carousel = $('.js-stage-padding').not('.latest-news-grid, .related-products-grid, .logo-showcase-grid, .card-grid-carousel');

		if (!$carousel.length) {
			return;
		}

		if (window.innerWidth < 768) {
			if (!$carousel.hasClass('slick-initialized')) {
				$carousel.slick({
					dots: false,
					arrows: false,
					infinite: true,
					speed: 300,
					slidesToShow: 1,
					slidesToScroll: 1,
					adaptiveHeight: false,
					onSetPosition: function() {
						setEqualHeight();
					}
				});

				// Call setEqualHeight after initialization
				setTimeout(setEqualHeight, 100);
			}
		} else {
			if ($carousel.hasClass('slick-initialized')) {
				$carousel.slick('unslick');
				// Reset heights when unslicking
				$('.js-stage-padding .card, .js-stage-padding .icon-card, .js-stage-padding .product-card').css('height', '');
			}
		}
	}

	// Initialize on load with delay to ensure DOM is ready
	setTimeout(initStagePaddingCarousel, 100);

	// Re-initialize on resize
	let carouselResizeTimer;
	$(window).on('resize', function() {
		clearTimeout(carouselResizeTimer);
		carouselResizeTimer = setTimeout(initStagePaddingCarousel, 250);
	});
});

// Logo showcase carousel with custom arrows (mobile only)
jQuery(document).ready(function($) {
	function initLogoShowcaseCarousel() {
		$('.logo-showcase-section').each(function() {
			const $section = $(this);
			const $carousel = $section.find('.logo-showcase-grid');
			const $arrowContainer = $section.find('.epaton-slick-arrow-container');
			const slideCount = $carousel.children('.logo-showcase-item').length;

			if (!$carousel.length) {
				return;
			}

			if (window.innerWidth < 768 && slideCount > 1) {
				$arrowContainer.addClass('show');
				$carousel.addClass('stagePaddingRight itemMargin');

				if (!$carousel.hasClass('slick-initialized')) {
					$carousel.slick({
						dots: false,
						arrows: true,
						infinite: true,
						speed: 300,
						slidesToShow: 1,
						slidesToScroll: 1,
						adaptiveHeight: false,
						prevArrow: $section.find('.epaton-slick-prev'),
						nextArrow: $section.find('.epaton-slick-next')
					});
				}
			} else {
				$arrowContainer.removeClass('show');
				$carousel.removeClass('stagePaddingRight itemMargin');

				if ($carousel.hasClass('slick-initialized')) {
					$carousel.slick('unslick');
				}
			}
		});
	}

	setTimeout(initLogoShowcaseCarousel, 100);

	let logoShowcaseResizeTimer;
	$(window).on('resize', function() {
		clearTimeout(logoShowcaseResizeTimer);
		logoShowcaseResizeTimer = setTimeout(initLogoShowcaseCarousel, 250);
	});
});



// Video autoplay on scroll
document.addEventListener('DOMContentLoaded', function () {
    const videoContainers = document.querySelectorAll('.autoplay-video');

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                const video = entry.target.querySelector('video');
                if (!video) {
                    return;
                }

                if (entry.isIntersecting) {
                    video.currentTime = 0;
                    video.play().catch((error) => {
                        console.error('Video autoplay failed:', error);
                    });
                } else {
                    video.pause();
                }
            });
        },
        {
            threshold: 0.5,
        }
    );

    videoContainers.forEach((container) => {
        observer.observe(container);
    });
});

/**
 * Smooth Scroll to Anchor with Header Offset
 *
 * Handles smooth scrolling to anchor links (e.g., #product-enquiry-form)
 * with offset for sticky header to prevent content hiding behind header.
 */
jQuery(document).ready(function($) {
	// Handle all anchor links that start with #
	$('a[href^="#"]').on('click', function(e) {
		const href = $(this).attr('href');

		// Skip if it's just "#" or empty
		if (!href || href === '#' || href === '#!') {
			return;
		}

		// Find the target element
		const target = $(href);

		// If target exists, scroll to it
		if (target.length) {
			e.preventDefault();

			// Get header height for offset
			const headerHeight = $('.site-header').outerHeight() || 0;
			const offset = 20; // Extra spacing
			const targetPosition = target.offset().top - headerHeight - offset;

			// Smooth scroll
			$('html, body').animate({
				scrollTop: targetPosition
			}, 800, 'swing');
		}
	});
});

/**
 * Team Section Navigation
 *
 * Scrolls between team grid groups without changing pages.
 */
jQuery(document).ready(function($) {
	const $navContainers = $('.team-section-nav-container');

	if (!$navContainers.length) {
		return;
	}

	$navContainers.each(function() {
		const $container = $(this);
		const $nav = $container.find('.team-section-nav');
		const $buttons = $nav.find('.team-section-nav-button');
		const $scrollbarTrack = $container.find('.team-section-scrollbar-track');
		const $scrollbarThumb = $container.find('.team-section-scrollbar-thumb');
		let isDown = false;
		let startX = 0;
		let scrollLeft = 0;
		let hasMoved = false;
		const dragThreshold = 5;

		if (!$nav.length || !$buttons.length) {
			return;
		}

		function getHeaderOffset() {
			return ($('.site-header').outerHeight() || 0) + 20;
		}

		function updateScrollbar() {
			const nav = $nav[0];

			if (!nav || !$scrollbarTrack.length || !$scrollbarThumb.length) {
				return;
			}

			const maxScroll = nav.scrollWidth - nav.clientWidth;

			if (maxScroll <= 0) {
				$scrollbarTrack.removeClass('show');
				return;
			}

			const scrollProgress = nav.scrollLeft / maxScroll;
			const visiblePercent = (nav.clientWidth / nav.scrollWidth) * 100;
			const progressPercent = visiblePercent + (scrollProgress * (100 - visiblePercent));

			$scrollbarTrack.addClass('show');
			$scrollbarThumb.css('width', progressPercent + '%');
		}

		function setActiveButton($button) {
			$buttons.removeClass('active').attr('aria-pressed', 'false');
			$button.addClass('active').attr('aria-pressed', 'true');

			if ($button.length) {
				const nav = $nav[0];
				const button = $button[0];
				const buttonLeft = button.offsetLeft;
				const buttonRight = buttonLeft + button.offsetWidth;
				const visibleLeft = nav.scrollLeft;
				const visibleRight = visibleLeft + nav.clientWidth;

				if (buttonLeft < visibleLeft || buttonRight > visibleRight) {
					nav.scrollTo({
						left: buttonLeft - 20,
						behavior: 'smooth'
					});
				}
			}
		}

		function updateActiveFromScroll() {
			const scrollPosition = $(window).scrollTop() + getHeaderOffset() + 10;
			let $currentButton = $buttons.first();

			$buttons.each(function() {
				const $button = $(this);
				const targetSelector = $button.data('team-target');
				const $target = $(targetSelector);

				if ($target.length && $target.offset().top <= scrollPosition) {
					$currentButton = $button;
				}
			});

			setActiveButton($currentButton);
		}

		$buttons.on('click', function(e) {
			const $button = $(this);
			const targetSelector = $button.data('team-target');
			const $target = $(targetSelector);

			if (hasMoved) {
				e.preventDefault();
				return;
			}

			if (!$target.length) {
				return;
			}

			setActiveButton($button);

			$('html, body').animate({
				scrollTop: $target.offset().top - getHeaderOffset()
			}, 700, 'swing');
		});

		$nav.on('mousedown', function(e) {
			isDown = true;
			hasMoved = false;
			$nav.addClass('dragging');
			startX = e.pageX - $nav.offset().left;
			scrollLeft = $nav.scrollLeft();
		});

		$(document).on('mouseup.teamSectionNav', function() {
			if (!isDown) {
				return;
			}

			isDown = false;
			$nav.removeClass('dragging');

			setTimeout(function() {
				hasMoved = false;
			}, 50);
		});

		$nav.on('mouseleave', function() {
			if (isDown) {
				isDown = false;
				$nav.removeClass('dragging');
			}
		});

		$nav.on('mousemove', function(e) {
			if (!isDown) {
				return;
			}

			const x = e.pageX - $nav.offset().left;
			const distance = Math.abs(x - startX);

			if (distance > dragThreshold) {
				e.preventDefault();
				hasMoved = true;
				$nav.scrollLeft(scrollLeft - ((x - startX) * 1.5));
			}
		});

		$nav.on('scroll', updateScrollbar);
		$(window).on('scroll.teamSectionNav', updateActiveFromScroll);
		$(window).on('resize.teamSectionNav', function() {
			clearTimeout(window.teamSectionNavScrollbarTimeout);
			window.teamSectionNavScrollbarTimeout = setTimeout(function() {
				updateScrollbar();
				updateActiveFromScroll();
			}, 100);
		});

		setTimeout(updateScrollbar, 100);
		setTimeout(updateScrollbar, 500);
		updateActiveFromScroll();
	});
});

/**
 * Accordion Section
 *
 * Opens one accordion item at a time.
 */
jQuery(document).ready(function($) {
	const $accordions = $('.accordion-section');

	if (!$accordions.length) {
		return;
	}

	$accordions.each(function() {
		const $accordion = $(this);
		const $items = $accordion.find('.accordion-item');

		$items.each(function() {
			const $item = $(this);
			const $content = $item.find('.accordion-item-content');
			const isActive = $item.hasClass('active');

			$content.toggle(isActive).prop('hidden', !isActive);
		});

		$accordion.on('click', '.accordion-item-title', function() {
			const $button = $(this);
			const $item = $button.closest('.accordion-item');
			const $content = $item.find('.accordion-item-content');
			const isActive = $item.hasClass('active');

			if (isActive) {
				$item.removeClass('active');
				$button.attr('aria-expanded', 'false');
				$content.slideUp(250, function() {
					$content.prop('hidden', true);
				});
				return;
			}

			$items.removeClass('active');
			$items.find('.accordion-item-title').attr('aria-expanded', 'false');
			$items.find('.accordion-item-content').slideUp(250, function() {
				$(this).prop('hidden', true);
			});

			$item.addClass('active');
			$button.attr('aria-expanded', 'true');
			$content.prop('hidden', false).stop(true, true).slideDown(250);
		});
	});
});

/**
 * Resource Category Filter Active States
 *
 * Adds 'active' class to resource filter links based on current page:
 * - Main resource page: "View all" link gets active class
 * - Archive page: Current category link gets active class
 * - Auto-scrolls to filter section on taxonomy pages
 * - Handles click events to prevent multiple clicks
 */
jQuery(document).ready(function($) {
	const filterContainer = $('.resource-category-filters');

	// Only run if filter container exists
	if (!filterContainer.length) {
		return;
	}

	const currentUrl = window.location.href;
	const filterLinks = filterContainer.find('.resource-filter-link');
	let isArchivePage = false;

	// Remove any existing active classes first
	filterLinks.removeClass('active');

	// Check each link
	filterLinks.each(function() {
		const linkUrl = $(this).attr('href');

		// Match current URL with link URL
		if (currentUrl === linkUrl || currentUrl === linkUrl + '/') {
			$(this).addClass('active');
			// If not the first link (not "View all"), it's an archive page
			if ($(this).index() > 0) {
				isArchivePage = true;
			}
		}
	});

	// Fallback: If no link is active, activate "View all" (first link)
	if (!filterContainer.find('.resource-filter-link.active').length) {
		filterContainer.find('.resource-filter-link').first().addClass('active');
	}

	// Handle click events on filter links
	filterLinks.on('click', function(e) {
		const $link = $(this);
		const targetUrl = $link.attr('href');

		// Prevent multiple clicks
		if ($link.hasClass('is-loading')) {
			e.preventDefault();
			return false;
		}

		// Add loading state
		$link.addClass('is-loading');

		// Remove active from all links
		filterLinks.removeClass('active');

		// Add active to clicked link
		$link.addClass('active');

		// Navigate to the URL
		window.location.href = targetUrl;

		// Prevent default to ensure our navigation happens
		e.preventDefault();
		return false;
	});

	// Auto-scroll to filter section on taxonomy/archive pages
	if (isArchivePage) {
		// Wait for page to fully load
		$(window).on('load', function() {
			// Small delay to ensure all content is rendered
			setTimeout(function() {
				const headerOffset = $('.site-header').outerHeight() || 0;
				const filterOffset = filterContainer.offset().top - headerOffset - 20; // 20px extra spacing

				$('html, body').animate({
					scrollTop: filterOffset
				}, 600, 'swing'); // 600ms smooth scroll
			}, 100);
		});
	}
});
