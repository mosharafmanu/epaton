/**
 * Core front-end interactions.
 *
 * @package epaton
 */

(function() {
	'use strict';

	const header = document.querySelector('.site-header');

	if (!header) {
		return;
	}

	function updateHeaderOffset() {
		const isStatic = header.classList.contains('is-static');
		const topGap = isStatic ? 0 : (window.innerWidth <= 767 ? 30 : 60);
		document.documentElement.style.setProperty('--header-offset', (header.offsetHeight + topGap) + 'px');
	}

	function updateHeaderScrollState() {
		if (header.classList.contains('is-static')) {
			return;
		}

		const stickyThreshold = window.innerWidth <= 767 ? 30 : 60;
		header.classList.toggle('is-scrolled', window.scrollY >= stickyThreshold);
	}

	let resizeTimer;
	let scrollFramePending = false;
	window.addEventListener('resize', function() {
		window.clearTimeout(resizeTimer);
		resizeTimer = window.setTimeout(function() {
			updateHeaderOffset();
			updateHeaderScrollState();
		}, 100);
	}, { passive: true });

	window.addEventListener('scroll', function() {
		if (scrollFramePending) {
			return;
		}

		scrollFramePending = true;
		window.requestAnimationFrame(function() {
			updateHeaderScrollState();
			scrollFramePending = false;
		});
	}, { passive: true });
	window.addEventListener('load', updateHeaderOffset, { once: true });

	document.addEventListener('click', function(event) {
		const link = event.target.closest('a[href^="#"]');
		if (!link) {
			return;
		}

		const href = link.getAttribute('href');
		if (!href || href === '#' || href === '#!') {
			return;
		}

		const target = document.querySelector(href);
		if (!target) {
			return;
		}

		event.preventDefault();
		const top = target.getBoundingClientRect().top + window.scrollY - header.offsetHeight - 20;
		window.scrollTo({ top: top, behavior: 'smooth' });
	});

	updateHeaderOffset();
	updateHeaderScrollState();
})();
