/**
 * Mobile navigation interactions.
 *
 * @package epaton
 */

(function() {
	'use strict';

	const trigger = document.querySelector('.menu-trigger');
	const overlay = document.querySelector('.hamburger-overlay');
	const wrapper = document.querySelector('.hamburger-wrapper');
	const mobileMenu = document.querySelector('.mobile-menu');

	if (!trigger || !overlay || !wrapper || !mobileMenu) {
		return;
	}

	function slideDown(element) {
		element.hidden = false;
		element.style.display = 'block';
		element.style.overflow = 'hidden';
		element.style.height = '0';
		element.style.opacity = '0';

		const targetHeight = element.scrollHeight;
		element.offsetHeight;
		element.style.transition = 'height 300ms ease, opacity 220ms ease';
		element.style.height = targetHeight + 'px';
		element.style.opacity = '1';

		element.addEventListener('transitionend', function cleanup(event) {
			if (event.propertyName !== 'height') {
				return;
			}
			element.style.height = 'auto';
			element.style.overflow = '';
			element.style.transition = '';
			element.removeEventListener('transitionend', cleanup);
		});
	}

	function slideUp(element) {
		element.style.overflow = 'hidden';
		element.style.height = element.scrollHeight + 'px';
		element.style.opacity = '1';
		element.offsetHeight;
		element.style.transition = 'height 300ms ease, opacity 220ms ease';
		element.style.height = '0';
		element.style.opacity = '0';

		element.addEventListener('transitionend', function cleanup(event) {
			if (event.propertyName !== 'height') {
				return;
			}
			element.hidden = true;
			element.style.display = 'none';
			element.style.height = '';
			element.style.opacity = '';
			element.style.overflow = '';
			element.style.transition = '';
			element.removeEventListener('transitionend', cleanup);
		});
	}

	function closeMenu(resetSubmenus) {
		trigger.classList.remove('isOpen');
		overlay.classList.remove('isOpen');
		wrapper.classList.remove('isOpen');
		document.body.classList.remove('no-scroll');
		trigger.setAttribute('aria-expanded', 'false');

		if (resetSubmenus) {
			mobileMenu.querySelectorAll('.submenu-open').forEach(function(item) {
				item.classList.remove('submenu-open');
				const submenu = item.querySelector(':scope > .sub-menu');
				if (submenu) {
					submenu.hidden = true;
					submenu.removeAttribute('style');
				}
			});
		}
	}

	function openMenu() {
		trigger.classList.add('isOpen');
		overlay.classList.add('isOpen');
		wrapper.classList.add('isOpen');
		document.body.classList.add('no-scroll');
		trigger.setAttribute('aria-expanded', 'true');
	}

	mobileMenu.querySelectorAll('.menu-item-has-children > a').forEach(function(link) {
		if (link.nextElementSibling && link.nextElementSibling.classList.contains('submenu-toggle')) {
			return;
		}

		const button = document.createElement('button');
		button.type = 'button';
		button.className = 'submenu-toggle';
		button.setAttribute('aria-label', 'Toggle submenu');
		button.setAttribute('aria-expanded', 'false');
		button.innerHTML = '<span></span>';
		link.after(button);

		const submenu = link.parentElement.querySelector(':scope > .sub-menu');
		if (submenu) {
			submenu.hidden = true;
		}
	});

	trigger.setAttribute('aria-expanded', 'false');
	trigger.addEventListener('click', function() {
		wrapper.classList.contains('isOpen') ? closeMenu(false) : openMenu();
	});
	overlay.addEventListener('click', function() { closeMenu(false); });

	mobileMenu.addEventListener('click', function(event) {
		const button = event.target.closest('.submenu-toggle');
		if (!button) {
			return;
		}

		const item = button.closest('.menu-item-has-children');
		const submenu = item.querySelector(':scope > .sub-menu');
		const willOpen = !item.classList.contains('submenu-open');

		item.parentElement.querySelectorAll(':scope > .menu-item-has-children.submenu-open').forEach(function(sibling) {
			if (sibling === item) {
				return;
			}
			sibling.classList.remove('submenu-open');
			const siblingButton = sibling.querySelector(':scope > .submenu-toggle');
			const siblingMenu = sibling.querySelector(':scope > .sub-menu');
			if (siblingButton) siblingButton.setAttribute('aria-expanded', 'false');
			if (siblingMenu) slideUp(siblingMenu);
		});

		item.classList.toggle('submenu-open', willOpen);
		button.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
		if (submenu) {
			willOpen ? slideDown(submenu) : slideUp(submenu);
		}
	});

	document.addEventListener('keydown', function(event) {
		if (event.key === 'Escape' && wrapper.classList.contains('isOpen')) {
			closeMenu(false);
			trigger.focus();
		}
	});

	let resizeTimer;
	window.addEventListener('resize', function() {
		window.clearTimeout(resizeTimer);
		resizeTimer = window.setTimeout(function() {
			if (window.innerWidth > 1024 && wrapper.classList.contains('isOpen')) {
				closeMenu(true);
			}
		}, 250);
	}, { passive: true });
})();
