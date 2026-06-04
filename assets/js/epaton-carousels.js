jQuery(document).ready(function($) {
	/**
	 * Update Hero USP Height
	 * Calculate and apply the dynamic height of .hero-usp as CSS variable
	 */
	function updateHeroUspHeight() {
		const $heroUspRows = $('.hero-usp');

		if (!$heroUspRows.length) {
			return;
		}

		$heroUspRows.each(function() {
			const $heroUsps = $(this);
			const uspHeight = $heroUsps.outerHeight();

			$heroUsps.css('--hero-usp-height', uspHeight + 'px');
			$heroUsps.addClass('height-calculated');
		});
	}

	/**
	 * Update Hero USP Scroll Distance
	 * Measures the exact start of the cloned item set so the CSS animation loops without a visible snap.
	 */
	function updateHeroUspScrollDistance($heroUsps) {
		const $firstItem = $heroUsps.children('.hero-usp-item:not(.cloned)').first();
		const $firstClone = $heroUsps.children('.hero-usp-item.cloned').first();

		if (!$firstItem.length || !$firstClone.length) {
			$heroUsps.css('--hero-usp-scroll-distance', '-50%');
			return;
		}

		const scrollDistance = $firstClone.position().left - $firstItem.position().left;

		if (scrollDistance > 0) {
			$heroUsps.css('--hero-usp-scroll-distance', '-' + scrollDistance + 'px');
		}
	}

	/**
	 * Hero USPs Continuous Scroll
	 * Enable on screens below 1199px with CSS-based continuous scroll
	 */
	function initHeroUspsCarousel() {
		const $heroUspRows = $('.hero-usp');

		if (!$heroUspRows.length) {
			return;
		}

		const windowWidth = $(window).width();

		$heroUspRows.each(function() {
			const $heroUsps = $(this);

			if (windowWidth <= 1199) {
				if (!$heroUsps.hasClass('continuous-scroll-enabled')) {
					const $items = $heroUsps.children('.hero-usp-item:not(.cloned)');
					const itemCount = $items.length;

					if (itemCount > 0 && !$heroUsps.hasClass('items-cloned')) {
						$items.clone().addClass('cloned').appendTo($heroUsps);
						$heroUsps.addClass('items-cloned');
					}

					$heroUsps.addClass('continuous-scroll-enabled');
				}

				updateHeroUspScrollDistance($heroUsps);
				setTimeout(function() {
					updateHeroUspScrollDistance($heroUsps);
					updateHeroUspHeight();
				}, 50);
			} else {
				if ($heroUsps.hasClass('continuous-scroll-enabled')) {
					$heroUsps.removeClass('continuous-scroll-enabled');
					$heroUsps.find('.hero-usp-item.cloned').remove();
					$heroUsps.removeClass('items-cloned');
					$heroUsps.css('--hero-usp-scroll-distance', '');

					setTimeout(updateHeroUspHeight, 50);
				}
			}
		});
	}
	
	/**
	 * Latest News Grid Carousel
	 * Enable Slick carousel on screens below 1200px
	 */
	function initLatestNewsCarousel() {
		const $newsGrid = $('.latest-news-grid');
		const $arrowContainer = $('.latest-news-section .epaton-slick-arrow-container');

		if (!$newsGrid.length) {
			return;
		}

		const windowWidth = $(window).width();

		if (windowWidth <= 1199) {
			// Show arrow container
			$arrowContainer.addClass('show');

			// Add stagePaddingRight class for mobile
			if (windowWidth < 768) {
				$newsGrid.addClass('stagePaddingRight itemMargin');
			} else {
				$newsGrid.removeClass('stagePaddingRight itemMargin');
			}

			// Initialize Slick carousel
			if (!$newsGrid.hasClass('slick-initialized')) {
				$newsGrid.slick({
					dots: false,
					arrows: true,
					infinite: true,
					speed: 300,
					slidesToShow: 3,
					slidesToScroll: 1,
					adaptiveHeight: false,
					prevArrow: $('.latest-news-section .epaton-slick-prev'),
					nextArrow: $('.latest-news-section .epaton-slick-next'),
					responsive: [
						{
							breakpoint: 992,
							settings: {
								slidesToShow: 2,
								slidesToScroll: 1
							}
						},
						{
							breakpoint: 768,
							settings: {
								slidesToShow: 1,
								slidesToScroll: 1
							}
						}
					]
				});
			}
		} else {
			// Hide arrow container on desktop
			$arrowContainer.removeClass('show');

			// Remove stagePaddingRight class
			$newsGrid.removeClass('stagePaddingRight itemMargin');

			// Destroy carousel on desktop
			if ($newsGrid.hasClass('slick-initialized')) {
				$newsGrid.slick('unslick');
			}
		}
	}

	/**
	 * Set equal height for benefit cards in carousel
	 */
	function setBenefitCardEqualHeight() {
		const $benefitGrid = $('.benefit-card-grid');

		if (!$benefitGrid.hasClass('slick-initialized')) {
			return;
		}

		// Reset heights first
		$benefitGrid.find('.feature-benefit-card').css('height', '');
		$benefitGrid.find('.feature-benefit-title').css('min-height', '');

		let maxCardHeight = 0;
		let maxTitleHeight = 0;

		// Get maximum heights
		$benefitGrid.find('.slick-slide:not(.slick-cloned) .feature-benefit-card').each(function() {
			const cardHeight = $(this).outerHeight();
			maxCardHeight = Math.max(maxCardHeight, cardHeight);
		});

		$benefitGrid.find('.slick-slide:not(.slick-cloned) .feature-benefit-title').each(function() {
			const titleHeight = $(this).outerHeight();
			maxTitleHeight = Math.max(maxTitleHeight, titleHeight);
		});

		// Apply equal heights
		$benefitGrid.find('.feature-benefit-card').css('height', maxCardHeight + 'px');
		$benefitGrid.find('.feature-benefit-title').css('min-height', maxTitleHeight + 'px');
	}

	/**
	 * Related Products Grid Carousel
	 * Enable Slick carousel on screens below 992px
	 */
	function initRelatedProductsCarousel() {
		const $productsGrid = $('.related-products-grid');
		const $arrowContainer = $('.related-products-section .epaton-slick-arrow-container');

		if (!$productsGrid.length) {
			return;
		}

		const windowWidth = $(window).width();

		if (windowWidth <= 991) {
			// Show arrow container
			$arrowContainer.addClass('show');

			// Add stagePaddingRight class for mobile
			if (windowWidth < 768) {
				$productsGrid.addClass('stagePaddingRight itemMargin');
			} else {
				$productsGrid.removeClass('stagePaddingRight itemMargin');
			}

			// Initialize Slick carousel
			if (!$productsGrid.hasClass('slick-initialized')) {
				$productsGrid.slick({
					dots: false,
					arrows: true,
					infinite: true,
					speed: 300,
					slidesToShow: 2,
					slidesToScroll: 1,
					adaptiveHeight: false,
					prevArrow: $('.related-products-section .epaton-slick-prev'),
					nextArrow: $('.related-products-section .epaton-slick-next'),
					responsive: [
						{
							breakpoint: 768,
							settings: {
								slidesToShow: 1,
								slidesToScroll: 1
							}
						}
					]
				});
			}
		} else {
			// Hide arrow container on desktop
			$arrowContainer.removeClass('show');

			// Remove stagePaddingRight class
			$productsGrid.removeClass('stagePaddingRight itemMargin');

			// Destroy carousel on desktop
			if ($productsGrid.hasClass('slick-initialized')) {
				$productsGrid.slick('unslick');
			}
		}
	}

	/**
	 * Card Grid Carousel
	 * Enable when the Card Grid section carousel option is selected.
	 */
	function initCardGridCarousel() {
		$('.card-grid-section.has-card-carousel').each(function() {
			const $section = $(this);
			const $cardGrid = $section.find('.card-grid-carousel');
			const windowWidth = $(window).width();

			if (!$cardGrid.length) {
				return;
			}

			if (windowWidth <= 767) {
				$cardGrid.addClass('stagePaddingRight itemMargin');
			} else {
				$cardGrid.removeClass('stagePaddingRight itemMargin');
			}

			if ($cardGrid.hasClass('slick-initialized')) {
				$cardGrid.slick('slickSetOption', 'arrows', windowWidth > 767, true);
				return;
			}

			$cardGrid.slick({
				dots: false,
				arrows: true,
				infinite: true,
				speed: 300,
				slidesToShow: 4,
				slidesToScroll: 1,
				adaptiveHeight: false,
				prevArrow: $section.find('.epaton-slick-prev'),
				nextArrow: $section.find('.epaton-slick-next'),
				responsive: [
					{
						breakpoint: 1200,
						settings: {
							slidesToShow: 3,
							slidesToScroll: 1
						}
					},
					{
						breakpoint: 992,
						settings: {
							slidesToShow: 2,
							slidesToScroll: 1
						}
					},
					{
						breakpoint: 768,
						settings: {
							arrows: false,
							slidesToShow: 1,
							slidesToScroll: 1
						}
					}
				]
			});
		});
	}

	/**
	 * Image Gallery Carousel
	 * Initialize carousel if enabled (always active, no breakpoint)
	 */
	function initImageGalleryCarousel() {
		const $galleryCarousel = $('.image-gallery-carousel');

		if (!$galleryCarousel.length) {
			return;
		}

		const $arrowContainer = $('.image-gallery-section .epaton-slick-arrow-container');
		const windowWidth = $(window).width();

		// Destroy existing carousel if initialized
		if ($galleryCarousel.hasClass('slick-initialized')) {
			$galleryCarousel.slick('unslick');
		}

		// For screens ≤ 991px: Use custom arrow container
		if (windowWidth <= 991) {
			// Show custom arrow container
			$arrowContainer.addClass('show');

			// Add stagePaddingRight class for mobile ≤ 767px
			if (windowWidth < 768) {
				$galleryCarousel.addClass('stagePaddingRight itemMargin');
			} else {
				$galleryCarousel.removeClass('stagePaddingRight itemMargin');
			}

			// Initialize carousel with custom arrow container
			$galleryCarousel.slick({
				dots: false,
				arrows: true,
				infinite: true,
				speed: 300,
				slidesToShow: 2,
				slidesToScroll: 1,
				adaptiveHeight: false,
				prevArrow: $('.image-gallery-section .epaton-slick-prev'),
				nextArrow: $('.image-gallery-section .epaton-slick-next'),
				responsive: [
					{
						breakpoint: 768,
						settings: {
							slidesToShow: 1,
							slidesToScroll: 1
						}
					}
				]
			});
		} else {
			// For screens > 991px: Use default Slick arrows
			$arrowContainer.removeClass('show');
			$galleryCarousel.removeClass('stagePaddingRight itemMargin');

			// Initialize carousel with default arrows
			$galleryCarousel.slick({
				dots: false,
				arrows: true,
				infinite: true,
				speed: 300,
				slidesToShow: 3,
				slidesToScroll: 1,
				adaptiveHeight: false,
				prevArrow: '<button class="slick-prev epaton-slick-arrow" aria-label="Previous">' + epatonCarousel.arrowLeft + '</button>',
				nextArrow: '<button class="slick-next epaton-slick-arrow" aria-label="Next">' + epatonCarousel.arrowRight + '</button>',
				responsive: [
					{
						breakpoint: 1200,
						settings: {
							slidesToShow: 2,
							slidesToScroll: 1
						}
					}
				]
			});
		}
	}

	/**
	 * Benefit Card Grid Carousel
	 * Enable Slick carousel on screens below 1600px
	 */
	function initBenefitCardCarousel() {
		const $benefitGrid = $('.benefit-card-grid');
		const $arrowContainer = $('.features-benefits-section .epaton-slick-arrow-container');

		if (!$benefitGrid.length) {
			return;
		}

		const windowWidth = $(window).width();

		if (windowWidth <= 1599) {
			// Show arrow container
			$arrowContainer.addClass('show');

			// Add stagePaddingRight class for mobile
			if (windowWidth < 768) {
				$benefitGrid.addClass('stagePaddingRight itemMargin');
			} else {
				$benefitGrid.removeClass('stagePaddingRight itemMargin');
			}

			// Initialize Slick carousel
			if (!$benefitGrid.hasClass('slick-initialized')) {
				$benefitGrid.slick({
					dots: false,
					arrows: true,
					infinite: true,
					speed: 300,
					slidesToShow: 4,
					slidesToScroll: 1,
					adaptiveHeight: false,
					prevArrow: $('.features-benefits-section .epaton-slick-prev'),
					nextArrow: $('.features-benefits-section .epaton-slick-next'),
					responsive: [
						{
							breakpoint: 1200,
							settings: {
								slidesToShow: 3,
								slidesToScroll: 1
							}
						},
						{
							breakpoint: 992,
							settings: {
								slidesToShow: 2,
								slidesToScroll: 1
							}
						},
						{
							breakpoint: 768,
							settings: {
								slidesToShow: 1,
								slidesToScroll: 1
							}
						}
					]
				});

				// Set equal heights after initialization
				setTimeout(setBenefitCardEqualHeight, 100);
			}
		} else {
			// Hide arrow container on desktop
			$arrowContainer.removeClass('show');

			// Remove stagePaddingRight class
			$benefitGrid.removeClass('stagePaddingRight itemMargin');

			// Destroy carousel on desktop
			if ($benefitGrid.hasClass('slick-initialized')) {
				$benefitGrid.slick('unslick');
				// Reset inline heights when destroying
				$benefitGrid.find('.feature-benefit-card').css('height', '');
				$benefitGrid.find('.feature-benefit-title').css('min-height', '');
			}
		}
	}

	/**
	 * Product Feedback Carousel
	 * Initialize carousel with fade effect and 1 item
	 */
	function initFeedbackCarousel() {
		const $feedbackCarousel = $('.feedback-carousel');

		if (!$feedbackCarousel.length) {
			return;
		}

		// Only initialize if more than 1 feedback item
		if ($feedbackCarousel.children().length <= 1) {
			return;
		}

		const $arrowContainer = $('.product-feedback-section .epaton-slick-arrow-container');
		const windowWidth = $(window).width();

		// Destroy existing carousel if initialized
		if ($feedbackCarousel.hasClass('slick-initialized')) {
			$feedbackCarousel.slick('unslick');
		}

		// For screens ≤ 991px: Use custom arrow container
		if (windowWidth <= 991) {
			// Show custom arrow container
			$arrowContainer.addClass('show');

			// Initialize carousel with custom arrow container
			$feedbackCarousel.slick({
				dots: false,
				arrows: true,
				infinite: true,
				speed: 500,
				fade: true,
				cssEase: 'linear',
				slidesToShow: 1,
				slidesToScroll: 1,
				adaptiveHeight: true,
				prevArrow: $('.product-feedback-section .epaton-slick-prev'),
				nextArrow: $('.product-feedback-section .epaton-slick-next')
			});
		} else {
			// For screens > 991px: Use default Slick arrows (left/right positioned)
			$arrowContainer.removeClass('show');

			// Initialize carousel with default arrows
			$feedbackCarousel.slick({
				dots: false,
				arrows: true,
				infinite: true,
				speed: 500,
				fade: true,
				cssEase: 'linear',
				slidesToShow: 1,
				slidesToScroll: 1,
				adaptiveHeight: true,
				prevArrow: '<button class="slick-prev epaton-slick-arrow" aria-label="Previous">' + epatonCarousel.arrowLeft + '</button>',
				nextArrow: '<button class="slick-next epaton-slick-arrow" aria-label="Next">' + epatonCarousel.arrowRight + '</button>'
			});
		}
	}

	/**
	 * Initialize all carousels
	 */
	function initAllCarousels() {
		initHeroUspsCarousel();
		initLatestNewsCarousel();
		initBenefitCardCarousel();
		initRelatedProductsCarousel();
		initCardGridCarousel();
		initImageGalleryCarousel();
		initFeedbackCarousel();
		updateHeroUspHeight();
	}

	// Initialize on load
	initAllCarousels();

	/**
	 * Re-initialize carousels on window resize
	 * Uses debounce to prevent excessive calls
	 */
	let resizeTimer;
	$(window).on('resize', function() {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(function() {
			// Re-init carousels that have breakpoint conditions
			initHeroUspsCarousel();
			initLatestNewsCarousel();
			initBenefitCardCarousel();
			initRelatedProductsCarousel();
			initCardGridCarousel();
			initImageGalleryCarousel();
			initFeedbackCarousel();
			// Update height after carousel changes
			updateHeroUspHeight();
			// Update benefit card equal heights after resize
			setTimeout(setBenefitCardEqualHeight, 100);
		}, 250);
	});

	/**
	 * Update height after fonts load
	 * Font loading can change content height
	 */
	$(window).on('load', function() {
		setTimeout(updateHeroUspHeight, 200);
		setTimeout(setBenefitCardEqualHeight, 200);
	});

	/**
	 * Resource Category Filters - Draggable Scroll with Progress Bar
	 * Enables mouse drag and touch scroll for filter items
	 * Shows scroll progress as a filled bar (like the example image)
	 */
	function initResourceFiltersDraggable() {
		const $filters = $('.resource-category-filters');
		const $scrollbarThumb = $('.resource-scrollbar-thumb');
		const $scrollbarTrack = $('.resource-scrollbar-track');

		console.log('Resource Filters Init:', {
			filters: $filters.length,
			thumb: $scrollbarThumb.length,
			track: $scrollbarTrack.length
		});

		if (!$filters.length) {
			console.warn('No resource filters found!');
			return;
		}

		// Update progress bar based on scroll position
		function updateScrollbar() {
			if (!$filters[0] || !$scrollbarThumb.length || !$scrollbarTrack.length) {
				console.warn('Missing elements for scrollbar update');
				return;
			}

			const scrollWidth = $filters[0].scrollWidth;
			const clientWidth = $filters[0].clientWidth;
			const scrollLeft = $filters[0].scrollLeft;
			const maxScroll = scrollWidth - clientWidth;

			// Check if there's overflow (scrolling is needed)
			const hasOverflow = maxScroll > 0;

			if (!hasOverflow) {
				// No scrolling needed - hide the scrollbar
				$scrollbarTrack.removeClass('show');
				console.log('No overflow - hiding scrollbar');
				return;
			}

			// Show the scrollbar track
			$scrollbarTrack.addClass('show');

			// Calculate scroll progress (0 to 1)
			const scrollProgress = scrollLeft / maxScroll;

			// Calculate visible portion as percentage of total content
			const visiblePercent = (clientWidth / scrollWidth) * 100;

			// Progress bar width = visible portion + (scrolled portion)
			const progressPercent = visiblePercent + (scrollProgress * (100 - visiblePercent));

			console.log('Progress Bar Update:', {
				scrollWidth,
				clientWidth,
				scrollLeft,
				maxScroll,
				hasOverflow,
				scrollProgress: (scrollProgress * 100).toFixed(1) + '%',
				visiblePercent: visiblePercent.toFixed(1) + '%',
				progressPercent: progressPercent.toFixed(1) + '%'
			});

			// Set the width of the thumb to show progress
			$scrollbarThumb.css({
				width: progressPercent + '%'
			});
		}

		// Mouse drag functionality
		let isDown = false;
		let startX;
		let scrollLeft;
		let hasMoved = false;
		let dragThreshold = 5; // Minimum pixels to consider it a drag

		$filters.on('mousedown', function(e) {
			// Don't interfere with link clicks
			if ($(e.target).is('a') || $(e.target).closest('a').length) {
				return;
			}

			isDown = true;
			hasMoved = false;
			$filters.addClass('dragging');
			startX = e.pageX - $filters.offset().left;
			scrollLeft = $filters.scrollLeft();
		});

		$(document).on('mouseup.resourceFilters', function() {
			if (isDown) {
				isDown = false;
				// Reset dragging state immediately
				$filters.removeClass('dragging');
				// Reset hasMoved after a short delay to allow click event to check it
				setTimeout(function() {
					hasMoved = false;
				}, 50);
			}
		});

		$filters.on('mouseleave', function() {
			if (isDown) {
				isDown = false;
				$filters.removeClass('dragging');
			}
		});

		$filters.on('mousemove', function(e) {
			if (!isDown) return;

			const x = e.pageX - $filters.offset().left;
			const distance = Math.abs(x - startX);

			// Only consider it a drag if moved more than threshold
			if (distance > dragThreshold) {
				e.preventDefault();
				hasMoved = true;
				const walk = (x - startX) * 1.5; // Scroll speed multiplier
				$filters.scrollLeft(scrollLeft - walk);
			}
		});

		// Prevent click on links when dragging
		$filters.on('click', 'a', function(e) {
			if (hasMoved) {
				console.log('Click prevented - drag detected');
				e.preventDefault();
				e.stopPropagation();
				return false;
			}
			console.log('Click allowed - no drag');
		});

		// Update scrollbar on scroll (real-time progress)
		$filters.on('scroll', updateScrollbar);

		// Update scrollbar on window resize
		$(window).on('resize', function() {
			clearTimeout(window.resourceScrollbarTimeout);
			window.resourceScrollbarTimeout = setTimeout(updateScrollbar, 100);
		});

		// Initial update
		setTimeout(updateScrollbar, 100);
		setTimeout(updateScrollbar, 500);
	}

	// Initialize draggable filters
	initResourceFiltersDraggable();

});
